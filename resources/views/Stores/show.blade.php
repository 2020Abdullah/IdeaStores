@extends('layouts.app')

@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">حركات المخزن</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
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
    <section class="storeHouse">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">حركات الصنف {{ $stock->product->name }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>التاريخ</th>
                            <th>المورد</th>
                            <th>اسم المنتج</th>
                            <th>الكمية المحركة</th>
                            <th>البيان</th>
                        </tr>
                        @foreach ($stock_movments as $move)
                            <tr>
                                <td>{{ $move->created_at->format('Y-m-d') }}</td>
                                <td>{{ $move->supplier->name }}</td>
                                <td>
                                    @if ($move->type == 'in')
                                        إضافة إلي المخزن
                                    @else
                                        خصم من المخزن
                                    @endif
                                </td>
                                <td>{{ $move->quantity }}</td>
                                <td>
                                    @if ($move->note === 'شراء')
                                        فاتورة شراء
                                    @else
                                        فاتورة بيع
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $stock_movments->links() }}
            </div>
        </div>
    </section>
@endsection
