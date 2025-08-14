@extends('layouts.app')

@section('css')
<style>
    .table input, .table select {
        width: 200px!important;
    }
</style>
@endsection

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">إضافة فاتورة بيع</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">بيانات الفاتورة</a>
                    </li>
                    <li class="breadcrumb-item active">
                        إضافة فاتورة جديدة
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<section class="addInvoice">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">إضافة فاتورة مبيعات</h3>
        </div>
        <form action="{{ route('supplier.invoice.store') }}" id="invoiceForm" method="POST">
            @csrf
            <input type="hidden" name="method" class="method">
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label" for="name">العميل</label>
                    @if (isset($customer))
                        <input type="hidden" class="form-control" name="customer_id" value="{{ $customer->id }}" required>
                        <input type="text" class="form-control" value="{{ $customer->name }}" readonly>
                    @else
                        <select name="supplier_id" class="form-select">
                            <option value="">أختر العميل ...</option>
                            @foreach ($customer_list as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('customer_id')
                        <div class="alert alert-danger mt-1" role="alert">
                            <h4 class="alert-heading">خطأ</h4>
                            <div class="alert-body">
                                {{ @$message }}
                            </div>
                        </div>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label" for="phone">تاريخ الفاتورة</label>
                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" name="invoice_date" required/>
                    @error('invoice_date')
                        <div class="alert alert-danger mt-1" role="alert">
                            <h4 class="alert-heading">خطأ</h4>
                            <div class="alert-body">
                                {{ @$message }}
                            </div>
                        </div>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label">نوع الفاتورة</label>
                    <select name="invoice_type" class="form-select invoice_type @error('invoice_type') is-invalid @enderror" required>
                        <option value="">اختر ...</option>
                        <option value="cash">كاش</option>
                        <option value="credit">آجل</option>
                        <option value="opening_balance">رصيد افتتاحي</option>
                    </select>
                    @error('invoice_type')
                        <div class="alert alert-danger mt-1" role="alert">
                            <h4 class="alert-heading">خطأ</h4>
                            <div class="alert-body">
                                {{ @$message }}
                            </div>
                        </div>
                    @enderror
                </div>
                <div class="mb-1 warehouse_container" style="display: none;">
                    <label class="form-label">من حساب</label>
                    <select name="warehouse_id" class="form-control warehouse_id">
                        <option value="">اختر الخزنة ...</option>
                        @foreach ($warehouse_list as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>              
                        @endforeach
                    </select>
                </div>
                <div class="mb-1 wallet_container" style="display: none;">
                    <label class="form-label">المحفظة</label>
                    <select name="wallet_id" class="form-control wallet_id">
                        <option value="">...</option>
                    </select>
                </div>
                <div class="mb-1 balance_container" style="display: none;">
                    <label class="form-label current_balance_label"></label>
                    <input type="hidden" class="form-control current_balance" name="current_balance" readonly>
                </div>
                <div class="mb-1 opening_balance_container" style="display: none;">
                    <label class="form-label">قيمة الرصيد الإفتتاحي</label>
                    <input type="number" class="form-control opening_balance_value" value="0" name="opening_balance_value">
                </div>
                <div class="mb-1 alert_container" style="display: none;">
                    <div class="alert alert-danger">
                        <h4 class="alert-heading">
                            يوجد خطأ
                        </h4>
                        <div class="alert-body">
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <button type="button" disabled class="addItems btn-icon-content btn btn-success waves-effect waves-float waves-light">
                        <i data-feather='plus-circle'></i>
                        <span>إضافة صنف جديد</span> 
                    </button>
                </div>
                <div class="mb-2">  
                    <div class="table-items">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <th>الصنف</th>
                                    <th>المنتج</th>
                                    <th>العرض</th>
                                    <th>الكمية المتاحة</th>
                                    <th>وحدة القياس</th>
                                    <th>سعر البيع</th>
                                    <th>الطول</th>
                                    <th>الكمية</th>
                                    <th>الإجمالي</th>
                                    <th>حذف</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="all_total">
                        <span>إجمالي الفاتورة :</span>
                        <strong>0</strong> 
                        <span>EGP</span>
                        <input type="hidden" class="total_amount_invoice" name="total_amount_invoice">
                    </div>
                </div>
                <div id="costs-wrapper">
                    <button type="button" class="btn btn-primary waves-effect waves-float waves-light mb-1" id="add-cost" disabled>+ أضف تكلفة</button>                
                </div>
                <div class="mb-2">
                    <label class="form-label">مجموع التكاليف</label>
                    <input type="text" id="total-cost" class="form-control additional_cost" value="0" name="additional_cost" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">إجمالي الفاتورة شامل سعر التكلفة</label>
                    <input type="text" class="form-control total_amount" value="0" name="total_amount" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">ملاحظات (اختيارى)</label>
                    <textarea class="form-control" cols="5" rows="5" name="notes"></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-relief-success">حفظ الفاتورة</button>
            </div>
        </form>
    </div>
</section>


@endsection

@section('js')
<script>
$(document).ready(function(){

})
</script>

@endsection 