@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">{{ $warehouse->name }}</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('warehouse.index') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض المحافظ</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<section class="warehouse_wallets">     
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $warehouse->name }}</h3>
        </div>
        <hr />
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="card-balance">
                        <h3>رصيد الخزنة</h3>
                        <h4>{{ number_format($warehouse->account->transactions->sum('amount') ) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- show wallets -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">المحافظ المرتبطة بالخزنة</h3>
            <div class="card-action">
                <button type="button" class="btn btn-success waves-effect waves-float waves-light" data-bs-toggle="modal" data-bs-target="#addWallet">
                    إضافة محفظة 
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>اسم المحفظة</th>
                        <th>نوع المحفظة</th>
                        <th>الوصف</th>
                        <th>الرصيد الحالي</th>
                        <th>إجراء</th>
                    </tr>
                    @foreach ($warehouse->account->wallets as $wallet)
                        <tr>
                            <td>{{ $wallet->name }}</td>
                            <td>{{ $wallet->method }}</td>
                            <td>{{ $wallet->details }}</td>
                            <td>{{ number_format($wallet->movements->sum('amount')) }}</td>
                            <td>
                                <a href="{{ route('wallet.show', $wallet->id) }}"
                                    class="btn btn-icon btn-info waves-effect waves-float waves-light"
                                    >
                                     <i data-feather='eye'></i>
                                 </a>
                                 
                                 <a href="#"
                                 class="btn btn-icon btn-success waves-effect waves-float waves-light editBtn"
                                 data-bs-toggle="modal" data-bs-target="#editWallet"
                                 data-wallet_id="{{ $wallet->id }}" 
                                 data-name="{{ $wallet->name }}"
                                 data-method="{{ $wallet->method }}"
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
            <form action="{{ route('wallet.store') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $warehouse->account->id }}" name="account_id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">اسم المحفظة *</label>
                        <input type="text" class="form-control" name="name" placeholder="مثال: فوادفون كاش">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">نوع المحفظة *</label>
                        <select name="method" class="form-select method">
                            <option value="">اختر ...</option>
                            <option value="cash">كاش</option>
                            <option value="vodafone_cash">فوادفون كاش</option>
                            <option value="bank">الحساب البنكي</option>
                            <option value="instapay">انستا باى</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">رقم المحفظة أو الحساب البنكي (اختيارى)</label>
                        <input type="text" class="form-control" name="details" placeholder="تفاصيل الحساب">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">الرصيد الحالي (اختيارى)</label>
                        <input type="text" class="form-control" name="current_balance">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
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
            <form action="{{ route('wallet.update') }}" method="POST">
                @csrf
                <input type="hidden" value="" class="wallet_id" name="wallet_id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">اسم المحفظة *</label>
                        <input type="text" class="form-control name" name="name" placeholder="مثال: فوادفون كاش">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">نوع المحفظة *</label>
                        <select name="method" class="form-select method">
                            <option value="">اختر ...</option>
                            <option value="cash">كاش</option>
                            <option value="vodafone_cash">فوادفون كاش</option>
                            <option value="bank">الحساب البنكي</option>
                            <option value="instapay">انستا باى</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">رقم المحفظة أو الحساب البنكي (اختيارى)</label>
                        <input type="text" class="form-control details" name="details" placeholder="تفاصيل الحساب">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
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
            <form action="{{ route('wallet.balance.add') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $warehouse->id }}" name="warehouse_id">
                <input type="hidden" value="" class="wallet_id" name="wallet_id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">المبلغ</label>
                        <input type="text" class="form-control balance" name="balance">
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
        $(document).on('click', '.editBtn', function(){
            let wallet_id = $(this).data('wallet_id');
            let name = $(this).data('name');
            let method = $(this).data('method');
            let details = $(this).data('details');

            $('#editWallet .wallet_id').val(wallet_id);
            $('#editWallet .name').val(name);
            $('#editWallet .method').val(method);
            $('#editWallet .details').val(details);
        });

        $(document).on('click', '.addBalanceBtn', function(){
            let wallet_id = $(this).data('wallet_id');
            $('#addBalance .wallet_id').val(wallet_id);
        })
    </script>
@endsection
