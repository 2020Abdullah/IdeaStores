@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">معلومات عن المورد</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">بيانات المورد</a>
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
            <h3 class="card-title">المورد : {{ $supplier->name }}</h3>
        </div>
        <hr />
        <div class="card-body">
            <div class="row">
                <div class="col-6 mb-1">
                    <label class="form-label">المورد ID</label>
                    <input type="text" class="form-control" value="{{ $supplier->id }}" readonly>
                </div>
                <div class="col-6 mb-1">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control" value="{{ $supplier->phone ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-6 mb-1">
                    <label class="form-label">رقم الواتساب</label>
                    <input type="text" class="form-control" value="{{ $supplier->whatsUp ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-6 mb-1">
                    <label class="form-label">اسم الشركة</label>
                    <input type="text" class="form-control" value="{{ $supplier->busniess_name ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-6 mb-1">
                    <label class="form-label">نشاط الشركة</label>
                    <input type="text" class="form-control" value="{{ $supplier->busniess_type ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-6">
                    <label class="form-label">مقر الشركة</label>
                    <input type="text" class="form-control" value="{{ $supplier->place ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-12">
                    <label class="form-label">ملاحظات</label>
                    <textarea class="form-control" cols="5" rows="5" readonly>{{ $supplier->notes }}</textarea>
                </div>
            </div>
        </div>
    </div>
@endsection


