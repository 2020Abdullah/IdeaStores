<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class SuppliersTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['اسم_المورد'],
        ];
    }
}
