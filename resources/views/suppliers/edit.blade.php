@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">تعديل المورد </h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">الموردين</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">تعديل المورد</a>
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
        <h3 class="card-title">تعديل المورد</h3>
    </div>
    <form action="{{ route('supplier.update') }}" id="formProduct" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $supplier->id }}">
        <div class="card-body">
                <div class="mb-1">
                    <label class="form-label">اسم المورد *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $supplier->name }}" name="name">
                    @error('name')
                        <div class="alert alert-danger mt-1">
                            <p>{{ @$message }}</p>
                        </div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label class="form-label">رقم الهاتف (اختيارى)</label>
                    <input type="text" class="form-control" value="{{ $supplier->phone }}" name="phone">
                </div>
                <div class="mb-1">
                    <label class="form-label">رقم الواتساب (اختيارى)</label>
                    <input type="text" class="form-control" value="{{ $supplier->whatsUp }}" name="whatsUp">
                </div>
                <div class="mb-1">
                    <label class="form-label">اسم الشركة (اختيارى)</label>
                    <input type="text" class="form-control" value="{{ $supplier->busniess_name }}" name="busniess_name">
                </div>
                <div class="mb-1">
                    <label class="form-label">نوع النشاط (اختيارى)</label>
                    <input type="text" class="form-control" value="{{ $supplier->busniess_type }}" name="busniess_type">
                </div>
                <div class="mb-1">
                    <label class="form-label">المكان (اختيارى)</label>
                    <input type="text" class="form-control" value="{{ $supplier->place }}" name="place">
                </div>
                <div class="mb-1">
                    <label class="form-label">ملاحظات (اختيارى)</label>
                    <textarea name="notes" class="form-control" cols="5" rows="5">{{ $supplier->notes }}</textarea>
                </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-relief-success">حفظ البيانات</button>
        </div>
    </form>
</div>

@endsection

