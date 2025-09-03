<table class="table table-bordered">
    <tr>
        <th>التاريخ</th>
        @if (auth()->user()->type == 1)
            <th>الجهة</th>            
        @endif
        <th>نوع الحركة</th>
        <th>الكمية المحركة</th>
        <th>البيان</th>
        @if (auth()->user()->type == 1)
            <th>المرجع</th>            
        @endif
        <th>تمت الحركة بواسطة</th>
    </tr>
    @foreach ($stock_movments as $move)
        <tr>
            <td>{{ $move->date }}</td>
            @if (auth()->user()->type == 1)
                <td>
                    @if ($move->related instanceof \App\Models\Supplier)
                        مورد : {{ $move->related->name }}
                    @elseif ($move->related instanceof \App\Models\Customer)
                        عميل : {{ $move->related->name }}
                    @else
                        غير معروف
                    @endif
                </td>
            @endif
            <td>
                @if ($move->type === 'in')
                    وارد
                @else
                    صادر
                @endif
            </td>
            <td>
                @if ($stock->unit->name === 'سنتيمتر')
                    @if ($move->type === 'in')
                        <span class="text-success">+{{ $move->quantity}}  متر</span>
                    @else
                        <span class="text-danger">{{ $move->quantity}}  متر</span>
                    @endif      
                @else
                    @if ($move->type === 'in')
                        <span class="text-success">+{{ $move->quantity }} {{ $stock->unit->name }}</span>
                    @else
                        <span class="text-danger">{{ $move->quantity }} {{ $stock->unit->name }}</span>
                    @endif  
                @endif
            </td>
            <td>
                @if ($move->type === 'in')
                    فاتورة شراء
                @else
                    فاتورة بيع
                @endif
            </td>
            @if (auth()->user()->type == 1)
                <td>
                    @if ($move->type === 'in')
                        <a href="{{ route('supplier.invoice.show', $move->source_code) }}">
                            {{ $move->source_code }}
                        </a>
                    @else
                        <a href="{{ route('customer.invoice.show', $move->source_code) }}">
                            {{ $move->source_code }}
                        </a>
                    @endif
                </td>
            @endif
            <td>{{ $move->user->name }}</td>
        </tr>
    @endforeach
</table>