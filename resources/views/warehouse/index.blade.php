@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">الخزن</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">
                        <a href="#">بيانات الخزن</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <section class="warehouse">
        <!-- all warehouse -->
        <div class="card">
            <div class="card-header">
                <h3>كل الخزن</h3>
                @if ($warehouse_list->count() < 2)
                    <div class="card-action">
                        <button type="button" class="btn btn-outline-success round waves-effect" data-bs-toggle="modal" data-bs-target="#addWarehouse">
                            إنشاء خزنة
                        </button>
                    </div>                  
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>الخزنة</th>
                            <th>نوع الخزنة</th>
                            <th>رصيد الربحية</th>
                            <th>الرصيد الحالي</th>
                            <th>حالة الخزنة</th>
                            <th>إجراء</th>
                        </tr>
                        @forelse ($warehouse_list as $w)
                            <tr>
                                <td>{{ $w->name }}</td>
                                <td>خزنة فرعية</td>
                                <td>{{ number_format($w->account->transactions->where('transaction_type', 'profit')->sum('amount')) }}</td>
                                <td>{{ number_format($w->account->transactions->sum('amount')) }}</td>
                                <td>
                                    @if ($w->statue == 1)
                                        <span class="badge badge-light-success">مفعلة</span>
                                    @else
                                        <span class="badge badge-light-danger">غير مفعلة</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('wallets.index', $w->id) }}" class="btn btn-primary waves-effect">
                                        <i data-feather='eye'></i>
                                        <span>المحافظ</span>
                                    </a>
                                    <a href="{{ route('warehouse.transactions', $w->id) }}" class="btn btn-danger waves-effect">
                                        <i data-feather='eye'></i>
                                        <span>سجل الحركات</span>
                                    </a>
                                </td>
                            </tr>   
                        @empty
                            <tr class="text-center">
                                <td colspan="6">لا توجد خزن فرعية</td>
                            </tr>                     
                        @endforelse 
                    </table> 
                </div>                  
            </div>
        </div>
        <!-- models -->
        @include('warehouse.models')
    </section>
@endsection


