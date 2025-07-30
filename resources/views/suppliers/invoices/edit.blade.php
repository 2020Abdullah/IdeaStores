@extends('layouts.app')

@section('css')
<style>
    .table input {
        width: 200px!important;
    }
</style>
@endsection

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">تعديل فاتورة مورد</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">بيانات الفاتورة</a>
                    </li>
                    <li class="breadcrumb-item active">
                        تعديل بيانات الفاتورة
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<section class="addInvoice">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">تعديل الفاتورة</h3>
        </div>
        <form action="{{ route('supplier.invoice.update') }}" id="invoiceForm" method="POST">
            @csrf
            <input type="hidden" value="{{ $invoice->id }}" name="id">
            <input type="hidden" value="{{ $invoice->total_amount }}" name="total_amount_old">
            @if ($invoice->invoice_type === 'cash')
                <input type="hidden" name="warehouse_id" value="{{ $invoice->warehouse_id }}">
                <input type="hidden" name="wallet_id" value="{{ $invoice->wallet_id }}">
            @endif
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label" for="name">المورد</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">أختر المورد ...</option>
                        @foreach ($suppliers_list as $supplier)
                            <option value="{{ $supplier->id }}" 
                                {{ (isset($invoice) && $invoice->supplier_id == $supplier->id) ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="alert alert-danger mt-1" role="alert">
                            <h4 class="alert-heading">خطأ</h4>
                            <div class="alert-body">
                                {{ @$message }}
                            </div>
                        </div>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label" for="phone">تاريخ الفاتورة</label>
                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ $invoice->invoice_date }}" name="invoice_date" />
                    @error('invoice_date')
                        <div class="alert alert-danger mt-1" role="alert">
                            <h4 class="alert-heading">خطأ</h4>
                            <div class="alert-body">
                                {{ @$message }}
                            </div>
                        </div>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label">نوع الفاتورة</label>
                    <input type="text" class="form-control" value="{{ $invoice->invoice_type }}" readonly name="invoice_type">
                </div>
                <div class="mb-2">
                    <a href="#" class="addItems btn-icon-content btn btn-success waves-effect waves-float waves-light">
                        <i data-feather='plus-circle'></i>
                        <span>إضافة صنف جديد</span> 
                    </a>
                </div>
                <div class="mb-2">  
                    <div class="table-items">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <th>الصنف</th>
                                    <th>المنتج</th>
                                    <th>وحدة القياس</th>
                                    <th>العرض</th>
                                    <th>سعر الشراء للوحدة</th>
                                    <th>السعر للمتر</th>
                                    <th>الطول</th>
                                    <th>الكمية</th>
                                    <th>الإجمالي</th>
                                    <th>حذف</th>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->items as $index => $item)
                                    <tr class="product-item">
                                        <td>
                                            <select class="select2 categorySelect" name="items[{{$index}}][category_id]">
                                                @foreach ($finalCategories as $cat)
                                                    @if ($cat->children->isEmpty()) 
                                                        <option value="{{ $cat->id }}" 
                                                            {{ $cat->id == $item->product->category_id ? 'selected' : '' }}>
                                                            {{ $cat->full_path }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 productSelect" name="items[{{$index}}][product_id]">
                                                @foreach ($products as $product)
                                                    @if ($product->category_id == $item->product->category_id)
                                                        <option value="{{ $product->id }}" 
                                                            {{ $product->id == $item->product_id ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 unitSelect" name="items[{{$index}}][unit_id]">
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}" 
                                                        {{ (isset($item) && $item->unit_id == $unit->id) ? 'selected' : '' }}>
                                                        {{ $unit->symbol }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td> 
                                        <td>
                                            <select class="select2 SizeSelect" name="items[{{$index}}][size_id]">
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}" 
                                                        {{ (isset($item) && $item->size_id == $size->id) ? 'selected' : '' }}>
                                                        {{ $size->width }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td><input type="number" name="items[{{$index}}][purchase_price]" value="{{ $item->purchase_price }}" class="form-control purchase_price" step="any"></td>

                                        <td><input type="number" name="items[{{$index}}][pricePerMeter]" value="{{ $item->pricePerMeter }}" class="form-control pricePerMeter" readonly step="any"></td>

                                        <td><input type="number" name="items[{{$index}}][length]" value="{{ $item->length }}" class="form-control length" readonly step="any"></td>

                                        <td><input type="number" name="items[{{$index}}][quantity]"class="form-control quantity" value="{{ $item->quantity }}" step="any"></td>

                                        <td><input type="number" name="items[{{$index}}][total_price]" value="{{ $item->total_price }}" class="form-control total_price" step="any" readonly></td>

                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i data-feather='trash-2'></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="all_total">
                        <span>إجمالي الفاتورة :</span>
                        <strong>{{ $invoice->total_amount - $invoice->cost_price }}</strong> 
                        <span>EGP</span>
                    </div>
                </div>
                <div id="costs-wrapper">
                    <button type="button" class="btn btn-primary waves-effect waves-float waves-light mb-1" id="add-cost">+ أضف تكلفة</button>    
                    @foreach ($invoice->costs as $index => $cost)
                        <div class="row cost-item mb-1">
                            <div class="col-md-6 mb-1">
                                <input type="text" name="costs[{{$index}}][description]" class="form-control costInfo" value="{{ $cost->description }}">
                            </div>
                            <div class="col-md-4 mb-1">
                                <input type="number" name="costs[{{$index}}][amount]" class="form-control costValue" value="{{ $cost->amount }}">
                            </div>
                            <div class="col-md-2 mb-1">
                                <button type="button" class="btn btn-danger remove-cost">حذف</button>
                            </div>
                        </div>
                    @endforeach            
                </div>
                <div class="mb-2">
                    <label class="form-label">مجموع التكاليف</label>
                    <input type="text" id="total-cost" class="form-control additional_cost @error('additional_cost') is-invalid @enderror" name="additional_cost" value="{{ $invoice->cost_price }}" readonly>
                    @error('additional_cost')
                        <div class="alert alert-danger mt-1" role="alert">
                            <h4 class="alert-heading">خطأ</h4>
                            <div class="alert-body">
                                {{ @$message }}
                            </div>
                        </div>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label">إجمالي الفاتورة شامل سعر التكلفة</label>
                    <input type="text" class="form-control total_amount" name="total_amount" value="{{ $invoice->total_amount }}" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">ملاحظات (اختيارى)</label>
                    <textarea class="form-control" cols="5" rows="5" name="notes">
                        {{ $invoice->notes }}
                    </textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-relief-success">حفظ الفاتورة</button>
            </div>
        </form>
    </div>
</section>


@endsection

@section('js')
<script>
$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('input', '.costValue', function () {
        calculateTotalCost();
        calculateTotalInvoice();
    });

    // إضافة التكاليف
    let costIndex = 1;
    $('#add-cost').click(function () {
        const newCost = `
            <div class="row cost-item mb-1">
                <div class="col-md-6 mb-1">
                    <input type="text" name="costs[${costIndex}][description]" class="form-control costInfo" placeholder="وصف التكلفة (مثلاً: شحن)">
                </div>
                <div class="col-md-4 mb-1">
                    <input type="number" name="costs[${costIndex}][amount]" class="form-control costValue" placeholder="القيمة (مثلاً: 300)">
                </div>
                <div class="col-md-2 mb-1">
                    <button type="button" class="btn btn-danger remove-cost">حذف</button>
                </div>
            </div>
        `;
        $('#costs-wrapper').append(newCost);
        costIndex++;
    });

    $(document).on('click', '.remove-cost', function () {
        $(this).closest('.cost-item').remove();
        calculateTotalCost();
        calculateTotalInvoice();
    });

    $(document).on('input', '.additional_cost,.unit_id, .quantity, .length ,.purchase_price, .SizeSelect', function () {
        let row = $(this).closest('tr');
        calculateTotalPerMM(row);
        calculateTotalPrice(row);
    });

    function calculateTotalCost(){
        let costtotal = 0;
        $('.costValue').each(function() {
            let costValue = parseInt($(this).val());
            costtotal += costValue;
        });
        $('#total-cost').val(costtotal);
        calculateTotalInvoice();
    }

    function calculateTotalPerMM(row) {
        let pricePerMM = 0;
        let category = row.find('.categorySelect');
        let unit = row.find('.unitSelect');
        
        let CatText = category.is('select') 
            ? category.find('option:selected').text() 
            : category.text();

        let symbol = unit.find('option:selected').text();

        let quantity = parseFloat(row.find('.quantity').val()) || 0;
        let length = parseFloat(row.find('.length').val()) || 0;
        let purchase_price = parseFloat(row.find('.purchase_price').val()) || 0;
        let size = parseFloat(row.find('.SizeSelect option:selected').text()) || 0;

        if(symbol === 'سم'){
            pricePerMM = size * purchase_price;
            row.find('.length').attr('readonly', false);
        }
        else {
            pricePerMM = 0;
            row.find('.length').attr('readonly', true);
        }

        row.find('.pricePerMeter').val(pricePerMM);  
    }

    function calculateTotalPrice(row){
        let total_price = 0;
        let unit = row.find('.unitSelect');
        let symbol = unit.find('option:selected').text();
        let length = parseFloat(row.find('.length').val()) || 0;
        let quantity = parseFloat(row.find('.quantity').val()) || 0;
        let pricePerMM = parseFloat(row.find('.pricePerMeter').val()) || 0;
        let purchase_price = parseFloat(row.find('.purchase_price').val()) || 0;

        if(pricePerMM !== 0){
            total_price = (length * pricePerMM) * quantity;
        }
        else {
            total_price = quantity * purchase_price;
        }
        row.find('.total_price').val(total_price);
        calculateTotalInvoice();
    }

    function calculateTotalInvoice() {
        let total_amount = 0;
        let all_total = 0;
        // جمع إجماليات كل صنف
        $('tr.product-item').each(function () {
            let price = parseFloat($(this).find('.total_price').val().replace(/,/g, '')) || 0;
            total_amount += price;
            all_total += price;
        });

        // جمع التكلفة الإضافية
        let additionalCost = parseInt($('.additional_cost').val().replace(/,/g, '')) || 0;
        total_amount += additionalCost;

        // عرض النتيجة
        $('.total_amount').val(total_amount);
        $('.all_total strong').text(all_total);
    }

    // جلب المنتجات بناء علي التصنيف
    function fetchProduct(categoryId, select){
        $.ajax({
            url: '{{ route("getProducts") }}',
            method: 'POST',
            data: {
                category_id: categoryId
            },
            success: function(response) {
                select.empty().append(`<option value="">اختر منتج</option>`);
                response.data.forEach(item => {
                    select.append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    }

    // عند تغيير التصنيف الحالي
    $(document).on('change', '.categorySelect', function(){
        const categoryId = $(this).val();
        const select = $(this).closest('tr').find('.productSelect');

        if (categoryId) {
            fetchProduct(categoryId, select);
        }
        else {
            select.empty().append(`<option value="">اختر منتج</option>`);
        }
    })


    // إضافة صنف جديد
    $(document).on('click', '.addItems', function () {
        // حساب رقم الصف الجديد
        let index = $('.product-item').length;

        // HTML الخاص بالصف الجديد
        let newRow = `
            <tr class="product-item">
                <td>
                    <select class="select2 categorySelect" name="items[${index}][category_id]">
                        <option value="">جاري التحميل...</option>
                    </select>
                </td>
                <td>
                    <select class="select2 productSelect" name="items[${index}][product_id]">
                        <option value="">اختر منتج</option>
                    </select>
                </td>
                <td>
                    <select class="select2 unitSelect" name="items[${index}][unit_id]">
                        <option value="">جاري التحميل...</option>
                    </select>
                </td> 
                <td>
                    <select class="select2 SizeSelect" name="items[${index}][size_id]">
                        <option value="">اختر المقاس</option>
                    </select>
                </td>
                <td><input type="number" name="items[${index}][purchase_price]"  class="form-control purchase_price" step="any"></td>
                <td><input type="number" name="items[${index}][pricePerMeter]" value="0"  class="form-control pricePerMeter" step="any" readonly></td>
                <td><input type="number" name="items[${index}][length]"class="form-control length" value="0" step="any"></td>
                <td><input type="number" name="items[${index}][quantity]"class="form-control quantity" value="1" step="any"></td>
                <td><input type="number" name="items[${index}][total_price]" class="form-control total_price" step="any" readonly></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i data-feather='trash-2'></i>
                    </button>
                </td>
            </tr>
        `;

        // إضافة الصف إلى الجدول
        $('.table-responsive tbody').append(newRow);

        // تفعيل أيقونات feather
        feather.replace();

        // جلب التصنيفات
        $.get('{{ route("getAllHierarchicalCategories") }}', function(response) {
            const lastCategorySelect = $('.categorySelect').last();
            if (response.status) {
                lastCategorySelect.empty().append(`<option value="">اختر تصنيف</option>`);
                response.data.forEach(item => {
                    lastCategorySelect.append(`<option value="${item.id}">${item.full_path}</option>`);
                });
            } else {
                lastCategorySelect.html('<option>حدث خطأ في جلب التصنيفات</option>');
            }
        });

        // جلب الوحدات
        $.get('{{ route("getUnits") }}', function(response) {
            const lastUnitSelect = $('.unitSelect').last();
            if (response.status) {
                lastUnitSelect.empty().append(`<option value="">اختر الوحدة</option>`);
                response.data.forEach(item => {
                    lastUnitSelect.append(`<option value="${item.id}">${item.symbol}</option>`);
                });
            } else {
                lastUnitSelect.html('<option>حدث خطأ في جلب الوحدات</option>');
            }
        });

        // جلب المقاسات
        $.get('{{ route("getSizes") }}', function(response) {
            lastSizeSelect = $(".SizeSelect").last();
            if (response.status) {
                lastSizeSelect.empty().append(`<option value="">اختر المقاس</option>`);
                response.data.forEach(item => {
                    lastSizeSelect.append(`<option value="${item.id}" data-width="${item.width}">${item.width}</option>`);
                });
            } else {
                lastSizeSelect.html('<option>حدث خطأ في جلب المقاسات</option>');
            }
        });

        $('.table-responsive tbody .select2').select2({
            dir: "rtl",
            width: '200px',
        });

    });
    
    // حذف الصف
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        calculateTotalInvoice();
        feather.replace();
    });


    $('#invoiceForm').on('submit', function(e) {
        e.preventDefault(); // منع الإرسال مؤقتًا

        // تأكيد من المستخدم
        if (confirm("هل أنت متأكد من حفظ البيانات؟")) {
            this.submit();
        } 
    });

    let isFormChanged = false;

    // راقب الحقول إذا المستخدم غيّر أي حاجة
    $('form input, form select, form textarea').on('change input', function () {
        isFormChanged = true;
    });

    // راقب الروابط داخل الـ sidebar أو التابات
    $('.nav-item a').on('click', function (e) {
        if (isFormChanged) {
            e.preventDefault(); // منع التنقل

            if (confirm("لديك تغييرات غير محفوظة، هل أنت متأكد من مغادرة الصفحة؟")) {
                isFormChanged = false;
                window.location.href = $(this).attr('href');
            }
        }
    });

    // مغادرة الصفحة (مثل إعادة تحميل أو إغلاق التبويب)
    window.onbeforeunload = function () {
        if (isFormChanged) {
            return "لديك تغييرات غير محفوظة، هل تريد فعلاً مغادرة الصفحة؟";
        }
    };

});
</script>

@endsection 