<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;

class CategoriesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if ($row[0] === 'id' || $row[0] === null) {
            return null;
        }

        return Category::updateOrCreate(
            ['id' => $row[0]], // لو الـ id موجود يحدث، لو مش موجود يضيف
            [
                'name'      => $row[1],
                'parent_id' => $row[2] ?? null,
            ]
        );
    }
}
