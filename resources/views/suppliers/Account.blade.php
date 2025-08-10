@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">كشف حساب المورد</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">حساب المورد</a>
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
            <h3 class="card-title">كشف حساب مورد : {{ $supplier->name }}</h3>
            <div class="card-action">
                <form action="{{ route('supplier.account.export') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $supplier->id }}" name="supplier_id">
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
                                <h3>الرصيد المستحق</h3>
                                <h4>{{ number_format($supplier->account->current_balance) }}</h4>
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
                            <th>طريقة الدفع</th>
                            <th>البيان</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($supplier->paymentTransactions as $trans)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $trans->payment_date }}</td>
                                <td>
                                    @if($trans->direction === 'out')
                                        <span class="text-danger">-{{ number_format($trans->amount, 2) }}</span>
                                    @else
                                        <span class="text-success">+{{ number_format($trans->amount, 2) }}</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($trans->method) }}</td>
                                <td>{{ $trans->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>                
            </div>
        </div>
    </div>

    <!-- الفواتير -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">فواتير المورد</h3>
            <div class="card-action">
                <a href="{{ route('supplier.target.invoice.add', $supplier->id) }}" class="btn btn-success waves-effect waves-float waves-light">
                    <i data-feather='plus'></i>
                    <span>إضافة فاتورة</span>
                </a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#PaymentBalance"
                    data-supplier_id="{{ $supplier->id }}"
                    data-
                    class="paymentBtn btn btn-icon btn-primary waves-effect waves-float waves-light creditOpenBtn"
                    >
                    <i data-feather='credit-card'></i>
                    <span>دفع دفعة</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>كود الفاتورة</th>
                        <th>تاريخ الفاتورة</th>
                        <th>اسم المورد</th>
                        <th>نوع الفاتورة</th>
                        <th>إجمالي الفاتورة</th>
                        <th>المبلغ المدفوع</th>
                        <th>حالة الفاتورة</th>
                        <th>إجراء</th>
                    </tr>
                    @foreach ($supplier_invoices as $inv)
                        <tr>
                            <td>{{ $inv->invoice_code }}</td>
                            <td>{{ $inv->invoice_date }}</td>
                            <td>{{ $inv->supplier->name }}</td>
                            <td>
                                @if ($inv->invoice_type === 'cash')
                                    <span>كاش</span>
                                @elseif($inv->invoice_type === 'credit')
                                    <span>آجل</span>
                                @else
                                    <span>رصيد افتتاحي</span>
                                @endif
                            </td>
                            <td>{{ number_format($inv->total_amount_invoice) }} EGP</td>
                            <td>{{ number_format($inv->paid_amount) }} EGP</td>
                            <td>
                                @if ($inv->invoice_staute == 0)
                                    <span class="badge badge-glow bg-danger">غير مدفوع</span>
                                @elseif($inv->invoice_staute == 2)
                                    <span class="badge badge-glow bg-warning">لم يتم التصفية</span>
                                @else
                                    <span class="badge badge-glow bg-success">مدفوعة</span>
                                @endif
                            </td>
                            <td>
                                @if ($inv->invoice_type !== 'opening_balance')
                                    <a href="{{ route('supplier.invoice.show', $inv->invoice_code) }}"
                                    class="btn btn-icon btn-info waves-effect waves-float waves-light editBtn"
                                    title="عرض">
                                        <i data-feather='eye'></i>
                                    </a>                                
                                @endif
    
                                <a href="{{ route('supplier.invoice.edit', $inv->id) }}"
                                   class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn"
                                   title="تعديل">
                                    <i data-feather='edit'></i>
                                </a>
                
                                {{-- <a href="#" data-bs-toggle="modal" data-bs-target="#delInvoice"
                                    data-id="{{ $inv->id }}"
                                    data-total_amount="{{ $inv->total_amount }}"
                                    data-supplier_id="{{ $inv->supplier_id }}"
                                   class="btn btn-icon btn-danger waves-effect waves-float waves-light delBtn"
                                   title="حذف">
                                    <i data-feather='trash-2'></i>
                                </a> --}}

                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $supplier_invoices->links() }}
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
            <form action="{{ route('supplier.invoice.payment') }}" method="POST">
                @csrf
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
                        <input type="text" class="form-control current_balance" name="current_balance" readonly>
                    </div>
                    <div class="mb-1 alert_container" style="display: none;">
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">
                                يوجد خطأ
                            </h4>
                            <div class="alert-body">
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">المديونية</label>
                        <input type="number" class="form-control total_balance" name="total_balance" value="{{ $supplier->account->current_balance }}" readonly>
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
            // $(".delBtn").on('click', function(){
            //     let id = $(this).data('id');
            //     let total_amount = $(this).data('total_amount');
            //     let supplier_id = $(this).data('supplier_id');
            //     $("#delInvoice .id").val(id);
            //     $("#delInvoice .supplier_id").val(supplier_id);
            //     $("#delInvoice .total_amount").val(total_amount);
            // })

            // payment Balance
            $(".paymentBtn").on('click', function(){
                let supplier_id = $(this).data('supplier_id');
                let opening_balance = $(this).data('opening_balance');
                $("#PaymentBalance .supplier_id").val(supplier_id);
                $("#PaymentBalance .openingBalance").val(opening_balance);
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
               let method = $(this).find('option:selected').attr('data-method');
               let current_balance = parseFloat($(this).find('option:selected').attr('data-balance')) || 0;
               let total_balance = parseFloat($(".total_balance").val()) || 0;
               $(".method").val(method)
               $(".balance_container .current_balance").val(current_balance)
               $(".balance_container").show(500);

               if(current_balance <= 0){
                    $(".alert_container").show(500);
                    $(".alert_container p").text('رصيد المحفظة غير كافي الخزنة سيصبح رصيد كل من المحفظة والخزنة بالسالب')
               }
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
        })
    </script>
@endsection


