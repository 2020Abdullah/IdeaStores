<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة #{{ $invoice->invoice_code }}</title>
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
        </div>
    
        <!-- Supplier & Invoice Info -->
        <div class="supplier-info">
            <p><strong>كشف حساب المورد :</strong> {{ $invoice->supplier->name }}</p>
            <p>
                <strong>من:</strong> <span>{{ $invoice->first_inv_date }}</span>
                <strong>إلي:</strong> <span>{{ $invoice->last_inv_date }}</span>
            </p>
        </div>
    
        <!-- Items Table -->
        <h4>الفواتير:</h4>
        <table>
            <thead>
                <tr>
                    <th>الصنف</th>
                    <th>المنتج</th>
                    <th>العرض</th>
                    <th>الطول</th>
                    <th>الكمية</th>
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
                        <td>{{ $item->size->width }}</td>
                        <td>{{ $item->length }}</td>
                        <td>{{ number_format($item->purchase_price) }} EGP</td>
                        <td>{{ number_format($item->total_price) }} EGP</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    
        <p class="total">إجمالي الفاتورة: {{ number_format($invoice->items->sum('total_price')) }} EGP</p>
    
        <!-- Note -->
        <div class="note">
            <p>مع أطيب التحيات، وخالص الشكر لتعاملكم الكريم معنا.</p>
        </div>
    </div>
</body>
</html>
