@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">الخزن</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">الرئيسية</a>
                    </li>
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
        @if ($main_warehouse)
            <!-- card main warehouse -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $main_warehouse->name }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="card-balance">
                                <h3>الرصيد الحالي</h3>
                                <h4>{{ number_format($main_warehouse->account->current_balance) }}</h4>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card-balance">
                                <h3>رصيد مديونية</h3>
                                <h4>{{ number_format($main_warehouse->account->total_capital_balance) }}</h4>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card-balance">
                                <h3>رصيد ربحية</h3>
                                <h4>{{ number_format($main_warehouse->account->total_profit_balance) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- sub warehouse -->
            <div class="card">
                <div class="card-header">
                    <h3>الخزن الفرعية</h3>
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
                                <th>رصيد مديونية</th>
                                <th>رصيد الربحية</th>
                                <th>الرصيد الحالي</th>
                                <th>حالة الخزنة</th>
                                <th>إجراء</th>
                            </tr>
                            @forelse ($warehouse_list as $w)
                                <tr>
                                    <td>{{ $w->name }}</td>
                                    <td>خزنة فرعية</td>
                                    <td>{{ number_format($w->account->total_capital_balance) }}</td>
                                    <td>{{ number_format($w->account->total_profit_balance) }}</td>
                                    <td>{{ number_format($w->account->current_balance) }}</td>
                                    <td>
                                        @if ($w->statue == 1)
                                            <span class="badge badge-light-success">مفعلة</span>
                                        @else
                                            <span class="badge badge-light-danger">غير مفعلة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('wallets.index', $w->id) }}" class="btn btn-info waves-effect">
                                            <i data-feather='eye'></i>
                                            <span>تفاصيل</span>
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
        @else
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إضافة خزنة</h3>
                </div>
                <form method="POST" action="{{ route('warehouse.store') }}">
                    @csrf
                    <input type="hidden" value="1" name="is_main">
                    <input type="hidden" value="main" name="type">
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label" for="name">اسم الخزنة</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name">
                            @error('name')
                                <div class="alert alert-danger mt-1" role="alert">
                                    <h4 class="alert-heading">خطأ</h4>
                                    <div class="alert-body">
                                        {{ @$message }}
                                    </div>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-relief-success">حفظ البيانات</button>
                    </div>
                </form>
            </div>
        @endif
    </section>
@endsection
