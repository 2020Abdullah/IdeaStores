@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">كشف حساب العميل</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.index') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">حساب العميل</a>
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
            <h3 class="card-title">{{ $customer->account->name }}</h3>
            <div class="card-action">
                <form action="{{ route('customer.account.export') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" value="{{ $customer->id }}" name="customer_id">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">تصدير PDF</button>
                </form>
            </div>
        </div>
        <hr />
        <div class="card-body">
            <div class="row">
                <div class="col mb-1 text-center">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-balance">
                                <h3>الرصيد</h3>
                                <h4>{{ number_format($customer->balance) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr />
        </div>
    </div>

    <!-- الدفعات -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">الدفعات</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>رقم الدفعة</th>
                            <th>تاريخ الدفعة</th>
                            <th>مبلغ الدفعة</th>
                            <th>البيان</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $trans)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $trans->payment_date }}</td>
                                <td>
                                    <span class="text-danger">-{{ number_format($trans->amount, 2) }}</span>
                                </td>
                                <td>{{ $trans->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>                
            </div>
        </div>
        <div class="card-footer">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- بحث متقدم -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">بحث متقدم</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('customer.invoice.filter') }}" id="searchForm" method="POST">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer->id }}" class="customer_id">
                <div class="row">
                    <div class="col-md-4 mb-1">
                        <label class="form-label">من</label>
                        <input type="date" class="form-control start_date" name="start_date" placeholder="YYY-MMM-DDD">
                    </div>
                    <div class="col-md-4 mb-1">
                        <label class="form-label">إلي</label>
                        <input type="date" class="form-control end_date" name="end_date" placeholder="YYY-MMM-DDD">
                    </div>
                    <div class="col-md-4 mb-1">
                        <label class="form-label">نوع الفاتورة</label>
                        <select name="invoice_type" class="form-select">
                            <option value="">اختر ...</option>
                            <option value="cash">كاش</option>
                            <option value="credit">آجل</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-1">
                        <label class="form-label">حالة الفاتورة</label>
                        <select name="invoice_staute" class="form-select">
                            <option value="">اختر ...</option>
                            <option value="1">مدفوعة</option>
                            <option value="unpaid">غير مدفوعة</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">بحث بكود الفاتورة ...</label>
                        <input type="text" class="form-control searchCode" name="searchCode" placeholder="بحث بكود الفاتورة ...">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-success waves-effect mt-2">بحث</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">فواتير العميل</h3>
            <div class="card-action">
                <a href="{{ route('customer.target.invoice.add', $customer->id) }}" class="btn btn-success waves-effect waves-float waves-light">
                    <i data-feather='plus'></i>
                    <span>إضافة فاتورة</span>
                </a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#PaymentBalance"
                    data-customer_id="{{ $customer->id }}"
                    class="paymentBtn btn btn-icon btn-primary waves-effect waves-float waves-light"
                    >
                    <i data-feather='credit-card'></i>
                    <span>دفع دفعة</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div class="table-invoices">
                    @include('customer.sales.invoice_table')
                </div>
            </div>
        </div>
        <div class="card-footer">
            {{ $invoices_list->links() }}
        </div>
    </div>

<!-- model payment balance -->
<div class="modal fade text-start modal-primary" id="PaymentBalance" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة دفعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customer.payment') }}" class="formSubmit" method="POST">
                @csrf
                <input type="hidden" name="customer_id" class="customer_id">
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
                    <div class="mb-1">
                        <label class="form-label">مبلغ الدفعة</label>
                        <input type="number" class="form-control" name="amount">
                    </div>
                    <div class="mb-1">
                        <label class="form-label">البيان</label>
                        <textarea name="description" class="form-control" cols="5" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">تأكيد العملية</button>
                </div>
            </form>
        </div>
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
            <form action="{{ route('customer.invoice.delete') }}" method="POST">
                @csrf
                <input type="hidden" name="id" class="id">
                <input type="hidden" name="customer_id" class="customer_id">
                <div class="modal-body">
                    <label class="form-label">هل أنت متأكد من عمل مرتجع لهذه الفاتورة لا يمكن التراجع عن هذه العملية ؟</label>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger waves-effect waves-float waves-light">تأكيد</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            // delete invoice
            $(document).on('click', '.delBtn', function(e){
                let id = $(this).data('id');
                let customer_id = $(this).data('customer_id');

                $("#delInvoice .id").val(id);
                $("#delInvoice .customer_id").val(customer_id);

            })

            // filter search 
            $(document).on('submit', '#searchForm', function(e){
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('filterByCustomer') }}",
                    method: 'POST',
                    data: formData,
                    beforeSend: function () {
                        $('#loading-excute').fadeIn(500);
                    },
                    success: function (response) {
                        $('.table-invoices').html(response);
                    },
                    error: function(xhr){
                        console.log(xhr);
                    },
                    complete: function(){
                        $('#loading-excute').fadeOut(500);
                        feather.replace();
                    }
                });
            });

            // delete invoice
            $(document).on('click', '.delBtn', function(e){
                let id = $(this).data('id');
                let customer_id = $(this).data('customer_id');

                $("#delInvoice .id").val(id);
                $("#delInvoice .customer_id").val(customer_id);

            })

            // payment Balance
            $(".paymentBtn").on('click', function(){
                let customer_id = $(this).data('customer_id');
                let opening_balance = $(this).data('opening_balance');
                $("#PaymentBalance .customer_id").val(customer_id);
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

            // item select only

            $(document).on('change', '.selectItem', function(){
                getRecoards();
            });

            // item select All
            $(document).on('change', '#selectAll' ,function(){

                $('.selectItem').prop('checked', this.checked);

                getRecoards();

            });

            function getRecoards(){
                let recardsIds = [];

                $.each($('.selectItem:checked'), function(){
                    recardsIds.push($(this).val());
                })

                $('.recardsIds').val(JSON.stringify(recardsIds));

                if(recardsIds.length > 0){
                    $(".exportData").attr('disabled', false);
                    $(".exportData").removeClass('disabled');
                }
                else {
                    $(".exportData").attr('disabled', true);
                    $(".exportData").addClass('disabled');
                }
            }

            
            $(document).on('submit', '.formSubmit', function(e){
                e.preventDefault();
                if(!$(this).find('.warehouse_id').val() && !$(this).find('.wallet_id').val() && !$(this).find('.amount').val()){
                    e.preventDefault();
                    toastr.info('يرجي ملئ بيانات الحقول المطلوبة !');
                }
                else {
                    $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
                    this.submit();
                }
            });
        })
    </script>
@endsection


