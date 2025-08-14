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

            // مسار ملف النسخة الاحتياطية
            $backupFile = storage_path('app/backup_' . date('Y-m-d_H-i-s') . '.sql');

            // مسار mysqldump (عدله حسب جهازك)
            $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';

            // أمر النسخ الاحتياطي
            $command = "\"$mysqldumpPath\" --user={$db['username']} --password=\"{$db['password']}\" --host={$db['host']} {$db['database']} > \"$backupFile\"";

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                return back()->with('error', '❌ فشل إنشاء النسخة الاحتياطية.');
            }

            // إرجاع الملف للتحميل
            return response()->download($backupFile)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    // استعادة نسخة احتياطية
    public function restoreBackupFlexible(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);
    
        try {
            $file = $request->file('backup_file');
            $sqlContent = file_get_contents($file->getRealPath());
    
            // تقسيم أوامر SQL حسب الفاصلة المنقوطة
            $statements = array_filter(array_map('trim', explode(";", $sqlContent)));
    
            // المرحلة 1: إنشاء الجداول وإضافة الأعمدة الجديدة
            foreach ($statements as $stmt) {
                if (stripos($stmt, 'CREATE TABLE') === 0) {
                    preg_match('/CREATE TABLE `?(\w+)`?/i', $stmt, $matches);
                    $tableName = $matches[1] ?? null;
    
                    if ($tableName) {
                        if (Schema::hasTable($tableName)) {
                            // الجدول موجود → إضافة الأعمدة الجديدة فقط
                            preg_match_all('/^\s*`([^`]+)`\s+([^,]+)/m', $stmt, $matches, PREG_SET_ORDER);
                            foreach ($matches as $match) {
                                $col = $match[1];
                                $colType = trim($match[2]);
    
                                if (!Schema::hasColumn($tableName, $col)) {
                                    if (preg_match('/^([a-z]+(\(\d+(,\d+)?\))?)/i', $colType, $typeMatch)) {
                                        $cleanType = $typeMatch[1];
                                        DB::statement("ALTER TABLE `$tableName` ADD `$col` $cleanType");
                                    }
                                }
                            }
                        } else {
                            // الجدول غير موجود → إنشاء كامل
                            DB::statement($stmt);
                        }
                    }
                }
            }
    
            // المرحلة 2: استيراد البيانات بشكل مرن
            foreach ($statements as $stmt) {
                if (stripos($stmt, 'INSERT INTO') === 0) {
                    preg_match('/INSERT INTO `?(\w+)`?/i', $stmt, $matches);
                    $tableName = $matches[1] ?? null;
    
                    if ($tableName && Schema::hasTable($tableName)) {
                        // جلب الأعمدة الحالية للجدول
                        $columns = Schema::getColumnListing($tableName);
                        $columnsList = implode(',', array_map(fn($c) => "`$c`", $columns));
    
                        // تعديل INSERT ليشمل فقط الأعمدة الموجودة
                        $stmt = preg_replace('/INSERT INTO `?\w+`?/i', "INSERT IGNORE INTO `$tableName` ($columnsList)", $stmt);
    
                        DB::statement($stmt);
                    }
                }
            }
    
            return back()->with('success', 'تمت الاستعادة بنجاح.');
    
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    
    
    

}
