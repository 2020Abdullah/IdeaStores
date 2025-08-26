<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CustomerImport implements ToModel, WithHeadingRow, WithStartRow
{
    public function startRow(): int
    {
        return 2; 
    }

    public function model(array $row)
    {
        $Supplier = new Customer([
            'name' => $row['اسم_العميل'],
        ]);

        $Supplier->save();
         
        $Supplier->account()->create([
            'name' => 'حساب العميل ' . $row['اسم_العميل'],
            'type' => 'customer',
        ]);
    }
}
