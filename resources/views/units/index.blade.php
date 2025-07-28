@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">وحدات القياس</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">وحدات القياس</a>
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
        <h3 class="card-title">وحدات القياس</h3>
        <div class="card-action">
            <button type="button" class="btn btn-success waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addUnits">
                إضافة وحدة 
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>اسم الوحدة</th>
                    <th>رمز الوحدة</th>
                </tr>
                @forelse ($units as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->symbol }}</td>
                        <td>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#editUnits" data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-symbol="{{ $u->symbol }}" class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn">
                                <i data-feather='edit'></i>
                            </a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#delUnits" data-id="{{ $u->id }}" class="btn btn-icon btn-danger waves-effect waves-float waves-light delBtn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i data-feather='trash-2'></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="2">لا يوجد وحدات مضافة</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
</div>

@include('units.models')
@endsection

@section('js')
<script>
    $(function(){
        $(document).on('click', '.editBtn', function(){
            let id = $(this).data('id');
            let name = $(this).data('name');
            let symbol = $(this).data('symbol');

            $("#editUnits input[name='id']").val(id);
            $("#editUnits input[name='name']").val(name);
            $("#editUnits input[name='symbol']").val(symbol);
        });

        $(document).on('click', '.delBtn', function(){
            let id = $(this).data('id');
            $("#delUnits input[name='id']").val(id);
        });
    })
</script>
@endsection