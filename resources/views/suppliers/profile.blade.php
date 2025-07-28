@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">معلومات عن المورد</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">المورد</a>
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
            <h3 class="card-title">المورد : {{ $supplier->name }}</h3>
        </div>
        <hr />
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-1 text-center">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-balance">
                                <h3>الرصيد المستحق</h3>
                                <h4>{{ number_format($supplier->account->total_capital_balance) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-1">
                    <label class="form-label">المورد ID</label>
                    <input type="text" class="form-control" value="{{ $supplier->id }}" readonly>
                </div>
                <div class="col-6 mb-1">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control" value="{{ $supplier->phone ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-6 mb-1">
                    <label class="form-label">رقم الواتساب</label>
                    <input type="text" class="form-control" value="{{ $supplier->whatsUp ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-6 mb-1">
                    <label class="form-label">اسم الشركة</label>
                    <input type="text" class="form-control" value="{{ $supplier->busniess_name ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-6 mb-1">
                    <label class="form-label">نشاط الشركة</label>
                    <input type="text" class="form-control" value="{{ $supplier->busniess_type ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-6">
                    <label class="form-label">مقر الشركة</label>
                    <input type="text" class="form-control" value="{{ $supplier->place ?? 'لا يوجد' }}" readonly>
                </div>
                <div class="col-12">
                    <label class="form-label">ملاحظات</label>
                    <textarea class="form-control" cols="5" rows="5" readonly>{{ $supplier->notes }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">فواتير المورد</h3>
            <div class="card-action">
                <a href="{{ route('supplier.target.invoice.add', $supplier->id) }}" class="btn btn-success waves-effect waves-float waves-light">
                    إضافة فاتورة جديدة 
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
                        <th>المبلغ المدفوع</th>
                        <th>إجمالي الفاتورة</th>
                        <th>المتبقي</th>
                        <th>حالة الفاتورة</th>
                        <th>إجراء</th>
                    </tr>
                    @foreach ($supplier_invoices as $inv)
                        <tr>
                            <td>{{ $inv->invoice_code }}</td>
                            <td>{{ $inv->invoice_date }}</td>
                            <td>{{ $inv->supplier->name }}</td>
                            <td>{{ $inv->invoice_type === 'cash' ? 'كاش' : 'اجل'}}</td>
                            <td>{{ number_format($inv->paid_amount) }} EGP</td>
                            <td>{{ number_format($inv->total_amount) }} EGP</td>
                            <td>{{ number_format($inv->total_amount - $inv->paid_amount)}} EGP</td>
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
                                
                                <a href="{{ route('supplier.invoice.show', $inv->id) }}"
                                   class="btn btn-icon btn-info waves-effect waves-float waves-light editBtn"
                                   title="عرض">
                                    <i data-feather='eye'></i>
                                </a>
    
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

                                @if ($inv->invoice_staute !== 1)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#PaymentInvoice"
                                    data-id="{{ $inv->id }}"
                                    data-supplier_id="{{ $inv->supplier_id }}"
                                    data-total_amount="{{ $inv->total_amount }}"
                                    class="btn btn-icon btn-primary waves-effect waves-float waves-light creditBtn"
                                    >
                                        <i data-feather='credit-card'></i>
                                    </a>
                                @endif
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
            // $(".delBtn").on('click', function(){
            //     let id = $(this).data('id');
            //     let total_amount = $(this).data('total_amount');
            //     let supplier_id = $(this).data('supplier_id');
            //     $("#delInvoice .id").val(id);
            //     $("#delInvoice .supplier_id").val(supplier_id);
            //     $("#delInvoice .total_amount").val(total_amount);
            // })

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
        })
    </script>
@endsection


