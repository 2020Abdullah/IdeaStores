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
    
            if ($user->type != 1) {
                return response()->json(['error' => 'غير مسموح بعرض الإحصائيات البيانية'], 403);
            }
    
            $start = $request->input('start');
            $end   = $request->input('end');
    
            // إجمالي المبيعات اليومية (بعد الخصومات)
            $salesQuery = DB::table('customer_invoices')
                ->selectRaw('DATE(date) as day, SUM(total_amount) as total_sales')
                ->groupBy('day')
                ->orderBy('day', 'ASC');
    
            if ($start && $end) {
                $salesQuery->whereBetween('date', [$start, $end]);
            }
    
            $salesData = $salesQuery->get()->keyBy('day');
    
            // إجمالي المشتريات + التكاليف اليومية
            $costsQuery = DB::table('supplier_invoices')
                ->selectRaw('DATE(invoice_date) as day, SUM(total_amount_invoice + cost_price) as total_costs')
                ->groupBy('day')
                ->orderBy('day', 'ASC');
    
            if ($start && $end) {
                $costsQuery->whereBetween('invoice_date', [$start, $end]);
            }
    
            $costsData = $costsQuery->get()->keyBy('day');
    
            // دمج الأيام
            $allDays = collect(array_unique(array_merge(
                $salesData->keys()->toArray(),
                $costsData->keys()->toArray()
            )))->sort();
    
            $chartData = $allDays->map(function($day) use ($salesData, $costsData) {
                return [
                    'day'         => $day,
                    'total_sales' => (float) ($salesData[$day]->total_sales ?? 0),
                    'total_costs' => (float) ($costsData[$day]->total_costs ?? 0),
                ];
            });
    
            return response()->json($chartData->values()->all());
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    
    public function profitLossChart()
    {
        $user = auth()->user();
    
        if ($user->type != 1) {
            return response()->json(['error' => 'غير مسموح بعرض الإحصائيات البيانية'], 403);
        }
    
        // إجمالي المشتريات (فاتورة المورد + التكاليف)
        $totalPurchases = Schema::hasTable('supplier_invoices')
            ? (float) DB::table('supplier_invoices')->selectRaw('SUM(total_amount_invoice + cost_price) as total')->value('total')
            : 0;
    
        // المصروفات (نجعلها بالسالب)
        $totalExpenses = Schema::hasTable('expenses')
            ? -(float) DB::table('expenses')->sum('amount') // السالب هنا
            : 0;
    
        $totalCosts = $totalPurchases + $totalExpenses; // الآن التكاليف الإجمالية تشمل المصروفات بالسالب
    
        // صافي الربح من المبيعات
        $netProfit = (Schema::hasTable('customer_invoices') && Schema::hasColumn('customer_invoices', 'total_profit'))
            ? (float) DB::table('customer_invoices')->sum('total_amount')
            : 0;
    
        // بيانات الرسم البياني
        $pieData = [
            'labels' => ['إجمالي التكاليف', 'المبيعات'],
            'datasets' => [[
                'data' => [round($totalCosts, 2), round($netProfit, 2)],
                'backgroundColor' => ['#f87171', '#34d399'] // أحمر للتكاليف / أخضر للربح
            ]]
        ];
    
        return response()->json([
            'summary' => [
                'period' => 'حتى الآن',
                'total_costs' => round($totalCosts, 2),
                'net_profit' => round($netProfit, 2),
            ],
            'pie_chart' => $pieData,
        ]);
    }
    
    
    
    
}
