@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css-rtl/flatpickr.min.css') }}">
<style>
    .product-item input {
        width: 150px!important;
    }
</style>
@endsection

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">إضافة فاتورة بيع</h2>
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
            <h3 class="card-title">إضافة فاتورة مبيعات</h3>
        </div>
        <form action="{{ route('customer.invoice.store') }}" id="invoiceForm" method="POST">
            @csrf
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label" for="name">العميل</label>
                    @if (isset($customer))
                        <input type="hidden" class="form-control customer_id" name="customer_id" value="{{ $customer->id }}" required>
                        <input type="text" class="form-control customer_id" value="{{ $customer->name }}" readonly>
                    @else
                        <select name="customer_id" class="form-select CustomerSelect customer_id">
                            <option value="">أختر العميل ...</option>
                            @foreach ($customer_list as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('customer_id')
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
                    <input type="date" placeholder="تاريخ الفاتورة" class="form-control dateForm date @error('date') is-invalid @enderror" name="date" required/>
                    @error('date')
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
                
                <div class="cash-options" style="display: none;">
                    <label>اختر الخزنة:</label>
                    <div class="d-flex gap-1 mb-1">
                        @foreach($warehouse_list as $warehouse)
                            <button type="button" class="btn btn-outline-primary select-warehouse"
                                data-id="{{ $warehouse->id }}" data-name="{{ $warehouse->name }}">
                                {{ $warehouse->name }}
                            </button>
                        @endforeach
                    </div>
                
                    <!-- الحقول ستظهر هنا عند اختيار الخزنة -->
                    <div class="warehouse-fields"></div>
                </div>
                

                <!-- opening balance -->
                <div class="mb-1 opening_balance_container" style="display: none;">
                    <label class="form-label">قيمة الرصيد الإفتتاحي</label>
                    <input type="number" class="form-control opening_balance_value" value="0" name="opening_balance_value">
                </div>
                <div class="mb-2">
                    <button type="button" disabled class="addItems btn-icon-content btn btn-success waves-effect waves-float waves-light">
                        <i data-feather='plus-circle'></i>
                        <span>إضافة صنف جديد</span> 
                    </button>
                </div>
                <div class="mb-2">  
                    <div class="table-items">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <th>المخزون</th>
                                    <th>التصنيف</th>
                                    <th>المقاس</th>
                                    <th>الكمية المتاحة</th>
                                    <th>الوحدة</th>
                                    <th>سعر تكلفة الوحدة</th>
                                    <th>الكمية المطلوبة</th>
                                    <th>سعر بيع الوحدة</th>
                                    <th>إجمالي سعر البيع</th>
                                    <th>هامش الربح للوحدة</th>
                                    <th>إجمالي هامش الربح</th>
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
                        <input type="hidden" class="total_amount_without_discount" name="total_amount_without_discount">
                    </div>
                </div>
                <div id="costs-wrapper">
                    <button type="button" class="btn btn-primary waves-effect waves-float waves-light mb-1" id="add-cost" disabled>+ أضف تكلفة</button>                
                </div>
                <div class="mb-2">
                    <label class="form-label">مجموع التكاليف</label>
                    <input type="text" class="form-control additional_cost" value="0" name="additional_cost" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">إجمالي الفاتورة بعد خصم سعر التكلفة والخصم إن وجد</label>
                    <input type="text" class="form-control total_amount" value="0" name="total_amount" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">صافي الربح</label>
                    <input type="text" class="form-control total_profit_inv" value="0" name="total_profit_inv" readonly>
                </div>

                <div class="mb-2">
                    <label class="form-label">تطبيق خصم على الفاتورة؟</label>
                    <select id="apply_discount" class="form-select">
                        <option value="no">لا</option>
                        <option value="yes">نعم</option>
                    </select>
                </div>
                
                <div id="discount_fields" style="display:none;">
                    <div class="mb-2">
                        <label class="form-label">نوع الخصم</label>
                        <select name="discount_type" class="form-select">
                            <option value="percent">نسبة مئوية %</option>
                            <option value="value">قيمة ثابتة</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">قيمة الخصم</label>
                        <input type="number" name="discount_value" class="form-control discount_value" value="0">
                    </div>
                </div>                

                <div class="mb-2">
                    <label class="form-label">ملاحظات (اختيارى)</label>
                    <textarea class="form-control" cols="5" rows="5" name="notes"></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btnSubmit btn btn-relief-success">حفظ الفاتورة</button>
            </div>
        </form>
    </div>
</section>


@endsection

@section('js')
<script src="{{ asset('assets/js/flatpickr.js') }}"></script>
<script>
$(document).ready(function(){
    let isFormChanged = false;

    $('.CustomerSelect').select2({
        dir: "rtl",
        width: '100%'
    });

    flatpickr(".dateForm", {
        dateFormat: "Y-m-d",
    });

    // عند تغيير حقول الإدخال 
    $(document).on('input', '.sale_price', function(){
        let row = $(this).closest('tr');
        calculateTotalPrice(row);
        calculateTotalProfit(row);
        calculateTotalInvoice();
        calculateTotalProfitInvoice();
    })

    // عند إدخال كمية
    $(document).on('input', '.quantity', function(){
        let row = $(this).closest('tr');
        calculateTotalPrice(row);
        calculateTotalProfit(row);
        calculateTotalInvoice();
        calculateTotalProfitInvoice();
    });


    // حساب التكلفة ووضع الإجمالي
    $(document).on('input', '.costValue', function () {
        calculateTotalCost();
        calculateTotalInvoice();
        calculateTotalProfitInvoice();
    });

    function calculateTotalCost() {
        let costtotal = 0;
        let invoiceAmount = 0;

        // ✅ حساب إجمالي الفاتورة بدون التكاليف الإضافية
        $('tr.product-item').each(function () {
            let price = parseFloat($(this).find('.total_price').val().replace(/,/g, '')) || 0;
            invoiceAmount += price;
        });

        // ✅ التكاليف الإضافية (قيمة أو نسبة)
        let costItems = $('.cost-item');
        if (costItems.length > 0) {
            costItems.each(function () {
                let type = $(this).find('.costType').val();
                let amount = parseFloat($(this).find('.costValue').val()) || 0;

                if (type === 'percent') {
                    costtotal += (invoiceAmount * amount / 100);
                } else {
                    costtotal += amount;
                }
            });
        } else {
            costtotal = 0; // ✅ لو مفيش أي تكلفة موجودة
        }

        // ✅ تحديث الحقل مباشرة (حتى لو صفر)
        $('.additional_cost').val(formatNumberValue(costtotal));
    }

    // إظهار أو إخفاء حقول الخصم حسب الاختيار
    $('#apply_discount').on('change', function() {
        if ($(this).val() === 'yes') {
            $('#discount_fields').slideDown();
        } else {
            $('#discount_fields').slideUp();
            $('#discount_fields').find('input, select').val(''); // مسح القيم
            calculateTotalInvoice(); // إعادة الحساب بدون الخصم
        }
    });

    $(document).on('input', '.discount_value', function(){
        calculateTotalInvoice();
        calculateTotalProfitInvoice();
    })


    // حساب مجموع هامش الربح للصنف
    function calculateTotalProfit(row){
        let total_profit = 0;

        $('tr.product-item').each(function() {
            let row = $(this);

            // جلب القيم مباشرة بدون الاعتماد على الحقل .profit المنسق
            let price_unit_cost = parseFloat(row.find('.price_unit_cost').val().replace(/,/g, '')) || 0;
            let sale_price = parseFloat(row.find('.sale_price').val().replace(/,/g, '')) || 0;
            let quantity = parseFloat(row.find('.quantity').val()) || 0;

            let profit = sale_price - price_unit_cost;
            let row_profit = profit * quantity;

            total_profit += row_profit;

            // تحديث الحقول
            row.find('.profit').val(formatNumberValue(profit));          // هامش الربح للوحدة
            row.find('.total_profit').val(formatNumberValue(row_profit)); // هامش الربح الإجمالي للصف
        });
    }


    // حساب مجموع هامش الربح للفاتورة بالكامل
    function calculateTotalProfitInvoice(){
        let total_profit_inv = 0;
        let additionalCost = parseFloat($('.additional_cost').val().replace(/,/g, '')) || 0;
        
        $('tr.product-item').each(function () {
            let row_profit = parseFloat($(this).find('.total_profit').val().replace(/,/g, '')) || 0;
            total_profit_inv += row_profit;
        });

        // خصم الفاتورة إذا تم تفعيله
        if ($('#apply_discount').val() === 'yes') {
            let discountType = $('select[name="discount_type"]').val();
            let discountValue = parseFloat($('input[name="discount_value"]').val()) || 0;

            if (discountType === 'percent') {
                total_profit_inv -= total_profit_inv * (discountValue / 100);
            } else {
                total_profit_inv -= discountValue;
            }
        }

        // خصم التكاليف الإضافية
        total_profit_inv -= additionalCost;

        $('.total_profit_inv').val(formatNumberValue(total_profit_inv));
    }

    // حساب الإجمالي للصنف
    function calculateTotalPrice(row) {
        let remaining_quantity = parseInt(row.find('.remaining_quantity').val()) || 0;
        let quantity = parseFloat(row.find('.quantity').val()) || 0;

        let sale_price = parseFloat(row.find('input.sale_price').val()) || 0;

        if (quantity > remaining_quantity) {
            toastr.info('الكمية أكبر من المسموح بها');
            quantity = remaining_quantity;
            row.find('.quantity').val(quantity);
        }

        let total_price = sale_price * quantity;
        row.find('.total_price').val(formatNumberValue(total_price));
    }

    // حساب إجمالي الفاتورة
    function calculateTotalInvoice() {
        let total_amount = 0;
        let all_total = 0;

        // جمع إجماليات الأصناف
        $('tr.product-item').each(function () {
            let price = parseFloat($(this).find('.total_price').val().replace(/,/g, '')) || 0;
            all_total += price;
        });

        // جمع التكاليف (قيمة أو نسبة)
        let additionalCost = parseInt($('.additional_cost').val().replace(/,/g, '')) || 0;
        total_amount = all_total - additionalCost;

        // تطبيق الخصم
        let discountType = $('select[name="discount_type"]').val();
        let discountValue = parseInt($('input[name="discount_value"]').val()) || 0;

        if ($('#apply_discount').val() === 'yes') {
            if (discountType === 'percent') {
                total_amount -= total_amount * (discountValue / 100);
            } else {
                total_amount -= discountValue;
            }
        }

        // تحديث الحقول
        $('.total_amount').val(formatNumberValue(total_amount));
        $('.all_total strong').text(formatNumberValue(all_total));
        $('.all_total .total_amount_without_discount').val(formatNumberValue(all_total));
    }


    function formatNumberValue(value) {
        // إزالة الفواصل
        if (typeof value === 'string') {
            value = value.replace(/,/g, '');
        }

        // تحويل لرقم
        let num = parseFloat(value) || 0;

        // لو الرقم كبير جدًا، نخليه ضمن حد معين (مثلاً 9999999999999.99)
        const max = 9999999999999.99;
        if (num > max) {
            num = max;
        }

        // إعادة الرقم منسق بخانتين عشريتين
        return num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // إضافة تكلفة 
    let costIndex = 1;
    $('#add-cost').click(function () {
        const exponseItems = @json($exponse_list);
        let options = `<option value="">اختر بند تكلفة</option>`;
        exponseItems.forEach(item => {
            options += `<option value="${item.id}">${item.name}</option>`;
        });

        const newCost = `
            <div class="row cost-item mb-1">
                <div class="col-md-3 mb-1">
                    <select name="costs[${costIndex}][exponse_id]" class="select2 cost-select">
                        ${options}
                    </select>
                </div>
                <div class="col-md-3 mb-1">
                    <select name="costs[${costIndex}][type]" class="form-select costType">
                        <option value="value">قيمة ثابتة</option>
                        <option value="percent">نسبة %</option>
                    </select>
                </div>
                <div class="col-md-3 mb-1">
                    <input type="number" name="costs[${costIndex}][amount]" class="form-control costValue" placeholder="ادخل القيمة أو النسبة">
                </div>
                <div class="col-md-3 mb-1">
                    <button type="button" class="btn btn-danger remove-cost">حذف</button>
                </div>
            </div>
        `;

        $('#costs-wrapper').append(newCost);
        costIndex++;
        $('.cost-select').select2({
            dir: "rtl",
            width: '100%'
        });
    });

    // السماح بالأرقام فقط (مع النقطة العشرية) على الحقول المطلوبة
    $(document).on('input', '.quantity, .sale_price', function() {
        let value = $(this).val();

        // إزالة أي حرف غير الأرقام أو النقطة
        value = value.replace(/[^0-9.]/g, '');

        // التأكد من وجود نقطة واحدة فقط
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts[1];
        }

        $(this).val(value);
    });

    // حذف التكلفة
    $(document).on('click', '.remove-cost', function () {
        $(this).closest('.cost-item').remove();
        calculateTotalCost();
        calculateTotalInvoice();
        calculateTotalProfitInvoice();
    });

    // إضافة صنف جديد
    $(document).on('click', '.addItems', function () {
        isFormChanged = true;

        // حساب رقم الصف الجديد
        let index = $('.product-item').length;

        // HTML الخاص بالصف الجديد
        let newRow = `
            <tr class="product-item">
                <td>
                    <input type="hidden" class="stock_id" name="items[${index}][stock_id]">
                    <select class="select2 productSelect" name="items[${index}][product_id]">
                        <option value="" data-stock_id="items[${index}][stock_id]">اختر منتج</option>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="items[${index}][category_id]" class="form-control category_id" />
                    <input type="text" class="form-control categoryInput" readonly />
                </td>
                <td>
                    <input type="hidden" name="items[${index}][size]" class="form-control size" />
                    <input type="text" class="form-control width" readonly />
                </td>
                <td>
                    <input type="text" name="items[${index}][remaining_quantity]" class="form-control remaining_quantity" readonly />
                </td>
                <td>
                    <input type="text" name="items[${index}][unit_name]" class="form-control unit" readonly />
                </td>
                <td>
                    <input type="text" name="items[${index}][price_unit_cost]"  class="form-control price_unit_cost" readonly />
                </td>
                <td>
                    <input type="text" name="items[${index}][quantity]"  class="form-control quantity" />
                </td>
                <td>
                    <input type="text" name="items[${index}][sale_price]"  class="form-control sale_price" />
                    <span class="alert alert-danger sale_price_error" style="display:none;">لا يمكن أن يكون سعر البيع أقل من سعر التكلفة</span>
                </td>
                <td>
                    <input type="text" name="items[${index}][total_price]"  class="form-control total_price" readonly />
                </td>
                <td>
                    <input type="text" name="items[${index}][profit]"  class="form-control profit" readonly />
                </td>
                <td>
                    <input type="text" name="items[${index}][total_profit]"  class="form-control total_profit" readonly />
                </td>
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

        // جلب ستوك المخزن
        $.get('{{ route("getStockProducts") }}', function(response) {
            // جلب آخر select مضاف
            const lastProductSelect = $('.productSelect').last();
            if (response.status) {
                lastProductSelect.empty().append(`<option value="">اختر المخزون</option>`);
                response.data.forEach(item => {
                    lastProductSelect.append(`<option value="${item.product.id}" data-stock_id="${item.id}">${item.product.name}</option>`);
                });
            } else {
                lastProductSelect.html('<option>حدث خطأ في جلب المخزون</option>');
            }
        });

        $(document).on('change', '.productSelect', function () {
            const row = $(this).closest('tr');
            const stockId = row.find('.productSelect option:selected').attr('data-stock_id');

            if (!stockId) {
                $category.val('');
                return;
            }

            $.ajax({
                url: '{{ route("getStocks") }}', // أو غيّرها إلى route("stocks.details") لو خصصت راوت للتفاصيل
                type: 'POST',
                data: {
                    stock_id: stockId,
                    _token: $('meta[name="csrf-token"]').attr('content') // تأكد من وجود الميتا في <head>
                },
                success: function (response) {
                    if (response.status && response.data) {
                        row.find('.stock_id').val(response.data.id);
                        row.find('.categoryInput').val(response.data.category.full_path ?? '');
                        row.find('.category_id').val(response.data.category_id);
                        row.find('.width').val(response.data.size?.width ?? 0);
                        row.find('.size').val(response.data.size?.id ?? 0);
                        row.find('.remaining_quantity').val(response.available_qty);

                        if(response.data.unit.name === 'سنتيمتر'){
                            row.find('.unit').val('متر');
                        }
                        else {
                            row.find('.unit').val(response.data.unit.name ?? '');
                        }

                        row.find('.price_unit_cost').val(
                            formatNumberValue(response.cost)
                        );

                    } else {
                        row.find('.categoryInput').val('لم يتم العثور على بيانات');
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        });

        // تفعيل الـ select2
        $('.table-responsive tbody .select2').select2({
            dir: "rtl",
            width: '200px'
        });
    });

    let selectedWarehouses = [];
    let wallets = @json($wallets);
    let totalInvoiceAmount = parseFloat($('.total_amount_invoice').val()) || 0;

    // عند تغيير نوع الفاتورة
    $(document).on('change', '.invoice_type', function(){
        let invoice_type = $(this).find('option:selected').val();
        let balance = parseInt($(this).find('option:selected').attr('data-balance')) || 0;
        let total_amount = parseFloat($('.total_amount').val()) || 0;

        if(invoice_type === 'cash'){
            $(".addItems").attr('disabled', false);
            $("#add-cost").attr('disabled', false);
            $("#apply_discount").attr('disabled', false);
            $('.cash-options').show();
            $(".opening_balance_container").hide(500);
        }
        else if(invoice_type === 'credit') {
            $(".addItems").attr('disabled', false);
            $("#add-cost").attr('disabled', false);
            $("#apply_discount").attr('disabled', false);
            $(".opening_balance_container").hide(500);
            $('.cash-options').hide();
            $('.warehouse-fields').empty();
            selectedWarehouses = [];
        }
        else {
            $(".addItems").attr('disabled', true);
            $("#add-cost").attr('disabled', true);
            $("#apply_discount").attr('disabled', true);
            $("#apply_discount").val('');
            $("#discount_fields").hide(500);
            $(".opening_balance_container").show(500);
            $(".warehouse_list").hide(500);
            $('.table-items tbody').empty();
            $('.cash-options').hide();
            $('.warehouse-fields').empty();
            selectedWarehouses = [];
        }
    });

    // عند اختيار خزنة
    $(document).on('click', '.select-warehouse', function() {
    let warehouseId = $(this).data('id');
    let warehouseName = $(this).data('name');

    if ($(this).hasClass('active')) {
        // إلغاء الاختيار
        $(this).removeClass('active btn-primary').addClass('btn-outline-primary');
        selectedWarehouses = selectedWarehouses.filter(item => item.id !== warehouseId);
        $(`.warehouse-field[data-id="${warehouseId}"]`).remove();
    } else {
        // اختيار جديد
        $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
        selectedWarehouses.push({ id: warehouseId, name: warehouseName });

        // إنشاء خيارات المحفظة
        let optionsHtml = '<option value="">اختر محفظة...</option>';
        wallets.forEach(wallet => {
            optionsHtml += `<option value="${wallet.id}">${wallet.name}</option>`;
        });

        // إضافة الحقل مع input مخفي للفورم
        let fieldHtml = `
            <div class="mb-2 warehouse-field" data-id="${warehouseId}">
                <label>محفظة ${warehouseName}:</label>
                <input type="hidden" name="warehouses[${warehouseId}][warehouse_id]" value="${warehouseId}">
                <select name="warehouses[${warehouseId}][wallet_id]" class="form-select mb-1">
                    ${optionsHtml}
                </select>
            </div>
        `;
        $('.warehouse-fields').append(fieldHtml);
    }
});

    // منع أن يكون سعر البيع أقل من سعر التكلفة مع إظهار رسالة خطأ
    $(document).on('input', '.sale_price', function() {
        let row = $(this).closest('tr');
        let salePrice = parseFloat($(this).val()) || 0;
        let costPrice = parseFloat(row.find('.price_unit_cost').val()) || 0;
        let errorSpan = row.find('.sale_price_error');

        if (salePrice < costPrice) {
            errorSpan.show(); // إظهار رسالة الخطأ
        } else {
            errorSpan.hide(); // إخفاء رسالة الخطأ إذا كان السعر صحيح
            calculateTotalPrice(row);
            calculateTotalProfit(row);
            calculateTotalInvoice();
            calculateTotalProfitInvoice();
        }
    });


    // حذف الصف
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        calculateTotalInvoice();
        calculateTotalProfitInvoice();
        feather.replace();
    });

    function toNumber(val) {
        if (val == null) return 0;
        val = String(val).replace(/,/g, '').replace(/[^\d.]/g, '');
        const n = parseFloat(val);
        return isNaN(n) ? 0 : n;
    }

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

    //  عند عمل تسجيل للفاتورة
    $('#invoiceForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);
        let invoice_type = form.find('select[name="invoice_type"]').val();
        let isValid = true;
        let message = "";

        // ✅ الحقول الأساسية (ماعدا رصيد افتتاحي)
        if (invoice_type !== 'opening_balance') {
            let invoice_date = $(".date").val();
            let customer_id = $(".customer_id").val();

            if (!invoice_date) {
                toastr.info('يجب ملئ حقل التاريخ');
                return;
            }
            if (!customer_id) {
                toastr.info('يجب اختيار عميل');
                return;
            }

            // ✅ التكاليف الإضافية
            if ($('.cost-item').length > 0) {
                let hasError = false;

                $('.cost-item').each(function () {
                    let expSelect = $(this).find('.cost-select').val();
                    let costVal = parseFloat($(this).find('.costValue').val()) || 0;

                    if (!expSelect) {
                        toastr.info("يجب اختيار بند تكلفة لكل تكلفة مضافة.");
                        hasError = true;
                        return false;
                    }
                    if (costVal <= 0) {
                        toastr.info("يجب إدخال قيمة صحيحة لكل بند تكلفة.");
                        hasError = true;
                        return false;
                    }
                });

                if (hasError) return;
            }

            // ✅ الخصم
            if ($('#apply_discount').val() === 'yes') {
                let discountVal = parseFloat($('.discount_value').val()) || 0;
                if (discountVal <= 0) {
                    toastr.info("يجب إدخال قيمة الخصم قبل الحفظ.");
                    return;
                }
            }
        }

        // ✅ رصيد افتتاحي
        if (invoice_type === 'opening_balance') {
            let opening_balance_value = $(".opening_balance_value").val();
            if (!opening_balance_value || opening_balance_value == 0) {
                toastr.info('يجب ملئ حقل الرصيد الإفتتاحي');
                return;
            }
        }

        // ✅ فاتورة كاش
        else if (invoice_type === 'cash') {
            if ($('.select-warehouse.active').length === 0) {
                toastr.info('يجب اختيار خزنة واحدة على الأقل عند الفاتورة الكاش');
                return;
            }

            let walletMissing = false;
            $('.warehouse-field').each(function() {
                let walletValue = $(this).find('select').val();
                if (!walletValue) {
                    walletMissing = true;
                    return false;
                }
            });

            if (walletMissing) {
                toastr.info('يجب اختيار محفظة لكل خزنة تم تحديدها');
                return;
            }
        }

        // ✅ باقي الفواتير (أصناف + تحقق منها)
        else {
            if ($('.product-item').length === 0) {
                toastr.info("يجب إضافة صنف واحد على الأقل إلى الفاتورة قبل الحفظ.");
                return;
            }

            $('.product-item').each(function(index, row) {
                let productSelect = $(row).find('.productSelect').val();
                let quantity = $(row).find('.quantity').val();
                let sale_price = $(row).find('.sale_price').val();

                if (!productSelect || quantity <= 0 || sale_price <= 0) {
                    isValid = false;
                    message = "تأكد من إدخال جميع البيانات المطلوبة لكل صنف (المنتج, سعر البيع, الكمية المطلوبة).";
                    return false;
                }
            });

            if (!isValid) {
                toastr.info(message);
                return;
            }
        }

        // ✅ لو كل شيء تمام → تنفيذ الحفظ
        if (confirm("هل أنت متأكد من حفظ البيانات؟")) {
            $.ajax({
                url: form.attr('action'),
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#loading-excute").fadeIn(500);
                    $(".btnSubmit").prop("disabled", true);
                },
                success: function(response) {
                    $("#loading-excute").hide();
                    $(".btnSubmit").prop("disabled", false);

                    if (response.success) {
                        toastr.success(response.message);

                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            form[0].reset();
                            $('.table tbody').empty(); // تفريغ الأصناف
                        }
                    } else {
                        toastr.error(response.message || "حدث خطأ أثناء حفظ الفاتورة");
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                    $("#loading-excute").hide(500);
                    $(".btnSubmit").prop("disabled", false);

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, error){
                            toastr.error(error[0]);
                        });
                    } else {
                        toastr.error("فشل الاتصال بالسيرفر");
                    }
                },
                complete: function(){
                    $('#loading-excute').fadeOut(500);
                    $(".btnSubmit").prop("disabled", false);
                    feather.replace();
                }
            });
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

})
</script>

@endsection 