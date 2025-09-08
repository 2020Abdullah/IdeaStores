@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">إضافة منتج</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">المنتجات</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">إضافة منتج جديد</a>
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
        <h3 class="card-title">إضافة منتج جديد</h3>
    </div>
    <form action="{{ route('product.store') }}" id="formSubmit" method="POST">
        @csrf
        <div class="card-body">
                <div class="mb-1">
                    <div id="category_selectors">
                        <select class="form-select select2 categorySelect" name="final_category_id" required>
                        </select>
                    </div>  
                    @error('final_category_id')
                        <div class="alert alert-danger">
                            <p>{{ @$message }}</p>
                        </div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label class="form-label">اسم المنتج</label>
                    <input type="text" class="form-control name @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
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
    $(function(){
        function getCategory(){
            let categorySelect = $('.categorySelect');
            $.get('{{ route("getAllHierarchicalCategories") }}', function(response) {
                if (response.status) {
                    categorySelect.empty().append(`<option value="">اختر تصنيف</option>`);
                    response.data.forEach(item => {
                        categorySelect.append(`<option value="${item.id}">${item.full_path}</option>`);
                    });
                } else {
                    categorySelect.html('<option>حدث خطأ في جلب التصنيفات</option>');
                }
            });
        }
        getCategory();

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
        })

    })

</script>
<script>
    $('.select2').select2({
       dir: "rtl",
       width: '100%'
   });
</script>
@endsection