@extends('layouts.app')

@section('content')
<div class="dashboard">
    <h2>إحصائيات</h2>
    <div class="row text-center">
        <div class="col-md-6">
            <div class="card">
                <a href="{{ route('supplier.index') }}">
                    <div class="card-body">
                        <h4 class="mb-2">الموردين</h4>
                        <h3>{{ $suppliersCount }}</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <a href="{{ route('supplier.invoice.index') }}">
                    <div class="card-body">
                        <h4 class="mb-2">فواتير الموردين</h4>
                        <h3>{{ $invoicesCount }}</h3>
                    </div>
                </a>
            </div>
        </div>
        {{-- <div class="col-md-6">
            <div class="card">
                <a href="#">
                    <div class="card-body">
                        <h4 class="mb-2">الموردين</h4>
                        <h3>0</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <a href="#">
                    <div class="card-body">
                        <h4 class="mb-2">المبيعات</h4>
                        <h3>0</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <a href="#">
                    <div class="card-body">
                        <h4 class="mb-2">المصروفات</h4>
                        <h3>0</h3>
                    </div>
                </a>
            </div>
        </div> --}}
    </div>
</div>
@endsection
