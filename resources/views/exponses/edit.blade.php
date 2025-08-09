@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">تعديل بند مصروف</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('expenses.items') }}">بنود المصروفات</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">تعديل بند مصروف</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">إضافة بند مصروف</h3>
    </div>
    <form action="{{ route('expenses.item.update') }}" id="formProduct" method="POST">
        @csrf
        <input type="hidden" value="{{ $exponseItem->id }}" name="id">
        <div class="card-body">
                <div class="mb-1">
                    <label class="form-label">اسم البند *</label>
                    <input type="text" class="form-control" value="{{ $exponseItem->name }}" required name="name">
                </div>
                <div class="mb-1">
                    <label class="form-label">هل يؤثر علي الخزنة ؟ *</label>
                    <select name="affect_wallet" class="form-select" id="affect_wallet">
                        <option value="1" selected>نعم</option>
                        <option value="0">لا</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">هل يؤثر علي المديونية ؟ *</label>
                    <select name="affect_debt" class="form-select" id="affect_debt">
                        <option value="1">نعم</option>
                        <option value="0" selected>لا</option>
                    </select>
                </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-relief-success">حفظ البيانات</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
     $(document).ready(function() {
        $('#affect_wallet').change(function() {
            if ($(this).val() == '1') {
                $('#affect_debt').val('0');
            }
        });

        $('#affect_debt').change(function() {
            if ($(this).val() == '1') {
                $('#affect_wallet').val('0');
            }
        });
    });
</script>
@endsection