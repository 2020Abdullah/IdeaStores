@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">فواتير المبيعات</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">فواتير المبيعات</a>
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
        <form action="#" id="searchForm" method="POST">
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
                    <label class="form-label">بحث بكود الفاتورة أو اسم العميل ...</label>
                    <input type="text" class="form-control searchText" name="searchText" placeholder="بحث بالإسم أو بالكود ...">
                </div>
            </div>
           <button type="submit" class="btn btn-outline-success waves-effect mt-2">بحث</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">فواتير المبيعات</h3>
        <div class="card-action">
            <a href="{{ route('customer.invoice.add') }}" class="btn btn-success waves-effect waves-float waves-light">
                إضافة فاتورة بيع 
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            @include('customer.sales.invoice_table')
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
                <input type="hidden" name="supplier_id" class="supplier_id">
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
                let supplier_id = $(this).data('supplier_id');

                $("#delInvoice .id").val(id);
                $("#delInvoice .supplier_id").val(supplier_id);

            })


            // filter search 
            $(document).on('submit', '#searchForm', function(e){
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('customer.invoice.filter') }}",
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
