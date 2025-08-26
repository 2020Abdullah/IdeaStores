<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class CustomerTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['اسم_العميل'],
        ];
    }
}
