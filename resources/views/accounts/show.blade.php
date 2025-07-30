@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>كشف : {{ $account->name }}</h3>
    </div>
    <div class="card-balance">
        <h3>الرصيد الحالي</h3>
        <h4>{{ number_format($account->current_balance, 2) }}</h4>
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
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
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
    <div class="card-footer">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
