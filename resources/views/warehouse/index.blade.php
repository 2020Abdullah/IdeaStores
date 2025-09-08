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
                <div class="card-action">
                    @if ($warehouse_list->count() < 2)
                        <button type="button" class="btn btn-success waves-effect" data-bs-toggle="modal" data-bs-target="#addWarehouse">
                            إنشاء خزنة
                        </button>
                    @endif
                    <button type="button" class="btn btn-primary waves-effect" data-bs-toggle="modal" data-bs-target="#transfer">
                        تسوية رصيد
                    </button>
                </div>                  
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>الخزنة</th>
                            <th>رصيد الربحية</th>
                            <th>الرصيد الحالي</th>
                            <th>هل هي افتراضية</th>
                            <th>إجراء</th>
                        </tr>
                        @forelse ($warehouse_list as $w)
                            <tr>
                                <td>{{ $w->name }}</td>
                                <td>{{ number_format($w->account->transactions->sum('profit_amount')) }}</td>
                                <td>{{ number_format($w->account->transactions->sum('amount')) }}</td>
                                <td>
                                    @if ($w->is_default == 0)
                                        <Span>لا</Span>
                                    @else
                                        <Span>نعم</Span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('warehouse.transactions', $w->id) }}" class="btn btn-icon btn-info waves-effect">
                                        <i data-feather='eye'></i>
                                    </a>
                                    <a href="#"
                                    class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn"
                                    data-bs-toggle="modal" data-bs-target="#editWarehouse"
                                    data-warehouse_id="{{ $w->id }}" 
                                    data-name="{{ $w->name }}"
                                    data-is_default="{{ $w->is_default }}"
                                    >
                                        <i data-feather='edit'></i>
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

@section('js')
<script>
    $(function(){
        $(document).on('submit', '.formSubmit', function(e){
            e.preventDefault();
            $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
            e.currentTarget.submit();
        });

        $(document).on('click', '.editBtn', function(e){
            let warehouse_id = $(this).data('warehouse_id');
            let name = $(this).data('name');
            let is_default = $(this).data('is_default');

            $("#editWarehouse .warehouse_id").val(warehouse_id);
            $("#editWarehouse .name").val(name);
            $("#editWarehouse .is_default").val(is_default);
        });
    })
</script>
@endsection