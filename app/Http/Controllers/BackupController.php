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

            // تقسيم أوامر SQL
            $statements = array_filter(array_map('trim', explode(";", $sqlContent)));

            foreach ($statements as $stmt) {
                if (stripos($stmt, 'CREATE TABLE') === 0) {
                    // استخراج اسم الجدول
                    preg_match('/CREATE TABLE `?(\w+)`?/i', $stmt, $matches);
                    $tableName = $matches[1] ?? null;

                    if ($tableName) {
                        // إذا الجدول موجود → أضف الحقول الجديدة فقط
                        if (Schema::hasTable($tableName)) {
                            // جلب الأعمدة فقط من CREATE TABLE (استثناء أول سطر لأنه اسم الجدول)
                            preg_match_all('/^\s*`([^`]+)`\s+([^,]+)/m', $stmt, $matches, PREG_SET_ORDER);
                        
                            foreach ($matches as $match) {
                                $col = $match[1]; // اسم العمود
                                $colType = trim($match[2]); // نوع العمود
                        
                                if (!Schema::hasColumn($tableName, $col)) {
                                    DB::statement("ALTER TABLE `$tableName` ADD `$col` $colType");
                                }
                            }
                        }
                        else {
                            // الجدول غير موجود → أنشئه
                            DB::statement($stmt);
                        }
                    }
                }
                elseif (stripos($stmt, 'INSERT INTO') === 0) {
                    // تعديل INSERT ليصبح مرنًا
                    $stmt = preg_replace('/INSERT INTO/i', 'INSERT IGNORE INTO', $stmt);
                    DB::statement($stmt);
                }
            }

            return back()->with('success',  'تمت الاستعادة بنجاح.');

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
