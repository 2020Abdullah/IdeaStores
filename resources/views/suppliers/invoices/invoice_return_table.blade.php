<table class="table table-bordered">
    <tr>
        <th>كود الفاتورة</th>
        <th>تاريخ الفاتورة</th>
        <th>اسم المورد</th>
        <th>نوع الفاتورة</th>
        <th>إجمالي الفاتورة</th>
        <th>المبلغ المدفوع</th>
        <th>حالة الفاتورة</th>
    </tr>
    @foreach ($invoices_list as $inv)
        <tr>
            <td>{{ $inv->invoice_code }}</td>
            <td>{{ $inv->invoice_date }}</td>
            <td>
                <a href="{{ route('supplier.account.show', $inv->supplier->id) }}">
                    {{ $inv->supplier->name }}
                </a>
            </td>
            <td>
                @if ($inv->invoice_type === 'cash')
                    <span>كاش</span>
                @elseif($inv->invoice_type === 'credit')
                    <span>آجل</span>
                @else
                    <span>رصيد افتتاحي</span>
                @endif
            </td>
            <td>{{ number_format($inv->total_amount_invoice) }} EGP</td>
            <td>{{ number_format($inv->paid_amount) }} EGP</td>
            <td>
                @if ($inv->invoice_staute == 0)
                    <span class="badge badge-glow bg-danger">غير مدفوع</span>
                @elseif($inv->invoice_staute == 2)
                    <span class="badge badge-glow bg-warning">لم يتم التصفية</span>
                @else
                    <span class="badge badge-glow bg-success">مدفوعة</span>
                @endif
            </td>
        </tr>
    @endforeach
</table>