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
                            <p class="card-text mb-0">فاتورة مورد م:/ {{ $invoice->supplier->name }}</p>
                        </div>
                        <div class="mt-md-0 mt-2">
                            <h4 class="invoice-title">
                                رقم الفاتورة
                                <span class="invoice-number">#{{ $invoice->invoice_code }}</span>
                            </h4>
                            <div class="invoice-date-wrapper">
                                <p class="invoice-date-title">تاريخ الفاتورة</p>
                                <p class="invoice-date">{{ $invoice->invoice_date }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Header ends -->
                </div>

                <hr class="invoice-spacing">

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
                                    @foreach ($invoice->costs as $cost)
                                        <tr>
                                            <td>{{ $cost->expenseItem->name }}</td>
                                            <td>{{ number_format($cost->amount) }} EGP</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="invoice_cost_total text-center mt-2">
                                <p><strong>إجمالي سعر التكلفة :</strong> {{ number_format($invoice->cost_price) }} EGP</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="py-1">الصنف</th>
                                <th class="py-1">المنتج</th>
                                <th class="py-1">العدد / الكمية</th>
                                <th class="py-1">العرض</th>
                                <th class="py-1">الطول / القطر</th>
                                <th class="py-1">سعر الشراء</th>
                                <th class="py-1">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->product->category->full_path }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->size->width }}</td>
                                    <td>{{ $item->length }}</td>
                                    <td>{{ number_format($item->purchase_price) }} EGP</td>
                                    <td>{{ number_format($item->total_price) }} EGP</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Invoice Description ends -->

                <hr class="invoice-spacing">

                <div class="invoice_total text-center">
                    <p><strong>إجمالي الفاتورة :</strong> {{ number_format($invoice->total_amount) }} EGP</p>
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
                    <a href="{{ route('supplier.invoice.download', $invoice->id) }}" class="btn btn-outline-secondary w-100 mb-75 waves-effect">تحميل</a>
                    <a href="{{ route('supplier.invoice.edit', $invoice->id) }}" class="btn btn-outline-secondary w-100 mb-75 waves-effect">تعديل</a>
                </div>
            </div>
        </div>
        <!-- /Invoice Actions -->
    </div>
</section>


@endsection
