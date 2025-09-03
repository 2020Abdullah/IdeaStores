<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerDue;
use App\Models\CustomerInvoices;
use App\Models\Exponse;
use App\Models\ExternalDebts;
use App\Models\Project;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Supplier_invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(){
        $tz = new \DateTimeZone(config('app.timezone'));
        $now = new \DateTime('now', $tz);
    
        $backupDay = 4; 
    
        if ((int)$now->format('w') === $backupDay) {
            // تأكد إن النسخة ما اتعملتش اليوم
            if (!cache()->has('backup_done_' . $now->format('Y-m-d'))) {
                $this->runBackup();
                cache(['backup_done_' . $now->format('Y-m-d') => true], now()->endOfDay());
            }
        }

        $data['suppliersCount'] = Supplier::count();
        $data['invoicesCount'] = Supplier_invoice::count();
        $data['customerCount'] = Customer::count();
        $data['customerInvoiceCount'] = CustomerInvoices::count();
        $data['salesCount'] = CustomerInvoices::sum('total_amount');
        $data['profitCount'] = CustomerInvoices::sum('total_profit');
        $data['receivables'] = CustomerDue::sum('amount') - CustomerDue::sum('paid_amount');
        $data['payables'] = ExternalDebts::sum('amount') - ExternalDebts::sum('paid');
        $data['totalExpenses'] = Exponse::sum('amount');
        return view('dashboard', $data);
    }

    protected function runBackup()
    {
        $db = config('database.connections.mysql');

        $backupFile = storage_path('app/backup_' . date('Y-m-d_H-i-s') . '.sql');

        // مسار mysqldump
        $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';

        if(!file_exists($mysqldumpPath)){
            $mysqldumpPath = 'D:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe';
        }

        $command = "\"$mysqldumpPath\" --user={$db['username']} --password=\"{$db['password']}\" --host={$db['host']} {$db['database']} > \"$backupFile\"";

        exec($command);
    }

    public function salesChart(Request $request)
    {
        try {
            $start = $request->input('start');
            $end = $request->input('end');
        
            $query = DB::table('customer_invoices')
                ->selectRaw('DATE(date) as day, SUM(total_amount) as total_sales, SUM(total_profit) as total_profit')
                ->groupBy('day')
                ->orderBy('day', 'ASC');
        
            if ($start && $end) {
                $query->whereBetween('date', [$start, $end]);
            }
        
            $salesData = $query->get();
        
            $chartData = $salesData->map(function($item) {
                $profitRatio = $item->total_sales > 0 ? ($item->total_profit / $item->total_sales) * 100 : 0;
                return [
                    'day' => $item->day,
                    'total_sales' => $item->total_sales,
                    'net_profit' => $item->total_profit,
                    'profit_ratio' => round($profitRatio, 2),
                ];
            });
        
            return response()->json($chartData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    
    public function profitLossChart(Request $request)
    {
        $start = $request->start ?? null;
        $end = $request->end ?? null;

        // مساعد تطبيق فلتر التاريخ
        $applyDate = function($table) use ($start, $end) {
            $q = DB::table($table);
            return ($start && $end) ? $q->whereBetween('date', [$start, $end]) : $q;
        };

        // 1) نجمع القيم الأساسية (نتحقق من اسماء الجداول الشائعة)
        $revenue = (float) $applyDate('customer_invoices')->sum('total_amount');

        $purchases = Schema::hasTable('supplier_invoices')
            ? (float) $applyDate('supplier_invoices')->sum('total_amount')
            : 0.0;

        $expenses = Schema::hasTable('exponses') // غيّر ل 'expenses' لو عندك الاسم الصحيح
            ? (float) $applyDate('exponses')->sum('amount')
            : (Schema::hasTable('expenses') ? (float) $applyDate('expenses')->sum('amount') : 0.0);

        $externalDebts = Schema::hasTable('external_debts')
            ? (float) $applyDate('external_debts')->sum('amount')
            : 0.0;

        // 2) المستحقات (receivables) — نحاول حسابها من جدول receivables أو من customer_invoices (total - paid)
        $receivables = 0.0;
        if (Schema::hasTable('receivables')) {
            $receivables = (float) $applyDate('receivables')->sum('amount');
        } else {
            // fallback: إذا الفواتير فيها حقل paid_amount نحسب المبلغ المتبقي
            if (Schema::hasTable('customer_invoices') && Schema::hasColumn('customer_invoices', 'paid_amount')) {
                $receivables = (float) $applyDate('customer_invoices')->selectRaw('SUM(total_amount - COALESCE(paid_amount,0)) as due')->value('due');
            } else {
                $receivables = 0.0; // لا توجد معلومة كافية
            }
        }

        // 3) صافي الربح من حقل total_profit إن كان موجودًا
        $netProfitByField = (Schema::hasColumn('customer_invoices', 'total_profit'))
            ? (float) $applyDate('customer_invoices')->sum('total_profit')
            : null;

        // 4) حسابات سريعة احتياطية
        $netProfit_calc_purchases = $revenue - ($purchases + $expenses); // الربح حسب المشتريات + المصروفات
        $netProfit_calc_externalDebts = $revenue - ($externalDebts + $expenses); // بديل إن اعتبرنا الديون كتكلفة قصيرة الأجل

        // نختار التمثيل المستخدم للـ "صافي الربح" في الdashboard:
        $netProfit = $netProfitByField !== null ? $netProfitByField : $netProfit_calc_purchases;

        // مؤشرات
        $totalCosts = $purchases + $expenses;
        $netMarginPercent = $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : null;
        $coverageRatio = $totalCosts > 0 ? round(($revenue / $totalCosts), 2) : null;
        $liquidityProxy = round($receivables - $externalDebts, 2); // موجبة يعني مستحقات تغطي الديون الخارجية تقريبا

        // رسالة موقف مبسطة
        if ($netProfit > 0) $positionText = "كسب";
        elseif ($netProfit < 0) $positionText = "خسارة";
        else $positionText = "متوازن";

        // بيانات للرسم:
        $barData = [
            'labels' => ['المبيعات','المشتريات','المصروفات','الديون الخارجية','المستحقات','صافي الربح'],
            'datasets' => [[
                'label' => 'قيمة (جنيه)',
                'data' => [
                    round($revenue,2),
                    round($purchases,2),
                    round($expenses,2),
                    round($externalDebts,2),
                    round($receivables,2),
                    round($netProfit,2),
                ]
            ]]
        ];

        $pieData = [
            'labels' => ['صافي الربح','التكاليف الإجمالية'],
            'datasets' => [[
                'data' => [ max($netProfit,0), max($totalCosts,0) ]
            ]]
        ];

        $summary = [
            'period' => ($start && $end) ? "$start إلى $end" : 'حتى الآن',
            'revenue' => round($revenue,2),
            'purchases' => round($purchases,2),
            'expenses' => round($expenses,2),
            'external_debts' => round($externalDebts,2),
            'receivables' => round($receivables,2),
            'total_costs' => round($totalCosts,2),
            'net_profit' => round($netProfit,2),
            'net_profit_by_field' => $netProfitByField !== null ? round($netProfitByField,2) : null,
            'net_margin_percent' => $netMarginPercent,
            'coverage_ratio' => $coverageRatio,
            'liquidity_proxy' => $liquidityProxy,
            'position' => $positionText,
        ];

        return response()->json([
            'summary' => $summary,
            'bar_chart' => $barData,
            'pie_chart' => $pieData,
        ]);
    }
}
