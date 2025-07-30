@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">التصنيفات</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">التصنيفات</a>
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
        <h3 class="card-title">التصنيفات</h3>
        <div class="card-action">
            <button type="button" class="btn btn-success waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addcate">
                إضافة تصنيف 
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>اسم التصنيف</th>
                    <th>نوع التصنيف</th>
                    <th>متفرع من</th>
                    <th>إجراء</th>
                </tr>
            
                @forelse ($category_list as $c)
                    <tr>
                        <td>{{ $c->name }}</td>
            
                        <td>
                            @if ($c->parent_id)
                                <span class="badge bg-secondary">فرعي</span>
                            @else
                                <span class="badge bg-primary">رئيسي</span>
                            @endif
                        </td>

                        <td>
                            @if ($c->parent)
                                {{ $c->parent->name }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
            
                        <td>

                            @if ($c->parent)
                                <a href="#" data-bs-toggle="modal" data-bs-target="#editcate"
                                data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-parent_id="{{ $c->parent->id }}"
                                class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn"
                                title="تعديل">
                                    <i data-feather='edit'></i>
                                </a>
                            @else
                                <a href="#" data-bs-toggle="modal" data-bs-target="#editcate"
                                data-id="{{ $c->id }}" data-name="{{ $c->name }}"
                                class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn"
                                title="تعديل">
                                    <i data-feather='edit'></i>
                                </a>
                            @endif
            
            
                            <a href="#" data-bs-toggle="modal" data-bs-target="#delcate"
                               data-id="{{ $c->id }}"
                               class="btn btn-icon btn-danger waves-effect waves-float waves-light delBtn"
                               title="حذف">
                                <i data-feather='trash-2'></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="3">لا يوجد تصنيفات مضافة</td>
                    </tr>
                @endforelse
            </table>            
        </div>
    </div>
</div>

@include('category.models')
@endsection

@section('js')
<script>
    $(function(){
        $(document).on('click', '.editBtn', function(){
            let id = $(this).data('id');
            let name = $(this).data('name');
            let parent_id = $(this).data('parent_id');

            $("#editcate input[name='id']").val(id);
            $("#editcate input[name='name']").val(name);
            $("#editcate select[name='parent_id']").val(parent_id);
        });

        $(document).on('click', '.delBtn', function(){
            let id = $(this).data('id');
            $("#delcate input[name='id']").val(id);
        });
    })
</script>
@endsection