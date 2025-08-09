<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب المورد {{ $supplier->name }}</title>
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
            <p><strong>كشف حساب المورد :</strong> {{ $supplier->name }}</p>
            <p>
                <strong>من:</strong> <span>{{ $first_inv_date }}</span>
                <strong>إلي:</strong> <span>{{ $last_inv_date }}</span>
            </p>
        </div>
    
        <!-- Items Table -->
        <h4>الفواتير:</h4>
        <table>
            <thead>
                <tr>
                    <th>كود الفاتورة</th>
                    <th>تاريخ الفاتورة</th>
                    <th>نوع الفاتورة</th>
                    <th>إجمالي الفاتورة</th>
                    <th>المبلغ المدفوع</th>
                    <th>حالة الفاتورة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_code }}</td>
                        <td>{{ $invoice->invoice_date }}</td>
                        <td>
                            @if ($invoice->invoice_type === 'cash')
                                كاش
                            @elseif ($invoice->invoice_type === 'credit')
                                آجل
                            @else 
                                رصيد افتتاحي
                            @endif
                        </td>
                        <td>{{ number_format($invoice->total_amount_invoice) }} EGP</td>
                        <td>{{ number_format($invoice->paid_amount) }} EGP</td>
                        <td>
                            @if ($invoice->invoice_staute == 0)
                                <span class="badge badge-glow bg-danger">غير مدفوع</span>
                            @elseif($invoice->invoice_staute == 2)
                                <span class="badge badge-glow bg-warning">لم يتم التصفية</span>
                            @else
                                <span class="badge badge-glow bg-success">مدفوعة</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    
        <p class="total">إجمالي الرصيد المستحق : {{ number_format($supplier->account->current_balance) }} EGP</p>
    
        <!-- Note -->
        <div class="note">
            <p>مع أطيب التحيات، وخالص الشكر لتعاملكم الكريم معنا.</p>
        </div>
    </div>
</body>
</html>
