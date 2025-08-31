<?php

namespace App\Http\Controllers;

use App\Exports\CategoriesExport;
use App\Exports\ProductExport;
use App\Imports\CategoriesImport;
use App\Imports\ProductImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportProducts()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function exportCategories()
    {
        return Excel::download(new CategoriesExport, 'categories.xlsx');
    }

    // ========== الاستيراد ==========
    public function ImportProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new ProductImport, $request->file('file'));

        return back()->with('success', '✅ تم استيراد المنتجات بنجاح');
    }

    public function ImportCategories(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new CategoriesImport, $request->file('file'));

        return back()->with('success', '✅ تم استيراد الأصناف بنجاح');
    }
}
