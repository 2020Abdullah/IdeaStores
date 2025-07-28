@extends('layouts.app')

@section('css')
<style>
    .card-balance {
        padding: 10px;
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        text-align: center;
    }
    .card-balance h3 {
        padding: 10px;
    }
</style>
@endsection

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
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">إضافة خزنة</h3>
            </div>
            <form method="POST" action="{{ route('warehouse.store') }}">
                @csrf
                <input type="hidden" value="1" name="is_main">
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
                    <div class="mb-2">
                        <label class="form-label" for="balance_start">رصيد أول المدة</label>
                        <input type="number" class="form-control" name="balance_start" value="0">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-relief-success">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </section>
@endsection