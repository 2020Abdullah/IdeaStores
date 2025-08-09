@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">المصروفات</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">بنود المصروفات</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- show all errors -->
@if ($errors->any())
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger">
            <div class="alert-body">
                <span>{{$error}}</span>
            </div>
        </div>
    @endforeach
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">بنود المصروفات</h3>
        <div class="card-action">
            <a href="{{ route('expenses.item.add') }}" class="btn btn-success waves-effect waves-float waves-light">
                إضافة بند مصروف
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="expenses-table">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>بند المصروف</th>
                    <th>الرصيد</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenseItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('expenses.item.show', $item->id) }}">
                                {{ $item->name }}
                            </a>
                        </td>
                        <td>{{ number_format($item->exponses->sum('amount') ?? 0, 2) }}</td>
                        <td>
                            <a href="{{ route('expenses.item.edit', $item->id) }}"class="btn btn-icon btn-success waves-effect waves-float waves-light">
                                <i data-feather='edit'></i>
                                <span>تعديل</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">لا توجد بنود</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
