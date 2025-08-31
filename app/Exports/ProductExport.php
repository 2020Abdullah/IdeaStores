<?php

namespace App\Exports;

use App\Models\product;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::with('category:id,name')
        ->get()
        ->map(function ($product) {
            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'category_id' => $product->category_id,
                'category'    => $product->category?->name,
            ];
        });
    }
}
