<table class="table table-bordered">
    <tr>
        <th>كود الفاتورة</th>
        <th>تاريخ الفاتورة</th>
        <th>اسم المورد</th>
        <th>نوع الفاتورة</th>
        <th>المبلغ المدفوع</th>
        <th>إجمالي الفاتورة</th>
        <th>المتبقي</th>
        <th>حالة الفاتورة</th>
        <th>إجراء</th>
    </tr>
    @foreach ($invoices_list as $inv)
        <tr>
            <td>{{ $inv->invoice_code }}</td>
            <td>{{ $inv->invoice_date }}</td>
            <td>{{ $inv->supplier->name }}</td>
            <td>
                @if ($inv->invoice_type === 'cash')
                    <span>كاش</span>
                @elseif($inv->invoice_staute === 'credit')
                    <span>آجل</span>
                @else
                    <span>رصيد افتتاحي</span>
                @endif
            </td>
            <td>{{ number_format($inv->paid_amount) }} EGP</td>
            <td>{{ number_format($inv->total_amount) }} EGP</td>
            <td>{{ number_format($inv->total_amount - $inv->paid_amount)}} EGP</td>
            <td>
                @if ($inv->invoice_staute == 0)
                    <span class="badge badge-glow bg-danger">غير مدفوع</span>
                @elseif($inv->invoice_staute == 2)
                    <span class="badge badge-glow bg-warning">دفع جزئي</span>
                @elseif($inv->invoice_staute == 3)
                    <span class="badge badge-glow bg-success">مدفوعة</span>
                @else
                   <span class="badge badge-glow bg-info">رصيد افتتاحي</span>
                @endif
            </td>
            <td>
                
                <a href="{{ route('supplier.invoice.show', $inv->invoice_code) }}"
                   class="btn btn-icon btn-info waves-effect waves-float waves-light editBtn"
                   title="عرض">
                    <i data-feather='eye'></i>
                </a>

                <a href="{{ route('supplier.invoice.edit', $inv->id) }}"
                   class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn"
                   title="تعديل">
                    <i data-feather='edit'></i>
                </a>
{{-- 
                <a href="#" data-bs-toggle="modal" data-bs-target="#delInvoice"
                   data-id="{{ $inv->id }}"
                   data-total_amount="{{ $inv->total_amount }}"
                   data-supplier_id="{{ $inv->supplier_id }}"
                   class="btn btn-icon btn-danger waves-effect waves-float waves-light delBtn"
                   title="حذف">
                    <i data-feather='trash-2'></i>
                </a> --}}

            </td>
        </tr>
    @endforeach
</table>