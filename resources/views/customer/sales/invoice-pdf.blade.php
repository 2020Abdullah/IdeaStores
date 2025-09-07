<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة #{{ $invoice->code }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            unicode-bidi: embed;
            margin: 0;
            padding: 0;
        }

        .page {
            border: 4px solid #333;
            padding: 10px;
            margin: 10px;
            height: 100%;
        }

        @font-face {
            font-family: 'Arial';
            font-weight: normal;
            font-style: normal;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .invoice-header h2 {
            margin: 0;
        }

        .company-info, .supplier-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border: 1px solid #333;
            padding: 8px 5px;
            font-size: 12px;
        }

        th {
            background-color: #f0f0f0;
        }

        .total {
            font-weight: bold;
            font-size: 14px;
        }

        .note {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="invoice-header">
            <h2>{{ $app->company_name }}</h2>
            <p>{{ $app->company_info }}</p>
            <h4>فاتورة بيع #{{ $invoice->code }}</h4>
        </div>
    
        <!-- customer & Invoice Info -->
        <div class="customer-info">
            <p><strong>العميل:</strong> {{ $invoice->customer->name }}</p>
            <p><strong>تاريخ الفاتورة:</strong> {{ $invoice->date }}</p>
            <p>
                <strong>حالة الفاتورة:</strong>
                @if ($invoice->staute == 0)
                    <span>غير مدفوع</span>
                @elseif($invoice->staute == 2)
                    <span>لم يتم التصفية</span>
                @else
                    <span>مدفوعة</span>
                @endif
            </p>
        </div>

        <!-- Items Table -->
        <h4>المنتجات:</h4>
        <table>
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
        
        @php
        $originalAmount = $invoice->total_amount_without_discount;
        $discountAmount = 0;
        $finalAmount = $originalAmount;
    
        if ($invoice->discount_type && $invoice->discount_value) {
            if ($invoice->discount_type === 'percent') {
                $discountAmount = ($originalAmount * $invoice->discount_value / 100);
            } else {
                $discountAmount = $invoice->discount_value;
            }
            $finalAmount = $originalAmount - $discountAmount;
        }
    @endphp
    
    <!-- إجمالي الفاتورة -->
    
    @if($discountAmount > 0)
        <p class="total">
            إجمالي الفاتورة بعد الخصم: {{ number_format($finalAmount) }} EGP
        </p>
        <p class="total">
            قيمة الخصم: {{ number_format($discountAmount) }} EGP
        </p>
    @else 
        <p class="total">
            إجمالي الفاتورة: {{ number_format($finalAmount) }} EGP
        </p>
    @endif
    
        <!-- Note -->
        <div class="note">
            <p>مع أطيب التحيات، وخالص الشكر لتعاملكم الكريم معنا.</p>
        </div>
    </div>
</body>
</html>
