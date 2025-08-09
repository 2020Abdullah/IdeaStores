@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">{{ $expenseItem->name }}</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض الحركات</a>
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
                    <h3>مجموع المصروفات</h3>
                    <h4>{{ number_format($expenseItem->exponses->sum('amount'), 2) }} EGP</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">حركات البند : {{ $expenseItem->name }}</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>المصدر</th>
                        <th>تم الخصم من حساب</th>
                        <th>البيان</th>
                        <th>المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenseItem->exponses as $ex)
                        <tr>
                            <td>
                                @php
                                    $typeName = class_basename($ex->expenseable_type);
                                @endphp
                                @if ($typeName === 'Supplier_invoice')
                                    <span>فاتورة شراء</span>
                                @endif
                            </td>
                            <td>
                                {{ $ex->account->name }}
                            </td>
                            <td>{{ $ex->note }}</td>
                            <td>{{ $ex->amount }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">لا توجد اى حركات لهذا البند.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $exponses->links() }}
    </div>
</div>
@endsection
