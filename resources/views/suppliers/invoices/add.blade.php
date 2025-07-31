@extends('layouts.app')

@section('css')
<style>
    .table input, .table select {
        width: 200px!important;
    }
</style>
@endsection

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">إضافة فاتورة مورد</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">بيانات الفاتورة</a>
                    </li>
                    <li class="breadcrumb-item active">
                        إضافة فاتورة جديدة
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
            <h3 class="card-title">إضافة فاتورة مورد</h3>
        </div>
        <form action="{{ route('supplier.invoice.store') }}" id="invoiceForm" method="POST">
            @csrf
            <input type="hidden" name="method" class="method">
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label" for="name">المورد</label>
                    @if (isset($supplier))
                        <input type="hidden" class="form-control" name="supplier_id" value="{{ $supplier->id }}" required>
                        <input type="text" class="form-control" value="{{ $supplier->name }}" readonly>
                    @else
                        <select name="supplier_id" class="form-select">
                            <option value="">أختر المورد ...</option>
                            @foreach ($suppliers_list as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    @endif
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
                    <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" name="invoice_date" required/>
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
                    <select name="invoice_type" class="form-select invoice_type @error('invoice_type') is-invalid @enderror" required>
                        <option value="">اختر ...</option>
                        <option value="cash">كاش</option>
                        <option value="credit">آجل</option>
                        <option value="opening_balance">رصيد افتتاحي</option>
                    </select>
                    @error('invoice_type')
                        <div class="alert alert-danger mt-1" role="alert">
                            <h4 class="alert-heading">خطأ</h4>
                            <div class="alert-body">
                                {{ @$message }}
                            </div>
                        </div>
                    @enderror
                </div>
                <div class="mb-1 warehouse_container" style="display: none;">
                    <label class="form-label">من حساب</label>
                    <select name="warehouse_id" class="form-control warehouse_id">
                        <option value="">اختر الخزنة ...</option>
                        @foreach ($warehouse_list as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>              
                        @endforeach
                    </select>
                </div>
                <div class="mb-1 wallet_container" style="display: none;">
                    <label class="form-label">المحفظة</label>
                    <select name="wallet_id" class="form-control wallet_id">
                        <option value="">...</option>
                    </select>
                </div>
                <div class="mb-1 balance_container" style="display: none;">
                    <label class="form-label current_balance_label"></label>
                    <input type="hidden" class="form-control current_balance" name="current_balance" readonly>
                </div>
                <div class="mb-1 opening_balance_container" style="display: none;">
                    <label class="form-label">قيمة الرصيد الإفتتاحي</label>
                    <input type="number" class="form-control opening_balance_value" value="0" name="opening_balance_value">
                </div>
                <div class="mb-2">
                    <button type="button" class="addItems btn-icon-content btn btn-success waves-effect waves-float waves-light">
                        <i data-feather='plus-circle'></i>
                        <span>إضافة صنف جديد</span> 
                    </button>
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
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="all_total">
                        <span>إجمالي الفاتورة :</span>
                        <strong>0</strong> 
                        <span>EGP</span>
                        <input type="hidden" class="total_amount_invoice" name="total_amount_invoice">
                    </div>
                </div>
                <div id="costs-wrapper">
                    <button type="button" class="btn btn-primary waves-effect waves-float waves-light mb-1" id="add-cost">+ أضف تكلفة</button>                
                </div>
                <div class="mb-2">
                    <label class="form-label">مجموع التكاليف</label>
                    <input type="text" id="total-cost" class="form-control additional_cost" value="0" name="additional_cost" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">إجمالي الفاتورة شامل سعر التكلفة</label>
                    <input type="text" class="form-control total_amount" value="0" name="total_amount" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">ملاحظات (اختيارى)</label>
                    <textarea class="form-control" cols="5" rows="5" name="notes"></textarea>
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

    // تحديث رصيد الفاتورة في حالة الرصيد الإفتتاحي
    $(document).on('input', '.opening_balance_value' ,function(){
        let opening_balance = $(this).val();
        $(".all_total strong").text(opening_balance);
        $('.total_amount').val(opening_balance);
    })

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

    $(document).on('input', '.additional_cost, .unitSelect , .quantity, .length ,.purchase_price, .SizeSelect', function () {
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

        let symbol = unit.find('option:selected').text().trim();

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
        $('.all_total .total_amount_invoice').val(all_total);
    }

    let isFormChanged = false;

    // إضافة صنف جديد
    $(document).on('click', '.addItems', function () {
        isFormChanged = true;

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

        // جلب آخر select مضاف
        const lastCategorySelect = $('.categorySelect').last();
        const lastUnitSelect = $('.unitSelect').last();

        // جلب التصنيفات
        $.get('{{ route("getAllHierarchicalCategories") }}', function(response) {
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

        // تفعيل الـ select2
        $('.table-responsive tbody .select2').select2({
            dir: "rtl",
            width: '200px'
        });

        // عند اختيار تصنيف، جلب المنتجات
        $(document).on('change', '.categorySelect' ,function() {
            const categoryId = $(this).val();
            const row = $(this).closest('tr');
            const select = row.find('.productSelect');

            if (categoryId) {
                $.ajax({
                    url: '{{ route("getProducts") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
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
            else {
                select.empty().append(`<option value="">اختر منتج</option>`);
            }
        });

    });

    function reindexItems() {
            $('.product-item').each(function(index) {
                $(this).find('select, input').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        // استبدل الرقم السابق بالرقم الجديد index
                        const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                        $(this).attr('name', newName);
                    }
                });
            });
    }

        // حذف الصف
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        reindexItems();
        calculateTotalInvoice();
        feather.replace();
    });

    // تفعيل الـ select2
    $('.table-responsive tbody .select2').select2({
        dir: "rtl",
        width: '300px'
    });

    // change type invoice 
    $(document).on('change', '.invoice_type', function(){
        let invoice_type = $(this).find('option:selected').val();
        if(invoice_type === 'cash'){
            $(".addItems").attr('disabled', false);
            $(".warehouse_container").show(500);
            $(".wallet_container").show(500);
            $(".balance_container").show(500);
            $(".opening_balance_container").hide(500);
            $("#add-cost").attr('disabled', false);
        }
        else if(invoice_type === 'credit') {
            $(".addItems").attr('disabled', false);
            $(".warehouse_container").hide(500);
            $(".wallet_container").hide(500);
            $(".balance_container").hide(500);
            $(".opening_balance_container").hide(500);
            $("#add-cost").attr('disabled', false);
        }
        else {
            $(".addItems").attr('disabled', true);
            $(".opening_balance_container").show(500);
            $("#add-cost").attr('disabled', true);
        }
    })

    // change warehouse_id action
    $(document).on('change', '.warehouse_id', function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('getWallets') }}",
            method: 'POST',
            data: {
                warehouse_id: $(this).val() 
            },
            success: function (response) {
                $('.wallet_id').empty();
                $('.wallet_id').append(`<option value="">اختر محفظة ...</option>`)
                $.each(response.data, function(index, item){
                    $('.wallet_id').append(`<option value="${item.id}" data-balance="${item.current_balance}" data-method="${item.method}">${item.name}</option>`)
                })
            },
            error: function(xhr){
                console.log(xhr);
            },
        });
    })

    // get balance wallet
    $(document).on('change', '.wallet_id', function(){
        let balance = parseInt($(this).find('option:selected').attr('data-balance')) || 0;
        let method = $(this).find('option:selected').attr('data-method');
        $(".current_balance").val(balance)
        $(".method").val(method)
        $(".balance_container").show(500);
        $(".current_balance_label").text('الرصيد المتوفر');
        $(".current_balance").attr('type', 'text');
    })

    $('#invoiceForm').on('submit', function(e) {
        e.preventDefault(); // منع الإرسال مؤقتًا
        // تأكيد من المستخدم
        if (confirm("هل أنت متأكد من حفظ البيانات؟")) {
            this.submit();
        } 
    });


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
    // window.onbeforeunload = function () {
    //     if (isFormChanged) {
    //         return "لديك تغييرات غير محفوظة، هل تريد فعلاً مغادرة الصفحة؟";
    //     }
    // };


});
</script>

@endsection 