<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
  // إنشاء نسخة احتياطية وتحميلها
    public function downloadBackup()
    {
        try {
            $db = config('database.connections.mysql');
    
            $backupFile = storage_path('app/backup_' . date('Y-m-d_H-i-s') . '.sql');
    
            $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';
    
            // ⚡ هنا هنضيف --routines و --triggers و --single-transaction
            $command = "\"$mysqldumpPath\" --user={$db['username']} --password=\"{$db['password']}\" --host={$db['host']} --routines --triggers --single-transaction {$db['database']} > \"$backupFile\"";
    
            exec($command, $output, $returnVar);
    
            if ($returnVar !== 0) {
                return back()->with('error', '❌ فشل إنشاء النسخة الاحتياطية.');
            }
    
            return response()->download($backupFile)->deleteFileAfterSend(true);
    
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ: ' . $e->getMessage());
        } 
    }

    public function restoreBackupFlexible(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt',
        ]);
    
        try {
            $file = $request->file('backup_file');
            $sqlContent = file_get_contents($file->getRealPath());
    
            $statements = array_filter(array_map('trim', explode(";", $sqlContent)));
    
            DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // ⚡ تعطيل FK
    
            foreach ($statements as $stmt) {
                // CREATE TABLE
                if (stripos($stmt, 'CREATE TABLE') === 0) {
                    preg_match('/CREATE TABLE `?(\w+)`?/i', $stmt, $matches);
                    $tableName = $matches[1] ?? null;
    
                    if ($tableName && !Schema::hasTable($tableName)) {
                        DB::statement($stmt);
                    }
                }
    
                // INSERT INTO
                if (stripos($stmt, 'INSERT INTO') === 0) {
                    preg_match('/INSERT INTO `?(\w+)`?/i', $stmt, $matches);
                    $tableName = $matches[1] ?? null;
    
                    if ($tableName && Schema::hasTable($tableName)) {
                        $columns = Schema::getColumnListing($tableName);
                        $columnsList = implode(',', array_map(fn($c) => "`$c`", $columns));
                        $stmt = preg_replace('/INSERT INTO `?\w+`?/i', "INSERT IGNORE INTO `$tableName` ($columnsList)", $stmt);
                        DB::statement($stmt);
                    }
                }
            }
    
            DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // ⚡ إعادة FK
    
            return back()->with('success', 'تمت الاستعادة بنجاح مع البيانات والجداول والعلاقات.');
    
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // إعادة FK حتى لو حصل خطأ
            return back()->with('error', 'حدث خطأ أثناء الاستعادة: ' . $e->getMessage());
        }
    }
    
    
}
