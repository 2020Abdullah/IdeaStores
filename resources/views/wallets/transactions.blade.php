@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">{{ $wallet->name }}</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('wallets.index') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض سجل حركات المحفظة</a>
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
                    <h3>رصيد المحفظة</h3>
                    <h4>{{ number_format($wallet->balance, 2) }} EGP</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">عرض سجل حركات المحفظة</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الحساب</th>
                        <th>نوع المعاملة</th>
                        <th>الاتجاه</th>
                        <th>المبلغ</th>
                        <th>البيان</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($t->date)->format('Y-m-d') }}</td>
                            <td>{{ $t->account->name }}</td>
                            <td>
                                @if ($t->transaction_type === 'added')
                                    <span>إضافة يدوية</span> 
                                @elseif($t->transaction_type === 'payment')
                                    <span>مدفوعات</span>  
                                @elseif($t->transaction_type === 'expense')   
                                    <span>مصروفات</span> 
                                @elseif($t->transaction_type === 'expense')  
                                    <span>فاتورة مشتريات</span>    
                                @else  
                                    <span>فاتورة مبيعات</span>    
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">لا توجد حركات مالية لهذه المحفظة.</td>
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
