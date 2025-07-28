<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SuppliersImport implements ToModel, WithHeadingRow, WithStartRow
{
    public function startRow(): int
    {
        return 2; 
    }

    public function model(array $row)
    {
        $Supplier = new Supplier([
            'name' => $row['اسم_المورد'],
        ]);

        $Supplier->save();
         
        $Supplier->account()->create([
            'name' => 'حساب المورد ' . $row['اسم_المورد'],
            'type' => 'supplier',
            'total_capital_balance' => 0,
            'total_profit_balance' => 0,
        ]);
    }
}
