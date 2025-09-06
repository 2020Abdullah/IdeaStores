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
            $user = auth()->user();

            // السماح بالوصول فقط للـ Admin
            if ($user->type != 1) {
                return response()->json([
                    'error' => 'غير مسموح بعرض الإحصائيات البيانية',
                ], 403);
            }
            $start = $request->input('start');
            $end = $request->input('end');
    
            // صافي الربح من فواتير العملاء
            $profitQuery = DB::table('customer_invoices')
                ->selectRaw('DATE(date) as day, SUM(total_profit) as total_profit')
                ->groupBy('day')
                ->orderBy('day', 'ASC');
    
            if ($start && $end) {
                $profitQuery->whereBetween('date', [$start, $end]);
            }
    
            $profitData = $profitQuery->get();
    
            // إجمالي التكاليف من فواتير الموردين (مبلغ الفاتورة بدون تكاليف + التكاليف)
            $costsQuery = DB::table('supplier_invoices')
                ->selectRaw('DATE(invoice_date) as day, SUM(total_amount_invoice + cost_price) as total_costs')
                ->groupBy('day');
    
            if ($start && $end) {
                $costsQuery->whereBetween('invoice_date', [$start, $end]);
            }
    
            $costsData = $costsQuery->get()->keyBy('day');
    
            // دمج النتائج
            $chartData = $profitData->map(function($item) use ($costsData) {
                $costs = $costsData[$item->day]->total_costs ?? 0;
                $netProfit = $item->total_profit;
                $profitRatio = $costs > 0 ? ($netProfit / $costs) * 100 : 0;
    
                return [
                    'day' => $item->day,
                    'total_costs' => (float) $costs,
                    'net_profit' => (float) $netProfit,
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
        $user = auth()->user();

        // السماح بالوصول فقط للـ Admin
        if ($user->type != 1) {
            return response()->json([
                'error' => 'غير مسموح بعرض الإحصائيات البيانية',
            ], 403);
        }

        $start = $request->start ?? null;
        $end = $request->end ?? null;
    
        $applyDate = function($table, $dateCol = 'invoice_date') use ($start, $end) {
            $q = DB::table($table);
            return ($start && $end) ? $q->whereBetween($dateCol, [$start, $end]) : $q;
        };
    
        // اجمالي المشتريات (الفاتورة بدون تكاليف + التكاليف)
        $purchases = 0.0;
        if (Schema::hasTable('supplier_invoices')) {
            $purchases = (float) $applyDate('supplier_invoices')
                ->selectRaw('SUM(total_amount_invoice + cost_price) as total_costs')
                ->value('total_costs');
        }
    
        // المصروفات
        $expenses = 0.0;
        if (Schema::hasTable('expenses')) {
            $expenses = (float) $applyDate('expenses', 'date')->sum('amount');
        }
    
        // إجمالي التكاليف
        $totalCosts = $purchases + $expenses;
    
        // صافي الربح من فواتير المبيعات
        $netProfit = 0.0;
        if (Schema::hasTable('customer_invoices') && Schema::hasColumn('customer_invoices', 'total_profit')) {
            $netProfit = (float) $applyDate('customer_invoices', 'invoice_date')->sum('total_profit');
        }
    
        // بيانات الرسم
        $pieData = [
            'labels' => ['إجمالي التكاليف', 'صافي الربح'],
            'datasets' => [[
                'data' => [round($totalCosts,2), round($netProfit,2)],
                'backgroundColor' => ['#f87171', '#34d399'] // ألوان (تكاليف أحمر / ربح أخضر)
            ]]
        ];
    
        return response()->json([
            'summary' => [
                'period' => ($start && $end) ? "$start إلى $end" : 'حتى الآن',
                'total_costs' => round($totalCosts,2),
                'net_profit' => round($netProfit,2),
            ],
            'pie_chart' => $pieData,
        ]);
    }
    
}
