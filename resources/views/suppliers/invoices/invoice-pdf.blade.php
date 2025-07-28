<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة #{{ $invoice->invoice_code }}</title>
    <style>
        body {
            font-family: 'dejavusans', sans-serif;
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
            height: calc(100% - 40px);
        }

        @font-face {
            font-family: 'dejavusans';
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
            <h4>فاتورة شراء #{{ $invoice->invoice_code }}</h4>
        </div>
    
        <!-- Supplier & Invoice Info -->
        <div class="supplier-info">
            <p><strong>المورد:</strong> {{ $invoice->supplier->name }}</p>
            <p><strong>تاريخ الفاتورة:</strong> {{ $invoice->invoice_date }}</p>
            <p><strong>كود السجل الضريبي:</strong> {{ $app->Tax_number }}</p>
        </div>
    
        <!-- Costs Table -->
        {{-- <h4>تفاصيل التكاليف:</h4>
        <table>
            <thead>
                <tr>
                    <th>الوصف</th>
                    <th>السعر</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->costs as $cost)
                    <tr>
                        <td>{{ $cost->description }}</td>
                        <td>{{ number_format($cost->amount) }} EGP</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    
        <p class="total">إجمالي التكاليف: {{ number_format($invoice->cost_price) }} EGP</p> --}}
    
        <!-- Items Table -->
        <h4>المنتجات:</h4>
        <table>
            <thead>
                <tr>
                    <th>الصنف</th>
                    <th>المنتج</th>
                    <th>العدد</th>
                    <th>سعر الوحدة</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product->category->full_path }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->purchase_price) }} EGP</td>
                        <td>{{ number_format($item->purchase_price * $item->quantity) }} EGP</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    
        <p class="total">إجمالي الفاتورة: {{ number_format($invoice->total_amount - $invoice->cost_price) }} EGP</p>
    
        <!-- Note -->
        <div class="note">
            <p>مع أطيب التحيات، وخالص الشكر لتعاملكم الكريم معنا.</p>
        </div>
    </div>
</body>
</html>
