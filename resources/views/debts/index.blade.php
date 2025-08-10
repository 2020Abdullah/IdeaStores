@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">الديون الخارجية</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض الديون الخارجية</a>
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
                    <h3>الديون الخارجية</h3>
                    <h4>{{ number_format($debts->sum('remaining')) }} EGP</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">عرض الديون الخارجية</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="expenses-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>تاريخ الإنشاء</th>
                        <th>نوع المصدر</th>
                        <th>الوصف</th>
                        <th>الإجمالي</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debts as $index => $debt)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $debt->created_at->format('Y-m-d') }}</td>
                            <td>
                                @php
                                    $typeName = class_basename($debt->debtable_type);
                                @endphp
                                {{ $typeName == 'Customer_invoice' ? 'فاتورة عميل' : ($typeName == 'Supplier_invoice' ? 'فاتورة مورد' : 'أخرى') }}
                            </td>
                            <td>{{ $debt->description ?? '-' }}</td>
                            <td>{{ number_format($debt->amount, 2) }} EGP</td>
                            <td>{{ number_format($debt->paid, 2) }} EGP</td>
                            <td>{{ number_format($debt->remaining, 2) }} EGP</td>
                            <td>
                                @if($debt->is_paid)
                                    <span class="badge bg-success">مدفوع</span>
                                @else
                                    <span class="badge bg-danger">غير مدفوع</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">لا توجد مديونيات حالياً</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="page-num">
            {{ $debts->links() }}
        </div>
    </div>
</div>
@endsection
