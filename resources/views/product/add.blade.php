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
    <form action="{{ route('product.store') }}" id="formProduct" method="POST">
        @csrf
        <input type="hidden" name="final_category_id" id="final_category_id">
        <div class="card-body">
                <div class="mb-1">
                    <div id="category_selectors">
                        <select class="form-control category-level" data-level="0" required>
                            <option value="">اختر التصنيف الرئيسي...</option>
                            @foreach ($main_categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>  
                    @error('final_category_id')
                        <div class="alert alert-danger">
                            <p>{{ @$message }}</p>
                        </div>
                    @enderror
                </div>
                <div class="mb-1">
                    <label class="form-label">وحدة القياس</label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" required>
                        <option value="" selected>اختر الوحدة ...</option>                        
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
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" required>
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

@section('js')
<script>
    $(function(){
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('category-level')) {
                loadSubcategories(e.target);
                document.getElementById('final_category_id').value = e.target.value || '';
            }
        });

        function loadSubcategories(selectElement) {
            const selectedId = selectElement.value;
            const currentLevel = parseInt(selectElement.dataset.level);

            // امسح كل التفرعات بعد هذا المستوى
            document.querySelectorAll('#category_selectors .category-level').forEach(el => {
                if (parseInt(el.dataset.level) > currentLevel) {
                    el.parentElement.remove();
                }
            });

            if (!selectedId) return;

            // أرسل AJAX لطلب التفرعات
            fetch('{{ route("getSubcategories") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ category_id: selectedId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status && data.data.length > 0) {
                    const nextLevel = currentLevel + 1;
                    const newSelect = document.createElement('div');
                    newSelect.innerHTML = `
                        <select class="form-control mt-2 category-level" data-level="${nextLevel}" onchange="loadSubcategories(this)">
                            <option value="">اختر التصنيف الفرعي...</option>
                            ${data.data.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('')}
                        </select>
                    `;
                    document.getElementById('category_selectors').appendChild(newSelect);
                }
                // لو لا يوجد تفرعات: لا تفعل شيئاً
            });
        }
    })
</script>
@endsection