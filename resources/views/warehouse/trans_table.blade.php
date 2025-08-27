@forelse($transactions as $t)
<tr>
    @php
        $typeName = class_basename($t->related_type);
    @endphp
    <td>{{ $t->wallet->name ?? '_'}}</td>
    <td>{{ $t->date ?? $t->created_at->format('Y-m-d') }}</td>
    <td>
        @if ($t->transaction_type === 'added')
            <span>إضافة يدوية</span> 
        @elseif($t->transaction_type === 'payment')
            <span>مدفوعات</span>  
        @elseif($t->transaction_type === 'expense')   
            <span>مصروفات</span>     
        @elseif($t->transaction_type === 'purchase')    
            <span>مشتريات</span> 
        @elseif($t->transaction_type === 'sale')   
            <span>مبيعات</span> 
        @elseif($t->transaction_type === 'transfer')     
            <span>تحويل رصيد</span>    
        @else  
            <span>رد مدفوعات</span>         
        @endif
    </td>
    <td>
        @if ($t->direction === 'in')
            <span class="badge bg-success">إضافة رصيد</span> 
        @else 
            <span class="badge bg-danger">خصم رصيد</span>               
        @endif
    </td>
    <td>
        @if ($t->direction == 'in')
            <span class="text-success">{{ number_format($t->amount, 2) }}</span>
        @else 
            <span class="text-danger">{{ number_format($t->amount, 2) }}</span>
        @endif
    </td>
    <td>{{ $t->description ?? '-' }}</td>
    <td>
        @if ($t->source_code !== null)
            @if ($typeName === 'Supplier_invoice')
                <a href="{{ route('supplier.invoice.show', $t->source_code) }}">
                    {{ $t->source_code }}
                </a>
            @else 
                <a href="{{ route('customer.invoice.show', $t->source_code) }}">
                    {{ $t->source_code }}
                </a>
            @endif
        @else
            _
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center">لا توجد حركات مالية لهذا الحساب.</td>
</tr>
@endforelse