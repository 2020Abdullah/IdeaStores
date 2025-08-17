@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">كل المقاسات</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">المقاسات المتوفرة</a>
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
        <h3 class="card-title">المقاسات</h3>
        <div class="card-action">
            <button type="button" class="btn btn-success waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addSize">
                إضافة مقاس 
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>المقاس</th>
                </tr>
                @forelse ($sizes as $s)
                    <tr>
                        <td>{{ $s->width }}</td>
                        <td>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#editSize" data-id="{{ $s->id }}" data-width="{{ $s->width }}" class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn">
                                <i data-feather='edit'></i>
                            </a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#delSize" data-id="{{ $s->id }}" class="btn btn-icon btn-danger waves-effect waves-float waves-light delBtn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i data-feather='trash-2'></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="2">لا يوجد مقاسات مضافة</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
</div>

@include('sizes.models')
@endsection

@section('js')
<script>
    $(function(){
        $(document).on('click', '.editBtn', function(){
            let id = $(this).data('id');
            let width = $(this).data('width');

            $("#editSize input[name='id']").val(id);
            $("#editSize input[name='width']").val(width);
        });

        $(document).on('click', '.delBtn', function(){
            let id = $(this).data('id');
            $("#delSize input[name='id']").val(id);
        });

        $(document).on('submit', '.formSubmit', function(e){
            e.preventDefault();
            if(!$(this).find('.width').val()){
                e.preventDefault();
                toastr.info('يرجي ملئ بيانات الحقول !');
            }
            else {
                $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
                e.currentTarget.submit();
            }
        });

        $(document).on('submit', '.submitDel', function(e){
            e.preventDefault();
            $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
            e.currentTarget.submit();
        });
    })
</script>
@endsection