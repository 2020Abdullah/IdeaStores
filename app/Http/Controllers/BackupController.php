<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
  // إنشاء نسخة احتياطية وتحميلها
  public function createBackup()
  {
      // بيانات الاتصال بقاعدة البيانات من config
      $dbHost = config('database.connections.mysql.host');
      $dbPort = config('database.connections.mysql.port') ?? 3306;
      $dbName = config('database.connections.mysql.database');
      $dbUser = config('database.connections.mysql.username');
      $dbPass = config('database.connections.mysql.password');

      // مسار حفظ النسخة مع اسم ملف يحتوي التاريخ والوقت
      $fileName = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
      $filePath = storage_path('app/' . $fileName);

      // مسار mysqldump.exe في ويندوز (تعديل حسب جهازك)
      $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';

      // أمر النسخ الاحتياطي
      $command = "\"$mysqldumpPath\" --user=$dbUser --password=$dbPass --host=$dbHost --port=$dbPort $dbName > \"$filePath\"";

      // تنفيذ الأمر
      exec($command, $output, $returnVar);

      if ($returnVar !== 0) {
          return back()->with('error', 'فشل عمل النسخة الاحتياطية.');
      }

      // تحميل الملف للمستخدم بعد إنشائه
      response()->download($filePath);

      return back()->with('success', 'تم إنشاء نسخة احتياطية بنجاح.');
  }

  // استعادة نسخة احتياطية مع حذف كل البيانات القديمة
  public function restoreBackup(Request $request)
  {
      $request->validate([
        'backup_file' => 'required|file',
      ]);

      try {
          $file = $request->file('backup_file');

          // حفظ الملف مؤقتًا في storage/app
          $path = $file->storeAs('', 'restore_temp.sql');

          $dbName = config('database.connections.mysql.database');

          // جلب أسماء الجداول في قاعدة البيانات
          $tables = DB::select('SHOW TABLES');

          $key = 'Tables_in_' . $dbName;

          // تعطيل الفحص المفاتيح الأجنبية لتجنب مشاكل الحذف
          DB::statement('SET FOREIGN_KEY_CHECKS=0;');

          foreach ($tables as $table) {
              DB::statement('DROP TABLE IF EXISTS `' . $table->$key . '`');
          }

          DB::statement('SET FOREIGN_KEY_CHECKS=1;');

          // إعداد أمر استعادة النسخة (تعديل مسار mysql.exe حسب جهازك)
          $db = config('database.connections.mysql');

          $mysqlPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysql.exe';

          $restoreCommand = "\"$mysqlPath\" --user={$db['username']} --password=\"{$db['password']}\" --host={$db['host']} {$dbName} < " . storage_path("app/restore_temp.sql");

          // تنفيذ الأمر
          exec($restoreCommand, $output, $return_var);

          // حذف الملف المؤقت بعد الاستعادة
          Storage::delete('restore_temp.sql');

          if ($return_var !== 0) {
              return back()->with('error', 'فشل في استعادة النسخة الاحتياطية.');
          }

          return back()->with('success', 'تمت استعادة النسخة بنجاح.');

      } catch (\Exception $e) {
          return back()->with('error', 'خطأ أثناء الاستعادة: ' . $e->getMessage());
      }
  }
}
