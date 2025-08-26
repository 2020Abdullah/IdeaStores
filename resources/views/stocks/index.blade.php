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
            <h2 class="content-header-title float-start mb-0">مخزن رئيسي</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">ستوك المخزن</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <section class="storeHouse">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">المخزون</h3>
                {{-- <div class="card-action">
                    <a href="#" class="btn btn-relief-success" data-bs-toggle="modal" data-bs-target="#addProduct">
                        <span>إضافة إلي المخزون</span>
                    </a>
                </div> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>التاريخ</th>
                            <th>التصنيف</th>
                            <th>المنتج</th>
                            <th>العرض</th>
                            <th>الكمية الواردة</th>
                            <th>الكمية المتبقية</th>
                            <th>عرض الحركات</th>
                        </tr>
                        @foreach ($stocks as $stock)
                            <tr>
                                <td>{{ $stock->date }}</td>
                                <td>{{ $stock->category->fullPath() }}</td>
                                <td>{{ $stock->product->name }}</td>
                                <td>{{ $stock->size->width }}</td>
                                <td>{{ $stock->initial_quantity }} {{ $stock->unit->name }}</td>
                                <td>{{ $stock->movements->sum('quantity') }} {{ $stock->unit->name }}</td>
                                <td>
                                    <a href="{{ route('stock.show', $stock->id) }}" class="btn btn-info waves-effect">
                                        <i data-feather='eye'></i>
                                        <span>حركات الصنف</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $stocks->links() }}
            </div>
        </div>
    </section>
@endsection
