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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $start = $request->input('start');
        $end = $request->input('end');
    
        // تحقق من أن start و end مرسلين بشكل صحيح
        if (!$start || !$end) {
            return response()->json([]);
        }
    
        $data = DB::table('customer_invoices')
            ->whereBetween('date', [$start, $end])  // مهم جدًا
            ->selectRaw('DATE(date) as day, SUM(total_amount) as total_sales, SUM(total_profit) as total_profit')
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->get();
    
        return response()->json($data);
    }


}
