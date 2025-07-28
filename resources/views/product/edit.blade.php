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
        <input type="hidden" name="final_category_id" id="final_category_id">
        <input type="hidden" name="id" value="{{ $product->id }}">
        <div class="card-body">
                <div class="mb-1">
                    <label class="form-label">التصنيف الرئيسي</label>
                    <select name="main_categories" class="form-select @error('main_categories') is-invalid @enderror" id="main_category">
                        @foreach ($main_categories as $c)
                            <option value="{{ $c->id }}" 
                                {{ $product->category && $product->category->parent_id == $c->id ? 'selected' : ($product->category && $product->category->id == $c->id && !$product->category->parent_id ? 'selected' : '') }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('main_categories')
                        <div class="alert alert-danger">
                            <p>{{ @$message }}</p>
                        </div>
                    @enderror
                </div>
                <div class="form-group" id="sub_category_container" style="display: none;">
                    <label for="category_id">التصنيف الفرعي</label>
                    <select name="category_id" id="sub_category" class="form-control">
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">وحدة القياس</label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" id="unit_id">
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
                    <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $product->name }}" name="name">
                    @error('name')
                        <div class="alert alert-danger">
                            <p>{{ @$message }}</p>
                        </div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label class="form-label">العرض</label>
                    <input type="text" class="form-control @error('width') is-invalid @enderror" value="{{ $product->width }}" name="width">
                    @error('width')
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

@section('js')
<script>
$(document).ready(function() {
    // تأكد وجود div#category_selectors أو أنشئه داخل formProduct
    let $selectorsWrapper = $('#category_selectors');
    if ($selectorsWrapper.length === 0) {
        $selectorsWrapper = $('<div id="category_selectors" class="mb-1"></div>');
        // انقل select الرئيسي داخله مع حذف القديم
        const $mainCategory = $('#main_category');
        $mainCategory.parent().append($selectorsWrapper);
        $selectorsWrapper.append($mainCategory);
    }

    const $finalCategoryInput = $('#final_category_id');
    const productCategory = @json($product->category);

    function loadSubcategories($select) {
        const selectedId = $select.val();
        const currentLevel = parseInt($select.data('level'));

        // حدّث الحقل المخفي final_category_id
        $finalCategoryInput.val(selectedId || '');

        // امسح التفرعات بعد هذا المستوى
        $selectorsWrapper.find('select.category-level').each(function() {
            if (parseInt($(this).data('level')) > currentLevel) {
                $(this).parent().remove();
            }
        });

        if (!selectedId) return;

        $.ajax({
            url: '{{ route("getSubcategories") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                category_id: selectedId
            },
            success: function(response) {
                if (response.status && response.data.length > 0) {
                    const nextLevel = currentLevel + 1;
                    const $newSelectWrapper = $('<div class="mt-2"></div>');
                    const options = response.data.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
                    const $newSelect = $(`<select class="form-control category-level" data-level="${nextLevel}"><option value="">اختر التصنيف الفرعي...</option>${options}</select>`);

                    $newSelectWrapper.append($newSelect);
                    $selectorsWrapper.append($newSelectWrapper);

                    $newSelect.on('change', function() {
                        loadSubcategories($(this));
                    });
                }
            },
            error: function() {
                console.error('خطأ في جلب التفرعات');
            }
        });
    }

    // ضبط select الرئيسي
    const $mainCategorySelect = $('#main_category');
    $mainCategorySelect.addClass('category-level').attr('data-level', '1');

    $mainCategorySelect.on('change', function() {
        loadSubcategories($(this));
    });

    // بناء التفرعات في حالة وجود تصنيف في المنتج
    if (productCategory) {
        if (productCategory.parent_id) {
            $mainCategorySelect.val(productCategory.parent_id);
            $finalCategoryInput.val(productCategory.id);

            $.ajax({
                url: '{{ route("getSubcategories") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    category_id: productCategory.parent_id
                },
                success: function(response) {
                    if (response.status && response.data.length > 0) {
                        const nextLevel = 2;
                        const $newSelectWrapper = $('<div class="mt-2"></div>');
                        const options = response.data.map(cat => {
                            return `<option value="${cat.id}" ${cat.id == productCategory.id ? 'selected' : ''}>${cat.name}</option>`;
                        }).join('');
                        const $newSelect = $(`<select class="form-control category-level" data-level="${nextLevel}"><option value="">اختر التصنيف الفرعي...</option>${options}</select>`);

                        $newSelectWrapper.append($newSelect);
                        $selectorsWrapper.append($newSelectWrapper);

                        $newSelect.on('change', function() {
                            loadSubcategories($(this));
                        });
                    }
                },
                error: function() {
                    console.error('خطأ في جلب التفرعات');
                }
            });
        } else {
            $mainCategorySelect.val(productCategory.id);
            $finalCategoryInput.val(productCategory.id);
        }
    }
});
</script>
@endsection