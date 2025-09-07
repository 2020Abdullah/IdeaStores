<table class="table table-bordered">
    <tr>
        <th>كود الفاتورة</th>
        <th>تاريخ الفاتورة</th>
        <th>اسم العميل</th>
        <th>نوع الفاتورة</th>
        <th>إجمالي الفاتورة قبل الخصم</th>
        <th>الخصم</th>
        <th>إجمالي الفاتورة بعد الخصم</th>
        <th>المبلغ المدفوع</th>
        <th>حالة الفاتورة</th>
        <th>تم الإضافة بواسطة</th>
        <th>إجراء</th>
    </tr>
    @foreach ($invoices_list as $inv)
        <tr>
            <td>
                <a href="{{ route('customer.invoice.show', $inv->code) }}">
                    {{ $inv->code }}
                </a>
            </td>
            <td>{{ $inv->date }}</td>
            <td>
                <a href="{{ route('customer.account.show', $inv->customer->id) }}">
                    {{ $inv->customer->name }}
                </a>
            </td>
            <td>
                @if ($inv->type === 'cash')
                    <span>كاش</span>
                @elseif($inv->type === 'credit')
                    <span>آجل</span>
                @else
                    <span>رصيد افتتاحي</span>
                @endif
            </td>
            <td>{{ number_format($inv->total_amount_without_discount) }} EGP</td>
            <td>
                @if ($inv->discount_type && $inv->discount_value)
                    @if ($inv->discount_type === 'percent')
                        {{ $inv->discount_value }}%
                    @else
                        {{ number_format($inv->discount_value) }} 
                    @endif
                @else
                    0 
                @endif
            </td>
            <td>
                @php
                    $discountValue = 0;
                    if ($inv->discount_type && $inv->discount_value) {
                        if ($inv->discount_type === 'percent') {
                            $discountValue = $inv->total_amount_without_discount * $inv->discount_value / 100;
                        } else {
                            $discountValue = $inv->discount_value;
                        }
                    }
                    $totalAfterDiscount = $inv->total_amount_without_discount - $discountValue;
                @endphp
                {{ number_format($totalAfterDiscount) }} EGP
            </td>
            <td>{{ number_format($inv->paid_amount) }} EGP</td>
            <td>
                @if ($inv->staute == 0)
                    <span class="badge badge-glow bg-danger">غير مدفوع</span>
                @elseif($inv->staute == 2)
                    <span class="badge badge-glow bg-warning">لم يتم التصفية</span>
                @else
                    <span class="badge badge-glow bg-success">مدفوعة</span>
                @endif
            </td>
            <td>{{ $inv->user->name }}</td>
            <td>
                <a href="{{ route('customer.invoice.edit', $inv->id) }}"
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
