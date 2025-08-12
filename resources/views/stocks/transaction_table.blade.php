<table class="table table-bordered">
    <tr>
        <th>التاريخ</th>
        <th>المورد</th>
        <th>نوع الحركة</th>
        <th>الكمية المحركة</th>
        <th>البيان</th>
        <th>المرجع</th>
    </tr>
    @foreach ($stock_movments as $move)
        <tr>
            <td>{{ $move->date }}</td>
            <td>{{ $move->supplier->name }}</td>
            <td>
                @if ($move->type == 'in')
                    وارد
                @else
                    صادر
                @endif
            </td>
            <td>{{ $move->quantity }}</td>
            <td>
                @if ($move->type === 'in')
                    فاتورة شراء
                @else
                    فاتورة بيع
                @endif
            </td>
            <td>
                <a href="{{ route('supplier.invoice.show', $move->source_code) }}">
                    {{ $move->source_code }}
                </a>
            </td>
        </tr>
    @endforeach
</table>