<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'مواقع مدونات', 'slug' => Str::slug('مواقع مدونات')],
            ['name' => 'منصات تعليمية', 'slug' => Str::slug('منصات تعليمية')],
            ['name' => 'مواقع الشركات وعرض الخدمات', 'slug' => Str::slug('مواقع الشركات وعرض الخدمات')],
            ['name' => 'المتاجر الإلكتروني', 'slug' => Str::slug('المتاجر الإلكترونية')],
            ['name' => 'أنظمة الحجز الإلكتروني', 'slug' => Str::slug('أنظمة الحجز الإلكتروني')],
            ['name' => 'أنظمة إدارة العملاء CRM', 'slug' => Str::slug('أنظمة إدارة العملاء CRM')],
            ['name' => 'أنظمة المحاسبة والمخزون', 'slug' => Str::slug('أنظمة المحاسبة والمخزون')],
            ['name' => 'مواقع التواصل الاجتماعي البسيطة', 'slug' => Str::slug('مواقع التواصل الاجتماعي البسيطة')],
            ['name' => 'أنظمة العضويات', 'slug' => Str::slug('أنظمة العضويات')],
            ['name' => 'أنظمة إدارية', 'slug' => Str::slug('أنظمة إدارية')],
        ]);
    }
}
