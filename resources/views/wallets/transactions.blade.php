@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">{{ $wallet->name }}</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('wallets.index') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض سجل حركات المحفظة</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="card-balance">
                    <h3>رصيد المحفظة</h3>
                    <h4>{{ number_format($wallet->balance, 2) }} EGP</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">عرض سجل حركات المحفظة</h3>
        <div class="card-action">
            <select name="transaction_type" class="transaction_type form-select" data-wallet_id="{{ $wallet->id }}" style="width: 200px">
                <option value="">الكل</option>
                <option value="payment">مدفوعات</option>
                <option value="expense">مصروفات</option>
                <option value="purchase">مشتريات</option>
                <option value="sale">مبيعات</option>
                <option value="refund">مرتجعات</option>
                <option value="transfer">التحويلات</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الحساب</th>
                        <th>نوع المعاملة</th>
                        <th>الاتجاه</th>
                        <th>المبلغ</th>
                        <th>البيان</th>
                        <th>الكود المرجعي</th>
                    </tr>
                </thead>
                <tbody>
                    @include('wallets.trans_table')
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $transactions->links() }}
    </div>
</div>
@endsection

@section('js')
    <script>
        $(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // filter search 
            $(document).on('change', '.transaction_type', function(e){
                e.preventDefault();
                let type = $(this).val();
                let wallet_id = $(this).data('wallet_id');
                $.ajax({
                    url: "{{ route('wallet.transactions.filter') }}",
                    method: 'POST',
                    data: {
                        type: type,
                        wallet_id: wallet_id,
                    },
                    beforeSend: function () {
                        $('#loading-excute').fadeIn(500);
                    },
                    success: function (response) {
                        $('.table-responsive tbody').html(response);
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
        })
    </script>
@endsection
