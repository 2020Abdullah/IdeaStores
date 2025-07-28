@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>كشف : {{ $account->name }}</h3>
    </div>
    <div class="card-balance">
        <h3>الرصيد الحالي</h3>
        <h4>{{ number_format($transactions->sum(fn($t) => $t->direction === 'in' ? $t->amount : -$t->amount), 2) }}</h4>
    </div>
    <hr>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>نوع المعاملة</th>
                        <th>الجهة</th>
                        <th>المصدر</th>
                        <th>طريقة الدفع</th>
                        <th>الوصف</th>
                        <th>المبلغ</th>
                        <th>الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    @php $balance = 0; @endphp
                    @forelse($transactions as $transaction)
                        @php
                            $amount = $transaction->amount;
                            $direction = $transaction->direction;
                            $balance += $direction === 'in' ? $amount : -$amount;
                        @endphp
                        <tr>
                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                {{ $transaction->direction === 'in' ? 'إيداع' : 'سحب' }}
                            </td>
                            <td>
                                {{ optional($transaction->related)->name ?? '-' }}
                            </td>
                            <td>
                                {{ class_basename($transaction->source_type) === 'Supplier_invoice' ? 'فاتورة شراء' : 'فاتورة بيع'}}
                            </td>
                            <td>{{ $transaction->method }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td class="{{ $transaction->direction === 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->direction === 'in' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td><strong>{{ number_format($balance, 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">لا توجد حركات مالية لهذا الحساب.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
