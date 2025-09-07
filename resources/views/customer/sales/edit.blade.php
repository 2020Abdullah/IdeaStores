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
            <h2 class="content-header-title float-start mb-0">تعديل فاتورة عميل</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.invoice.index') }}">الرئيسية</a>
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
<section class="editInvoice">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">تعديل الفاتورة</h3>
        </div>
        <form action="{{ route('customer.invoice.update') }}" id="invoiceForm" method="POST">
            @csrf
            <input type="hidden" value="{{ $invoice->id }}" name="id">
            <input type="hidden" value="{{ $invoice->code }}" name="code">
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label">العميل</label>
                    <input type="hidden" value="{{ $invoice->customer->id }}" name="customer_id">
                    <input type="text" class="form-control" value="{{ $invoice->customer->name }}" readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label">تاريخ الفاتورة</label>
                    <input type="date" class="form-control date dateForm @error('date') is-invalid @enderror" value="{{ $invoice->date }}" name="date" />
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
                    <input type="hidden" class="form-control invoice_type" value="{{ $invoice->type }}" name="invoice_type">                        
                    @if ($invoice->type === 'credit')
                        <input type="text" class="form-control" value="آجل" readonly>                        
                    @elseif($invoice->type === 'cash')
                        <input type="text" class="form-control" value="كاش" readonly>                        
                    @else 
                        <input type="text" class="form-control" value="رصيد افتتاحي" readonly>                        
                    @endif
                </div>
            
                @if ($invoice->type === 'opening_balance')
                    <div class="mb-1 opening_balance_container">
                        <label class="form-label">قيمة الرصيد الإفتتاحي</label>
                        <input type="number" class="form-control opening_balance_value" value="{{ $invoice->total_amount }}" name="total_amount_invoice">
                    </div>
                @else   
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
                                    <tbody>
                                        @foreach ($invoice->items as $index => $item)
                                        <tr class="product-item">
                                            <td>
                                                <input type="hidden" class="stock_id" name="items[{{$index}}][stock_id]" value="{{ $item->stock->id }}">
                                                <select class="select2 productSelect" name="items[{{$index}}][product_id]">
                                                    <option value="{{ $item->product_id }}" data-stock_id="{{ $item->stock->id }}" 
                                                        {{ $item->product_id == $item->product->id ? 'selected' : '' }}>
                                                        {{ $item->product->name }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="hidden" name="items[{{$index}}][category_id]" value="{{ $item->category_id }}" class="form-control category_id" />
                                                <input type="text" class="form-control categoryInput" value="{{ $item->category->full_path }}" readonly />
                                            </td>
                                            <td>
                                                <input type="hidden" name="items[{{$index}}][size]" value="{{ $item->size ?? ''}}" class="form-control size" />
                                                <input type="text" class="form-control width" value="{{ $item->size->width ?? 0}}" readonly />
                                            </td>
                                            <td>
                                                @if ($item->stock->movements->sum('quantity') <= $item->quantity)
                                                    <input type="text" name="items[{{$index}}][remaining_quantity]" 
                                                    class="form-control remaining_quantity" 
                                                    value="{{ $item->quantity }}" readonly />
                                                @else
                                                    <input type="text" name="items[{{$index}}][remaining_quantity]" 
                                                    class="form-control remaining_quantity" 
                                                    value="{{ $item->stock->movements->sum('quantity') }}" readonly />
                                                @endif
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{$index}}][unit_name]" class="form-control unit" value="{{ $item->unit_name }}" readonly />
                                            </td>
                                            @php
                                            $lastInMovement = $item->stock->movements
                                                    ->where('type', 'in')
                                                    ->sortByDesc('created_at')  
                                                    ->first();
                                            @endphp
                                            <td>
                                                <input type="text" name="items[{{$index}}][price_unit_cost]"  class="form-control price_unit_cost" value="{{ number_format($item->stock->cost->cost_share / $lastInMovement->quantity) }}" readonly />
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{$index}}][quantity]"  class="form-control quantity" value="{{ $item->quantity }}" />
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{$index}}][sale_price]"  class="form-control sale_price" value="{{ number_format($item->sale_price) }}"  />
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{$index}}][total_price]"  class="form-control total_price" value="{{ number_format($item->total_price) }}" readonly />
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{$index}}][profit]"  class="form-control profit" value="{{ number_format($item->profit) }}" readonly />
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{$index}}][total_profit]"  class="form-control total_profit" value="{{ number_format($item->total_profit) }}" readonly />
                                            </td>
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
                            <strong>{{ number_format($invoice->total_amount_without_discount) }}</strong> 
                            <span>EGP</span>
                            <input type="hidden" class="total_amount_without_discount" value="{{ $invoice->total_amount_without_discount }}" name="total_amount_without_discount">
                        </div>
                    </div>
                    <div id="costs-wrapper">
                        <button type="button" class="btn btn-primary waves-effect waves-float waves-light mb-1" id="add-cost">+ أضف تكلفة</button>    
                        @foreach ($invoice->costs as $index => $cost)
                            @if($cost->expenseItem && $cost->expenseItem->is_profit != 1)
                                <div class="row cost-item mb-1">
                                    <div class="col-md-3 mb-1">
                                        <select name="costs[{{ $index }}][exponse_id]" class="select2 cost-select">
                                            @foreach ($exponse_list as $ex_item)
                                                <option value="{{ $ex_item->id }}" 
                                                    {{ (isset($ex_item) && $ex_item->id == $cost->expense_item_id) ? 'selected' : '' }}>
                                                    {{ $ex_item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <select name="costs[{{$index}}][type]" class="form-select costType">
                                            <option value="value" {{ ( $cost->type ?? '') == 'value' ? 'selected' : '' }}>قيمة ثابتة</option>
                                            <option value="percent" {{ ($cost->type ?? '') == 'percent' ? 'selected' : '' }}>نسبة %</option>
                                        </select>                                       
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <input type="number" name="costs[{{ $index }}][amount]" class="form-control costValue" value="{{ -$cost->amount }}" placeholder="القيمة">
                                    </div>
                                    <div class="col-md-3 mb-1">
                                        <button type="button" class="btn btn-danger remove-cost">حذف</button>
                                    </div>
                                </div>
                            @endif
                        @endforeach  
                    </div>
                    <div class="mb-2">
                        <label class="form-label">مجموع التكاليف</label>
                        <input type="text" class="form-control additional_cost"  name="additional_cost" value="{{ $invoice->cost_price }}" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">إجمالي الفاتورة بعد خصم سعر التكلفة والخصم إن وجد</label>
                        <input type="hidden" class="form-control" name="total_amount_old" value="{{ $invoice->total_amount }}">
                        <input type="text" class="form-control total_amount"  name="total_amount" value="{{ number_format($invoice->total_amount) }}" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">صافي الربح</label>
                        <input type="text" class="form-control total_profit_inv" value="{{ number_format($invoice->items->sum('total_profit')) }}" name="total_profit_inv" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">تطبيق خصم على الفاتورة؟</label>
                        <select id="apply_discount" class="form-select">
                            <option value="no" {{ $invoice->discount_type === null ? 'selected' : '' }}>لا</option>
                            <option value="yes" {{ $invoice->discount_type !== null ? 'selected' : '' }}>نعم</option>
                        </select>
                    </div>
                    
                    <div id="discount_fields" style="{{ $invoice->discount_type === null ? 'display:none;' : '' }}">
                        <div class="mb-2">
                            <label class="form-label">نوع الخصم</label>
                            <select name="discount_type" class="form-select">
                                <option value="percent" {{ $invoice->discount_type === 'percent' ? 'selected' : '' }}>نسبة مئوية %</option>
                                <option value="value" {{ $invoice->discount_type === 'value' ? 'selected' : '' }}>قيمة ثابتة</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">قيمة الخصم</label>
                            <input type="number" name="discount_value" class="form-control discount_value" 
                                   value="{{ $invoice->discount_value ?? 0 }}">
                        </div>
                    </div>
                @endif
                <div class="mb-2">
                    <label class="form-label">ملاحظات (اختيارى)</label>
                    <textarea class="form-control" cols="5" rows="5" name="notes">
                        {{ $invoice->notes }}
                    </textarea>
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
$(function () {
    let isFormChanged = false;

    flatpickr(".dateForm", {
        dateFormat: "Y-m-d",
    });

    $.get('{{ route("getStockProducts") }}', function(response) {
        const lastProductSelect = $('.productSelect').last();
        if (response.status) {
            let selectedProductIds = [];
            $('.productSelect').each(function() {
                if ($(this).val()) selectedProductIds.push($(this).val());
            });

            response.data.forEach(item => {
                if (!selectedProductIds.includes(item.product.id.toString())) {
                    lastProductSelect.append(`<option value="${item.product.id}" data-stock_id="${item.id}">${item.product.name}</option>`);
                }
            });
        } else {
            lastProductSelect.html('<option>حدث خطأ في جلب المخزون</option>');
        }
    });


    $('.cost-select').select2({
        dir: "rtl",
        width: '100%'
    });

    $('.product-item .select2').select2({
        dir: "rtl",
        width: '200px'
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


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // $(document).on('input', '.costValue', function () {
    //     calculateTotalCost();
    //     calculateTotalInvoice();
    // });


    // إضافة التكاليف
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
                    <input type="hidden" name="costs[${costIndex}][calculated_amount]" class="calculatedCost">
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

    $(document).on('click', '.remove-cost', function () {
        $(this).closest('.cost-item').remove();
        calculateTotalCost();
        calculateTotalProfitInvoice();
        calculateTotalInvoice();
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

        $('.product-item .select2').select2({
            dir: "rtl",
            width: '200px'
        });
    });

    $(document).on('change', '.productSelect', function () {
        const row = $(this).closest('tr');
        const stockId = row.find('.productSelect option:selected').attr('data-stock_id');

        if (!stockId) {
            $category.val('');
            return;
        }

        $.ajax({
            url: '{{ route("getStocks") }}',
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
                    row.find('.remaining_quantity').val(response.remaining_quantity);

                    if(response.data.unit.name === 'سنتيمتر'){
                        row.find('.unit').val('متر');
                    }
                    else {
                        row.find('.unit').val(response.data.unit.name ?? '');
                    }

                    row.find('.price_unit_cost').val(
                        formatNumberValue(response.cost)
                    );
                    row.find('.sale_price').val(
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

    $(document).on('input', '.quantity, .sale_price, .profit', function(){
        let row = $(this).closest('tr');
        calculateProfit(row);
        calculateTotalPrice(row);
        calculateTotalProfit(row);
        calculateTotalInvoice();
        calculateTotalProfitInvoice();
    })

    // حساب التكلفة ووضع الإجمالي
    $(document).on('input', '.costValue', function () {
        calculateTotalCost();
        calculateTotalInvoice();
        calculateTotalProfitInvoice();
    });

    function calculateTotalCost() {
    let costtotal = 0;
    let invoiceAmount = 0;

    // إجمالي الفاتورة قبل التكاليف
    $('tr.product-item').each(function () {
        let price = parseFloat($(this).find('.total_price').val().replace(/,/g, '')) || 0;
        invoiceAmount += price;
    });

    // المرور على التكاليف
    $('.cost-item').each(function () {
        let type = $(this).find('.costType').val();
        let amount = parseFloat($(this).find('.costValue').val()) || 0;
        let finalValue = 0;

        if (type === 'percent') {
            finalValue = (invoiceAmount * amount / 100);
        } else {
            finalValue = amount;
        }

        // وضع القيمة الفعلية في الحقل المخفي
        $(this).find('.calculatedCost').val(finalValue);

        // جمعها في الإجمالي
        costtotal += finalValue;
    });

    // تحديث مجموع التكاليف
    $('.additional_cost').val(formatNumberValue(costtotal));
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


    // حساب هامش الربح 
    function calculateProfit(row){
        let profit_price = 0;
        let price_unit_cost = parseFloat(row.find('.price_unit_cost').val()) || 0;
        let sale_price = parseFloat(row.find('.sale_price').val()) || 0;
        profit_price = sale_price - price_unit_cost;
        row.find('.profit').val(formatNumberValue(profit_price));
    }

    // حساب مجموع هامش الربح للصنف
    function calculateTotalProfit(row){
        let total_profit = 0;
        let profit = parseFloat(row.find('.profit').val()) || 0;
        let quantity = parseFloat(row.find('.quantity').val()) || 0;
        total_profit = profit * quantity;
        row.find('.total_profit').val(formatNumberValue(total_profit));
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
    function calculateTotalPrice(row){
        let total_price = 0;
        let remaining_quantity = parseFloat(row.find('.remaining_quantity').val()) || 0;
        let quantity = parseFloat(row.find('.quantity').val()) || 0;
        let sale_price = parseFloat(row.find('.sale_price').val()) || 0;
        if(quantity <= remaining_quantity){
            total_price = sale_price * quantity;
            row.find('.total_price').val(formatNumberValue(total_price));
        }
        else {
            toastr.info('الكمية أكبر من المسموح بها');
            row.find('.quantity').val(remaining_quantity)
        }
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
        let additionalCost = parseFloat($('.additional_cost').val()) || 0;
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
        $('.all_total .total_amount_invoice').val(formatNumberValue(all_total));
    }

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
            calculateProfit(row);
            calculateTotalProfit(row);
            calculateTotalInvoice();
            calculateTotalProfitInvoice();
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

    // عند عمل تسجيل للفاتورة 
    $('#invoiceForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let formData = new FormData(this);

        // إظهار التحميل وإيقاف الزر
        $("#loading-excute").show();
        $(".btnSubmit").prop("disabled", true);

        let isValid = true;
        let message = "";

        let invoice_type = $('.invoice_type').val();

        if (invoice_type === 'opening_balance') {
            let opening_balance_value = $(".opening_balance_value").val();
            if (!opening_balance_value || opening_balance_value == 0) {
                toastr.info('يجب ملئ حقل الرصيد الإفتتاحي');
                return; // وقف مباشرة
            }
        } else {
            // تحقق أولًا هل فيه أصناف أصلاً
            if ($('.product-item').length === 0) {
                toastr.info("يجب إضافة صنف واحد على الأقل إلى الفاتورة قبل الحفظ.");
                return;
            }

            // تحقق من كل صف
            $('.product-item').each(function(index, row) {
                let productSelect = $(row).find('.productSelect').val();
                let quantity = $(row).find('.quantity').val();
                let sale_price = $(row).find('.sale_price').val();

                if (!productSelect || quantity <= 0 || sale_price <= 0) {
                    isValid = false;
                    message = "تأكد من إدخال جميع البيانات المطلوبة لكل صنف (المنتج, سعر البيع, الكمية المطلوبة).";
                    return false; // يوقف الـ each
                }
            });

            if (!isValid) {
                toastr.info(message);
                return;
            }

            // تحقق من التكاليف الإضافية
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

                if (hasError) return; // يوقف الحفظ
            }

            // تحقق من الخصم
            if ($('#apply_discount').val() === 'yes') {
                let discountVal = parseFloat($('.discount_value').val()) || 0;
                if (discountVal <= 0) {
                    toastr.info("يجب إدخال قيمة الخصم قبل الحفظ.");
                    return;
                }
            }
        }

        // لو كل شيء تمام، اعرض التأكيد
        if (confirm("هل أنت متأكد من حفظ البيانات؟")) {
            $.ajax({
                url: form.attr('action'),
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    $("#loading-excute").hide();
                    $(".btnSubmit").prop("disabled", false);

                    if(response.success){
                        toastr.success(response.message);
                        // لو عايز بعد الحفظ تروح لصفحة تانية
                        if(response.redirect){
                            window.location.href = response.redirect;
                        }
                        // أو تعمل reset للفورم
                        else {
                            form[0].reset();
                            $('.table tbody').empty(); // تفريغ الأصناف
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    $("#loading-excute").hide();
                    $(".btnSubmit").prop("disabled", false);

                    if(xhr.responseJSON && xhr.responseJSON.errors){
                        $.each(xhr.responseJSON.errors, function(key, error){
                            toastr.error(error[0]);
                        });
                    } else {
                        toastr.error("فشل الاتصال بالسيرفر");
                    }
                }
            });
        }
    });
});
</script>

@endsection 