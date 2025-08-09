@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">تكلفة أسعار البضاعة</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض أسعار البضاعة</a>
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
        <h3 class="card-title">عرض أسعار البضاعة</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>التصنيف</th>
                    <th>المنتج</th>
                    <th>سعر الشراء الأساسي</th>
                    <th>سعر تكلفة الصنف</th>
                    <th>النسبة</th>
                    <th>سعر البيع المقترح</th>
                    <th>تعديل النسبة</th>
                </tr>
                @forelse ($stocks as $stock)
                    <tr>
                        <td>{{ $stock->category->full_path }}</td>
                        <td>{{ $stock->product->name }}</td>
                        <td>{{ $stock->cost->base_cost }}</td>
                        <td>{{ $stock->cost->cost_share }}</td>
                        <td>{{ $stock->cost->rate }} %</td>
                        <td>{{ $stock->cost->suggested_price ?? 0 }}</td>
                        <td>
                            <a href="#" data-bs-toggle="modal" data-cost_price="{{ $stock->cost->cost_share }}" data-stock_id="{{ $stock->id }}" data-bs-target="#editPrice" data-id="{{ $stock->id }}" class="btn btn-icon btn-success waves-effect waves-float waves-light editPriceBtn">
                                <i data-feather='edit'></i>
                                <span>تعديل سعر البيع</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="6">لا يوجد بضاعة مضافة بعد</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
</div>

<!-- model edit product rate -->
<div class="modal fade text-start modal-success" id="editPrice" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل نسبة البيع المقترح</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('product.Price.update') }}" method="POST">
                @csrf
                <input type="hidden" name="stock_id" class="stock_id">
                <input type="hidden" name="cost_price" class="cost_price">
                <div class="modal-body">
                    <label class="form-label">النسبة</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="rate">
                        <button class="btn btn-outline-primary waves-effect" type="button" readonly>%</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-float waves-light">حفظ وعرض البيع المقترح</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(function(){
        $(document).on('click', '.editPriceBtn', function(){
            let stock_id = $(this).attr('data-stock_id');
            let cost_price = $(this).attr('data-cost_price');
            $("#editPrice input[name='stock_id']").val(stock_id);
            $("#editPrice input[name='cost_price']").val(cost_price);
        });
    })
</script>
@endsection
