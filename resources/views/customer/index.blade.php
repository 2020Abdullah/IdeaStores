@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">العملاء</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">العملاء</a>
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
        <h3 class="card-title">العملاء</h3>
        <div class="card-action">
            <a href="{{ route('customer.add') }}" class="btn btn-success waves-effect waves-float waves-light">
                إضافة عميل جديد 
            </a>
            {{-- <a href="{{ route('download.customer.Template') }}" class="btn btn-info waves-effect waves-float waves-light">
                تنزيل قالب 
            </a>
            <a href="#" data-bs-toggle="modal" data-bs-target="#importFile" class="btn btn-warning waves-effect waves-float waves-light">
                استيراد بيانات
            </a> --}}
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                        </div>
                    </th>
                    <th>اسم العميل</th>
                    <th>الرصيد</th>
                    <th>إجراء</th>
                </tr>
                @forelse ($customer_list as $c)
                    <tr>
                        <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input selectItem" type="checkbox" value="{{ $c->id }}" id="select{{$c->id}}">
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('customer.account.show', $c->id) }}">
                                {{ $c->name }}
                            </a>
                        </td>
                        <td>{{ number_format(-$c->account->current_balance ) }}</td>
                        <td>
                            <a href="{{ route('customer.edit', $c->id) }}" class="btn btn-success waves-effect">
                                <i data-feather='edit'></i>
                                <span>تعديل بيانات العميل</span>
                            </a>
                            <a href="{{ route('supplier.profile', $c->id) }}" class="btn btn-info waves-effect">
                                <i data-feather='eye'></i>
                                <span>بيانات المورد</span>
                            </a>
                            <a href="{{ route('supplier.target.invoice.add', $c->id) }}" class="btn btn-success waves-effect waves-float waves-light">
                                <i data-feather='plus'></i>
                                <span>إضافة فاتورة</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="4">لا يوجد عملاء مضافة</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
</div>

<!-- model import file -->
<div class="modal fade text-start modal-success" id="importFile" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">استيراد شيت اكسيل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('import.supplier') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">الملف</label>
                        <input type="file" class="form-control" name="file">
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

{{-- @section('js')
<script>
    // // delete category 
    // $(document).on('click', '.delBtn' ,function(){
    //     let id = $(this).attr('data-id');
    //     $("#deleteCate .id").val(id);
    // })
    
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

</script>
@endsection --}}
