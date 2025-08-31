@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">حركات المخزن</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('stock.index') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">حركات الصنف</a>
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
                        <h3>الكمية المتاحة</h3>
                        @if ($stock->unit->name === 'سنتيمتر')
                            <h4>{{ $stock->movements()->sum('quantity') }} متر</h4>   
                        @else
                            <h4>{{ $stock->movements()->sum('quantity') }} {{ $stock->unit->name }}</h4>   
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="storeHouse">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">حركات الصنف {{ $stock->product->name }}</h3>
            </div>
            <div class="card-body">
                <form action="#" id="searchForm" method="POST">
                    @csrf
                    <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                    <div class="row">
                        <div class="col-md-3 mb-1">
                            <label class="form-label">من</label>
                            <input type="date" class="form-control start_date" name="start_date" placeholder="YYY-MMM-DDD">
                        </div>
                        <div class="col-md-3 mb-1">
                            <label class="form-label">إلي</label>
                            <input type="date" class="form-control end_date" name="end_date" placeholder="YYY-MMM-DDD">
                        </div>
                        <div class="col-md-5 mb-1">
                            <label class="form-label">نوع الحركة</label>
                            <select name="moveType" class="form-select">
                                <option value="">اختر ...</option>
                                <option value="in">وارد</option>
                                <option value="out">صادر</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-1">
                            <button type="submit" class="btn btn-outline-success waves-effect mt-2">بحث</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    @include('stocks.transaction_table')
                </div>
            </div>
            <div class="card-footer">
                {{ $stock_movments->links() }}
            </div>
        </div>
    </section>
@endsection

@section('js')
<script>
    $(document).ready(function(){
        // filter search 
        $(document).on('submit', '#searchForm', function(e){
            e.preventDefault();
            let formData = $(this).serialize();
            $.ajax({
                url: "{{ route('transction.filter') }}",
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