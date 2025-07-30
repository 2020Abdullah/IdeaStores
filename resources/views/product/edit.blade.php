@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">تعديل منتج</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">تعديل المنتج</a>
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
        <h3 class="card-title">تعديل المنتج</h3>
    </div>
    <form action="{{ route('product.update') }}" id="formProduct" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $product->id }}">
        <div class="card-body">
                <div class="mb-1">
                    <label class="form-label">التصنيف</label>
                    <select name="final_category_id" class="form-select @error('final_category_id') is-invalid @enderror" id="main_category" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->full_path }}
                            </option>
                        @endforeach
                    </select>
                    @error('final_category_id')
                        <div class="alert alert-danger">
                            <p>{{ @$message }}</p>
                        </div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label class="form-label">وحدة القياس</label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" required>
                        <option value="{{ $product->unit->id }}" selected>{{ $product->unit->name }}</option>                        
                        @foreach ($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} - {{ $u->symbol }}</option>                        
                        @endforeach
                    </select>
                    @error('unit_id')
                        <div class="alert alert-danger">
                            <p>{{ @$message }}</p>
                        </div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label class="form-label">اسم المنتج</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $product->name }}" name="name" required>
                    @error('name')
                        <div class="alert alert-danger">
                            <p>{{ @$message }}</p>
                        </div>
                    @enderror
                </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-relief-success">حفظ المنتج</button>
        </div>
    </form>
</div>

@endsection
