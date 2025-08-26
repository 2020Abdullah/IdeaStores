@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">المستحقات</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض المستحقات</a>
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
                    <h3>المستحقات</h3>
                    <h4>{{ number_format($dues->sum('amount')) }} EGP</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">عرض المستحقات</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="expenses-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>تاريخ الإستحقاق</th>
                        <th>العميل</th>
                        <th>الفاتورة</th>
                        <th>الإجمالي</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dues as $index => $due)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $due->due_date }}</td>
                            <td>{{ $due->customer->name }}</td>
                            <td>{{ $due->invoice->code }}</td>
                            <td>{{ number_format($due->amount, 2) }} EGP</td>
                            <td>{{ number_format(-$due->paid_amount, 2) }} EGP</td>
                            <td>{{ number_format($due->amount - $due->paid_amount, 2) }} EGP</td>
                            <td>
                                @if($due->status == 1)
                                    <span class="badge bg-success">مدفوع</span>
                                @else
                                    <span class="badge bg-danger">غير مدفوع</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">لا توجد مستحقات حالياً</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="page-num">
            {{ $dues->links() }}
        </div>
    </div>
</div>
@endsection
