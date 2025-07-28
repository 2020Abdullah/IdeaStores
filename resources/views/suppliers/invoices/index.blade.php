@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">فواتير الموردين</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">فواتير الموردين</a>
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
        <h3 class="card-title">بحث متقدم</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('supplier.invoice.filter') }}" id="searchForm" method="POST">
            @csrf
            <div class="row">
                <div class="col-4 mb-1">
                    <label class="form-label">من</label>
                    <input type="date" class="form-control start_date" name="start_date" placeholder="YYY-MMM-DDD">
                </div>
                <div class="col-4 mb-1">
                    <label class="form-label">إلي</label>
                    <input type="date" class="form-control end_date" name="end_date" placeholder="YYY-MMM-DDD">
                </div>
                <div class="col-4">
                    <label class="form-label">بحث بكود الفاتورة أو اسم المورد ...</label>
                    <input type="text" class="form-control searchText" name="searchText" placeholder="بحث بالإسم أو بالرقم ...">
                </div>
            </div>
           <button type="submit" class="btn btn-outline-success waves-effect mt-2">بحث</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">فواتير الموردين</h3>
        <div class="card-action">
            <a href="{{ route('supplier.invoice.add') }}" class="btn btn-success waves-effect waves-float waves-light">
                إضافة فاتورة جديدة 
            </a>
            <a href="{{ route('download.supplier.Template') }}" class="btn btn-info waves-effect waves-float waves-light">
                تنزيل قالب 
            </a>
            <a href="#" data-bs-toggle="modal" data-bs-target="#importFile" class="btn btn-warning waves-effect waves-float waves-light">
                استيراد بيانات
            </a>
            <a href="button" class="btn btn-danger waves-effect waves-float waves-light">
                تصدير اكسيل
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            @include('suppliers.invoices.invoice_table')
        </div>
    </div>
    <div class="card-footer">
        {{ $invoices_list->links() }}
    </div>
</div>

<!-- model delete invoice -->
<div class="modal fade text-start modal-danger" id="delInvoice" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحذير !</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('supplier.invoice.delete') }}" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <input type="hidden" name="total_amount" class="total_amount">
                <input type="hidden" name="supplier_id" class="supplier_id">
                <div class="modal-body">
                    <label class="form-label">هل أنت متأكد من حذف هذه الفاتورة لا يمكن التراجع عن هذه العملية ؟</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger waves-effect waves-float waves-light">تأكيد الحذف</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model payment invoice -->
<div class="modal fade text-start modal-primary" id="PaymentInvoice" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">دفع فاتورة مورد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('supplier.invoice.payment') }}" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <input type="hidden" name="supplier_id" class="supplier_id">
                <input type="hidden" name="method" class="method">
                <div class="modal-body">
                    <div class="mb-1">
                        <label class="form-label">من حساب</label>
                        <select name="warehouse_id" class="form-control warehouse_id">
                            <option value="">اختر الخزنة ...</option>
                            @foreach ($warehouse_list as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>              
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">المحفظة</label>
                        <select name="wallet_id" class="form-control wallet_id">
                            <option value="">...</option>
                        </select>
                    </div>
                    <div class="mb-1 balance_container" style="display: none;">
                        <label class="form-label current_balance_label"></label>
                        <input type="hidden" class="form-control current_balance" name="current_balance" readonly>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">المبلغ المطلوب</label>
                        <input type="number" class="form-control total_amount" name="total_amount" readonly>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">المبلغ</label>
                        <input type="number" class="form-control" name="amount">
                    </div>
                    <div class="mb-1">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="description" class="form-control" cols="5" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">تأكيد العملية</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            // delete action
            $(".delBtn").on('click', function(){
                let id = $(this).data('id');
                let total_amount = $(this).data('total_amount');
                let supplier_id = $(this).data('supplier_id');
                $("#delInvoice .id").val(id);
                $("#delInvoice .supplier_id").val(supplier_id);
                $("#delInvoice .total_amount").val(total_amount);
            })

            // credit action
            $(".creditBtn").on('click', function(){
                let id = $(this).data('id');
                let total_amount = $(this).data('total_amount');
                let supplier_id = $(this).data('supplier_id');
                $("#PaymentInvoice .id").val(id);
                $("#PaymentInvoice .supplier_id").val(supplier_id);
                $("#PaymentInvoice .total_amount").val(total_amount);
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
               $("#PaymentInvoice .current_balance").val(balance)
               $("#PaymentInvoice .method").val(method)
               $("#PaymentInvoice .balance_container").show(500);
               $("#PaymentInvoice .current_balance_label").text('الرصيد المتوفر');
               $("#PaymentInvoice .current_balance").attr('type', 'text');
            })

            // filter search 
            $(document).on('submit', '#searchForm', function(e){
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('supplier.invoice.filter') }}",
                    method: 'POST',
                    data: formData,
                    beforeSend: function () {
                        $('#loading-excute').fadeIn(500);
                    },
                    success: function (response) {
                        $('.table-responsive').html(response);
                    },
                    error: function(xhr){
                        console.log(xhr);
                    },
                    complete: function(){
                        $('#loading-excute').fadeOut(500);
                        feather.replace();
                    }
                });
            })
        })
    </script>
@endsection
