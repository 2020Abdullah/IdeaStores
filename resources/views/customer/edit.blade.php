@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">{{ $customer->name }}</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.index') }}">العملاء</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">تعديل بيانات العميل </a>
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
        <h3 class="card-title">تعديل بيانات العميل</h3>
    </div>
    <form action="{{ route('customer.update') }}" id="formSubmit" method="POST">
        @csrf
        <input type="hidden" value="{{ $customer->id }}" name="id">
        <div class="card-body">
                <div class="mb-1">
                    <label class="form-label">اسم العميل *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="اسم العميل" name="name" value="{{ $customer->name ?? ''}}" required>
                    @error('name')
                        <div class="alert alert-danger mt-1">
                            <p>{{ @$message }}</p>
                        </div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label class="form-label">رقم الهاتف (اختيارى)</label>
                    <input type="text" class="form-control" placeholder="رقم الهاتف" name="phone" value="{{ $customer->phone}}">
                </div>
                <div class="mb-1">
                    <label class="form-label">رقم الواتساب (اختيارى)</label>
                    <input type="text" class="form-control" placeholder="رقم الواتساب" name="whatsUp" value="{{ $customer->whatsUp}}">
                </div>
                <div class="mb-1">
                    <label class="form-label">اسم الشركة (اختيارى)</label>
                    <input type="text" class="form-control" placeholder="اسم الشركة" name="busniess_name" value="{{ $customer->busniess_name}}">
                </div>
                <div class="mb-1">
                    <label class="form-label">نوع النشاط (اختيارى)</label>
                    <input type="text" class="form-control" placeholder="نشاط الشركة" name="busniess_type" value="{{ $customer->busniess_type ?? ''}}">
                </div>
                <div class="mb-1">
                    <label class="form-label">المكان (اختيارى)</label>
                    <input type="text" class="form-control" placeholder="مقر العميل" name="place" value="{{ $customer->place ?? ''}}">
                </div>
                <div class="mb-1">
                    <label class="form-label">ملاحظات (اختيارى)</label>
                    <textarea name="notes" class="form-control" cols="5" rows="5">{{ $customer->notes ?? ''}}</textarea>
                </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-relief-success">حفظ البيانات</button>
        </div>
    </form>
</div>
@endsection

