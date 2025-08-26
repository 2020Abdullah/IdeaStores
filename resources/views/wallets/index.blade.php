@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">الحسابات البنكية</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">
                        <a href="#">عرض الحسابات البنكية</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<section class="wallets">     
    <!-- show wallets -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">الحسابات البنكية</h3>
            <div class="card-action">
                <button type="button" class="btn btn-success waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addWallet">
                    إضافة محفظة 
                </button>
                <button type="button" class="btn btn-primary waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#transfer">
                    تسوية رصيد 
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>اسم المحفظة</th>
                        <th>الوصف</th>
                        <th>الرصيد الحالي</th>
                        <th>إجراء</th>
                    </tr>
                    @foreach ($wallets as $wallet)
                        <tr>
                            <td>{{ $wallet->name }}</td>
                            <td>{{ $wallet->details }}</td>
                            <td>{{ number_format($wallet->balance) }}</td>
                            <td>
                                <a href="{{ route('wallet.transactions.show', $wallet->id) }}"
                                    class="btn btn-icon btn-info waves-effect waves-float waves-light"
                                    >
                                     <i data-feather='eye'></i>
                                 </a>
                                 
                                 <a href="#"
                                 class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn"
                                 data-bs-toggle="modal" data-bs-target="#editWallet"
                                 data-wallet_id="{{ $wallet->id }}" 
                                 data-name="{{ $wallet->name }}"
                                 data-details="{{ $wallet->details }}"
                                 >
                                    <i data-feather='edit'></i>
                                </a>     

                                <a href="#"
                                    class="addBalanceBtn btn btn-icon btn-primary waves-effect waves-float waves-light"
                                    data-bs-toggle="modal" data-bs-target="#addBalance"
                                    data-wallet_id="{{ $wallet->id }}" 
                                >
                                    <i data-feather='credit-card'></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</section>

<!-- model Add wallet -->
<div class="modal fade text-start modal-success" id="addWallet" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة محفظة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('wallet.store') }}" class="formSubmit" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">اسم المحفظة *</label>
                        <input type="text" class="form-control name" name="name" placeholder="مثال: فوادفون كاش">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">رقم المحفظة أو الحساب البنكي (اختيارى)</label>
                        <input type="text" class="form-control" name="details" placeholder="تفاصيل الحساب">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model edit wallet -->
<div class="modal fade text-start modal-success" id="editWallet" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل بيانات المحفظة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('wallet.update') }}" class="formSubmit" method="POST">
                @csrf
                <input type="hidden" value="" class="wallet_id" name="wallet_id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">اسم المحفظة *</label>
                        <input type="text" class="form-control name" name="name" placeholder="مثال: فوادفون كاش">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">رقم المحفظة أو الحساب البنكي (اختيارى)</label>
                        <input type="text" class="form-control details" name="details" placeholder="تفاصيل الحساب">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model add balance -->
<div class="modal fade text-start modal-success" id="addBalance" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة رصيد إلي المحفظة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('wallet.balance.store') }}" class="formAmount" method="POST">
                @csrf
                <input type="hidden" value="" class="wallet_id" name="wallet_id">
                <div class="modal-body">
                    
                    <!-- اختيار الخزنة -->
                    <div class="mb-2">
                        <label class="form-label">اختر الخزنة</label>
                        <select name="warehouse_id" class="form-control select2">
                            <option value="">-- اختر الخزنة --</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- المبلغ -->
                    <div class="mb-2">
                        <label class="form-label">المبلغ</label>
                        <input type="number" class="form-control amount" value="0" name="amount" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">
                        حفظ البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- model transfer balance-->
<div class="modal fade text-start modal-success" id="transfer" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحويل رصيد إلي محفظة أخرى</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('wallet.transfer') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">من حساب المحفظة</label>
                        <select name="wallet_id_from" class="form-select">
                            <option value="" selected>اختر محفظة ...</option>
                            @foreach ($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">إلي حساب المحفظة</label>
                        <select name="wallet_id_to" class="form-select">
                            <option value="" selected>اختر محفظة ...</option>
                            @foreach ($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">المبلغ</label>
                        <input type="text" class="form-control" name="balance">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">البيان</label>
                        <textarea name="notes" class="form-control" cols="5" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script>
        $(document).ready(function(){
            $(document).on('click', '.editBtn', function(e){
                let wallet_id = $(this).data('wallet_id');
                let name = $(this).data('name');
                let details = $(this).data('details');

                $("#editWallet .wallet_id").val(wallet_id);
                $("#editWallet .name").val(name);
                $("#editWallet .details").val(details);
            });

            $(document).on('click', '.addBalanceBtn', function(e){
                let wallet_id = $(this).data('wallet_id');
                $("#addBalance .wallet_id").val(wallet_id);
            });

            $(document).on('submit', '.formSubmit', function(e){
                e.preventDefault();
                if(!$(this).find('.name').val()){
                    e.preventDefault();
                    toastr.info('يرجي ملئ بيانات الحقول !');
                }
                else {
                    $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
                    e.currentTarget.submit();
                }
            });

        $(document).on('submit', '.formAmount', function(e){
            e.preventDefault();

            let amount = parseFloat($(this).find('.amount').val());
            let warehouse = $(this).find('select[name="warehouse_id"]').val();

            if(!warehouse){
                toastr.info('يرجي اختيار الخزنة أولاً!');
                return;
            }

            if(!amount || amount <= 0){
                toastr.info('يرجي إدخال مبلغ أكبر من صفر!');
                return;
            }

            // لو التحقق سليم
            $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
            e.currentTarget.submit();
        });

        });
    </script>
@endsection
