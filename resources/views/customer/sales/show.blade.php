@extends('layouts.app')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css-rtl/pages/app-invoice-print.css') }}">
    <style>
        #print-area {
            display: none;
        }
        .logo-wrapper .logo {
            width: 100px;
            height: 100px;
        }
        @media print {
            #print-area {
                display: block;
            }
        }
    </style>
@endsection

@section('content')
<section class="invoice-preview-wrapper">
    <div class="row invoice-preview">
        <!-- Invoice -->
        <div class="col-xl-9 col-md-8 col-12">
            <div class="card invoice-preview-card">
                <div class="card-body invoice-padding pb-0">
                    <!-- Header starts -->
                    <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                        <div>
                            <div class="logo-wrapper">
                                <x-logo-component />
                                <h3 class="text-success invoice-logo mt-2">{{ $app->company_name }}</h3>
                            </div>
                            <p class="card-text mb-25">{{ $app->company_info }}</p>
                            <p class="card-text mb-0">فاتورة عميل م:/ {{ $invoice->customer->name }}</p>
                        </div>
                        <div class="mt-md-0 mt-2">
                            <h4 class="invoice-title">
                                رقم الفاتورة
                                <span class="invoice-number">#{{ $invoice->code }}</span>
                            </h4>
                            <div class="invoice-date-wrapper">
                                <p class="invoice-date-title">تاريخ الفاتورة</p>
                                <p class="invoice-date">{{ $invoice->date }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Header ends -->
                </div>


                @if ($invoice->type !== 'opening_balance')
                    <div class="card-body invoice-padding pt-0">
                        <div class="row invoice-spacing">
                            <div class="col-lg-12 p-0 mt-2">
                                <h6 class="mb-2">تفاصيل التكاليف :</h6>
                                <table class="table table-bordered">
                                    <thead>
                                        <th>وصف التكلفة</th>
                                        <th>سعر التكلفة</th>
                                    </thead>
                                    <tbody>
                                        @forelse ($nonProfitCosts as $cost)
                                            <tr>
                                                <td>{{ $cost->expenseItem->name }}</td>
                                                <td>
                                                    @if ($invoice->discount_type !== null)
                                                        @if($invoice->discount_type == 'percent')
                                                            {{ number_format($invoice->total_amount * $invoice->discount_value / 100) }} EGP
                                                        @else
                                                            {{ number_format($invoice->discount_value) }} EGP
                                                        @endif
                                                    @else 
                                                        {{ number_format($invoice->amount) }} EGP
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center">لا توجد تكاليف</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="invoice_cost_total text-center mt-2">
                                    <p><strong>إجمالي سعر التكلفة :</strong> {{ number_format($invoice->cost_price) }} EGP</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="invoice-spacing">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="py-1">الصنف</th>
                                    <th class="py-1">المنتج</th>
                                    <th class="py-1">المقاس</th>
                                    <th class="py-1">العدد / الكمية</th>
                                    <th class="py-1">سعر البيع</th>
                                    <th class="py-1">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->product->category->full_path }}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->size->width ?? 0}}</td>
                                        @if ($item->unit_name === 'متر')
                                            <td>{{ $item->quantity }} متر</td>                                        
                                        @else
                                            <td>{{ $item->quantity }} {{ $item->unit_name }}</td>                                        
                                        @endif
                                        <td>{{ number_format($item->sale_price) }} EGP</td>
                                        <td>{{ number_format($item->total_price) }} EGP</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif


                <!-- Invoice Description ends -->

                <hr class="invoice-spacing">

                <div class="invoice_total text-center">
                    @if ($invoice->invoice_type !== 'opening_balance')
                        <p><strong>إجمالي الفاتورة :</strong> {{ number_format($invoice->total_amount) }} EGP</p>
                
                        @if($invoice->discount_value > 0)
                        <p><strong>الخصم :</strong>
                            @if($invoice->discount_type == 'percent')
                                {{ number_format($invoice->discount_value) }} %
                                ({{ number_format($invoice->total_amount * $invoice->discount_value / 100) }} EGP)
                            @else
                                {{ number_format($invoice->discount_value) }} EGP
                            @endif
                        </p>
                    
                        <p><strong>الإجمالي بعد الخصم :</strong>
                            {{ number_format(
                                $invoice->discount_type == 'percent' 
                                    ? $invoice->total_amount - ($invoice->total_amount * $invoice->discount_value / 100) 
                                    : $invoice->total_amount - $invoice->discount_value
                            ) }} EGP
                        </p>
                    @endif
                    
                
                    @else
                        <p><strong>إجمالي الرصيد الإفتتاحي :</strong> {{ number_format($invoice->total_amount) }} EGP</p>
                    @endif
                </div>
                

                <hr class="invoice-spacing">

                <!-- Invoice Note starts -->
                <div class="card-body invoice-padding pt-0">
                    <div class="row">
                        <div class="col-12">
                            <span class="fw-bold">ملاحظة :</span>
                            <p>مع أطيب التحيات، وخالص الشكر لتعاملكم الكريم معنا.</p>
                        </div>
                    </div>
                </div>
                <!-- Invoice Note ends -->
            </div>
        </div>
        <!-- /Invoice -->

        <!-- Invoice Actions -->
        <div class="col-xl-3 col-md-4 col-12 invoice-actions mt-md-0 mt-2 no-print">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('customer.invoice.download', $invoice->id) }}" class="btn btn-outline-secondary w-100 mb-75 waves-effect">تحميل</a>
                    <a href="{{ route('customer.invoice.edit', $invoice->id) }}" class="btn btn-outline-secondary w-100 mb-75 waves-effect">تعديل</a>
                </div>
            </div>
        </div>
        <!-- /Invoice Actions -->
    </div>
</section>


@endsection
