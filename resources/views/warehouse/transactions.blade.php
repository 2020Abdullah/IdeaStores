@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">{{ $warehouse->name }}</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض سجل حركات الخزنة</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="card-balance">
                    <h3>رصيد الخزنة</h3>
                    <h4>{{ number_format($transactions->sum('amount'), 2) }} EGP</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">عرض سجل حركات الخزنة</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>نوع المصدر</th>
                        <th>إلي حساب</th>
                        <th>نوع المعاملة</th>
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
                                {{ $warehouse->name }}
                            </td>
                            <td>
                                {{ optional($transaction->related)->name ?? '-' }}
                            </td>
                            <td>
                                @if ($transaction->transaction_type === 'payment')
                                    مدفوعات
                                @elseif($transaction->transaction_type === 'expense')
                                    مصروفات
                                @elseif($transaction->transaction_type === 'added')
                                    إضافة رصيد
                                @else 
                                    تحويل 
                                @endif
                            </td>
                            <td>{{ $transaction->method }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td class="{{ $transaction->direction === 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->direction === 'in' ? '+' : ''}}{{ number_format($transaction->amount, 2) }}
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
