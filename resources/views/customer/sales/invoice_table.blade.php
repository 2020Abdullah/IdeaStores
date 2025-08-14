<table class="table table-bordered">
    <tr>
        <th>كود الفاتورة</th>
        <th>تاريخ الفاتورة</th>
        <th>اسم المورد</th>
        <th>نوع الفاتورة</th>
        <th>إجمالي الفاتورة</th>
        <th>المبلغ المدفوع</th>
        <th>حالة الفاتورة</th>
        <th>إجراء</th>
    </tr>
    @foreach ($invoices_list as $inv)
        <tr>
            <td>{{ $inv->code }}</td>
            <td>{{ $inv->date }}</td>
            <td>
                <a href="{{ route('supplier.account.show', $inv->customer->id) }}">
                    {{ $inv->customer->name }}
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
            <td>
                @if ($inv->invoice_type !== 'opening_balance')
                    <a href="{{ route('customer.sale.show', $inv->code) }}"
                    class="btn btn-icon btn-info waves-effect waves-float waves-light editBtn"
                    title="عرض">
                        <i data-feather='eye'></i>
                    </a>                                
                @endif

                <a href="{{ route('customer.sale.edit', $inv->id) }}"
                class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn"
                title="تعديل">
                    <i data-feather='edit'></i>
                </a>

                <a href="#" data-bs-toggle="modal" data-bs-target="#delInvoice"
                    data-id="{{ $inv->id }}"
                    data-customer_id="{{ $inv->customer_id }}"
                class="btn btn-icon btn-danger waves-effect waves-float waves-light delBtn"
                title="حذف">
                    <i data-feather='trash-2'></i>
                </a>

            </td>
        </tr>
    @endforeach
</table>