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
    <form action="{{ route('product.update') }}" id="formSubmit" method="POST">
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
            <button type="submit" class="btn btn-relief-success btnSubmit">حفظ المنتج</button>
        </div>
    </form>
</div>

@endsection

@section('js')
<script>
    $(document).ready(function(){
        $(document).on('submit', '#formSubmit', function(e){
            e.preventDefault();
            if($(this).find('.name').val() == '' && !$(this).find('.categorySelect').val() == ''){
                e.preventDefault();
                toastr.info('يرجي ملئ بيانات الحقول !');
            }
            else {
                $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
                e.currentTarget.submit();
            }
        });

        $('.select2').select2({
           dir: "rtl",
           width: '100%'
       });

    })
</script>
@endsection