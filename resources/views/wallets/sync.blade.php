@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">صفحة ربط الخزن بالحسابات</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">
                        <a href="#">ربط الحسابات البنكية</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<section class="wallets">     
    <!-- sync wallets & warehouse -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">ربط الحسابات البنكية بالخزن</h3>
        </div>
        <form action="#" class="formSubmit" method="POST">
            @csrf
            <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label" for="name">الخزن</label>
                        <select name="Warehouse_ids[]" class="select2 form-select select2-hidden-accessible warehouse_ids" multiple="" tabindex="-1" aria-hidden="true">
                            <option value="">اختر الخزنة ...</option>                                
                            @foreach ($warehouse_list as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>                                
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="name">الحسابات البنكية</label>
                        <select name="wallets_ids[]" class="select2 form-select select2-hidden-accessible wallet_ids" multiple="" tabindex="-1" aria-hidden="true">
                            <option value="">اختر الحساب البنكي ...</option>                                
                            @foreach ($wallets_list as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>                                
                            @endforeach
                        </select>
                    </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btnSubmit btn btn-success waves-effect waves-float waves-light">حفظ البيانات</button>
            </div>
        </form>
    </div>

<!-- show wallets & warehouse -->
<div class="card">
    <div class="card-body">
        <div class="accordion accordion-margin" id="accordionMargin" data-toggle-hover="true">
            @foreach($warehouse_list as $warehouse)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $warehouse->id }}">
                        <button class="accordion-button collapsed" type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse{{ $warehouse->id }}" 
                                aria-expanded="false" 
                                aria-controls="collapse{{ $warehouse->id }}">
                            {{ $warehouse->name }}
                        </button>
                    </h2>
                    <div id="collapse{{ $warehouse->id }}" class="accordion-collapse collapse" 
                         aria-labelledby="heading{{ $warehouse->id }}" 
                         data-bs-parent="#accordionMargin">
                        <div class="accordion-body">
                            @if($warehouse->wallets->count())
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>اسم المحفظة</th>
                                            <th>رقم الحساب / التفاصيل</th>
                                            <th>الرصيد الحالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($warehouse->wallets as $wallet)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('wallet.transactions.show', $wallet->id) }}">
                                                        {{ $wallet->name }}
                                                    </a>
                                                </td>
                                                <td>{{ $wallet->details ?? '-' }}</td>
                                                <td>{{ number_format($wallet->balance, 2) }} EGP</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>لا توجد حسابات مرتبطة بهذه الخزنة.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

</section>

@endsection

@section('js')
<script>
$('.warehouse_ids').select2({
    placeholder: "اختر الخزن",
    allowClear: true,
    width: '100%' 
});
$('.wallet_ids').select2({
    placeholder: "اختر الحسابات البنكية",
    allowClear: true,
    width: '100%' 
});

$(document).on('submit', '.formSubmit', function(e){
    e.preventDefault();

    let warehousesSelected = $(this).find('.warehouse_ids').val(); // مصفوفة العناصر المختارة
    let walletsSelected = $(this).find('.wallet_ids').val();       // مصفوفة العناصر المختارة

    // تحقق إذا كان أي حقل فارغ
    if(!warehousesSelected || warehousesSelected.length === 0 || !walletsSelected || walletsSelected.length === 0){
        toastr.info('يرجى اختيار الخزن والحسابات البنكية للربط!');
        return false;
    }

    // إذا تم الاختيار في كلا الحقلين
    $(this).find('.btnSubmit').prop('disabled', true).addClass('disabled');
    e.currentTarget.submit();
});

</script>
@endsection
