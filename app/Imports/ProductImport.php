<?php

namespace App\Imports;

use App\Models\Product;
use DragonCode\Contracts\Routing\Core\Tag;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductImport implements ToModel
{
    public function model(array $row)
    {
        if ($row[0] === 'id' || $row[0] === null) {
            return null;
        }

        return Product::updateOrCreate(
            ['id' => $row[0]],
            [
                'name'        => $row[1],
                'category_id' => $row[2], // هنا لازم الـ category_id يجي من الشيت
            ]
        );
    }
}
