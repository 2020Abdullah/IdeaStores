@extends('layouts.app')


@section('content-title')
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">حركات المحفظة</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="#">عرض معاملات المحفظة</a>
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
            <h3 class="card-title">رصيد المحفظة</h3>
        </div>
        <hr />
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="card-balance">
                        <h3>الرصيد الحالي</h3>
                        <h4>{{ number_format($wallet->movements->sum('amount')) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- show wallets -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">سجل حركات المحفظة: {{ $wallet->name }}</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>المبلغ</th>
                            <th>الوصف</th>
                            <th>المرجع</th>
                        </tr>
                    </thead>
                    @foreach($wallet->movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('Y-m-d - h:i a') }}</td>
                            <td>
                                @if($movement->direction === 'in')
                                    <span class="badge bg-success">إضافة</span>
                                @else
                                    <span class="badge bg-danger">خصم</span>
                                @endif
                            </td>
                            <td>{{ number_format($movement->amount, 2) }}</td>
                            <td>{{ $movement->note ?? '-' }}</td>
                            <td>
                                @if($movement->source_code)
                                    @if ($movement->note == 'فاتورة شراء')
                                        <a href="{{ route('supplier.invoice.show', $movement->source_code) }}">
                                            # {{ $movement->source_code }}
                                        </a>
                                    @else
                                        
                                    @endif
                                    
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</section>

@endsection
