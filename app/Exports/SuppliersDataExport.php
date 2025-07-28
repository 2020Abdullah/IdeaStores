<?php

namespace App\Exports;

use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class SuppliersDataExport implements FromView
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function view(): View
    {
        $suppliers = Supplier::with('account')
            ->whereIn('id', $this->ids)
            ->get();

        return view('exports.suppliersDataExport', [
            'suppliers' => $suppliers
        ]);
    }

}
