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
    <form action="{{ route('expenses.item.update') }}" class="formSubmit" method="POST">
        @csrf
        <input type="hidden" value="{{ $exponseItem->id }}" name="id">
        <div class="card-body">
            <div class="mb-1">
                <label class="form-label">اسم البند *</label>
                <input type="text" class="form-control name" value="{{ $exponseItem->name }}" required name="name">
            </div>
            <div class="mb-1">
                <label class="form-label">هل له نصيب في الربحية ؟ *</label>
                <select name="is_profit" class="form-select" id="is_profit">
                    <option value="0" selected>لا</option>
                    <option value="1">نعم</option>
                </select>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btnSubmit btn btn-relief-success">حفظ البيانات</button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
     $(document).ready(function() {
        $(document).on('submit', '.formSubmit', function(e){
            e.preventDefault();
            if(!$(this).find('.name').val()){
                e.preventDefault();
                toastr.info('يرجي ملئ بيانات الحقول !');
            }
            else {
                $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
                e.currentTarget.submit();
            }
        });
    });
</script>
@endsection