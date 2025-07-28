@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">إضافة مخزن</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">بيانات الخزن</a>
                    </li>
                    <li class="breadcrumb-item active">
                        إضافة مخزن جديد
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <section class="addStore">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">إضافة مخزن جديد</h3>
            </div>
            <form action="{{ route('storesHouse.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label" for="name">اسم المخزن</label>
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
                    <div class="mb-2">
                        <label class="form-label" for="phone">رقم الهاتف</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" />
                        @error('phone')
                            <div class="alert alert-danger mt-1" role="alert">
                                <h4 class="alert-heading">خطأ</h4>
                                <div class="alert-body">
                                    {{ @$message }}
                                </div>
                            </div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="address">العنوان</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" />
                        @error('address')
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
    </section>
@endsection