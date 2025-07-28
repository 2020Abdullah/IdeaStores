@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">المنتجات</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">المنتجات</a>
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
        <h3 class="card-title">المنتجات</h3>
        <div class="card-action">
            <a href="{{ route('product.add') }}" class="btn btn-success waves-effect waves-float waves-light">
                إضافة منتج جديد 
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>المنتج</th>
                    <th>التصنيف</th>
                    <th>الوحدة</th>
                    <th>العرض</th>
                    <th>إجراء</th>
                </tr>
                @forelse ($products as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td>
                            {{ $p->category?->parent?->name ?? $p->category?->name ?? 'لا يوجد' }}
                            / 
                           @if ($p->category && $p->category->parent)
                               {{ $p->category->name }}
                           @else
                               لا يوجد
                           @endif
                        </td>
                        <td>{{ $p->unit->symbol }}</td>
                        <td>{{ $p->width }}</td>
                        <td>
                            <a href="{{ route('product.edit', $p->id) }}" class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn">
                                <i data-feather='edit'></i>
                            </a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#delProduct" data-id="{{ $p->id }}" class="btn btn-icon btn-danger waves-effect waves-float waves-light delBtn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i data-feather='trash-2'></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="6">لا يوجد منتجات مضافة</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
</div>

<!-- model delete product -->
<div class="modal fade text-start modal-danger" id="delProduct" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحذير !</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('product.delete') }}" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <div class="modal-body">
                    <label class="form-label">هل أنت متأكد من حذف المنتج سيتم حذف كل شئ مرتبط به ؟</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger waves-effect waves-float waves-light">تأكيد الحذف</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(function(){
        $(document).on('click', '.delBtn', function(){
            let id = $(this).data('id');
            $("#delProduct input[name='id']").val(id);
        });
    })
</script>
@endsection
