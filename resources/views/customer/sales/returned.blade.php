@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">المرتجعات</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">مرتجعات العملاء</a>
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
        <form action="{{ route('customer.invoice.filter') }}" id="searchForm" method="POST">
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
        <h3 class="card-title">مرتجعات العملاء</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            @include('customer.sales.invoice_return_table')
        </div>
    </div>
    <div class="card-footer">
        {{ $invoices_list->links() }}
    </div>
</div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            // filter search 
            $(document).on('submit', '#searchForm', function(e){
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('customer.returned_invoices.filter') }}",
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
