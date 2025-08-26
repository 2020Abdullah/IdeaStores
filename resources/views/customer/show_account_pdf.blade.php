<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب عميل: {{ $customer->name }}</title>
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

        .supplier-info {
            text-align: center;
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
        .invoice-header img {
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto 10px auto;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="invoice-header">
            <img src="{{ public_path('assets/images/web/logo.png') }}" alt="Logo" style="width: 100px; height: 100px; display: block; margin: 0 auto 10px auto;">
            <h2>{{ $app->company_name }}</h2>
            <p>{{ $app->company_info }}</p>
        </div>
    
        <!-- Supplier & Invoice Info -->
        <div class="supplier-info">
            <p><strong>كشف حساب عميل :</strong> {{ $customer->name }}</p>
            <p>
                <strong>من  : </strong> <span>{{ $first_inv_date }}</span>
                <strong>إلي : </strong> <span>{{ $last_inv_date }}</span>
            </p>
        </div>
    
        <!-- invoices Table -->
        <h4>الفواتير:</h4>
        <table>
            <thead>
                <tr>
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
                        <td>{{ $invoice->date }}</td>
                        <td>
                            @if ($invoice->type === 'cash')
                                كاش
                            @elseif ($invoice->type === 'credit')
                                آجل
                            @else 
                                رصيد افتتاحي
                            @endif
                        </td>
                        <td>{{ number_format($invoice->total_amount) }} EGP</td>
                        <td>{{ number_format($invoice->paid_amount) }} EGP</td>
                        <td>
                            @if ($invoice->staute == 0)
                                <span class="badge badge-glow bg-danger">غير مدفوع</span>
                            @elseif($invoice->staute == 2)
                                <span class="badge badge-glow bg-warning">لم يتم التصفية</span>
                            @else
                                <span class="badge badge-glow bg-success">مدفوعة</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- payments Table -->
        <h4>الدفعات:</h4>
        <table>
            <thead>
                <tr>
                    <th>رقم الدفعة</th>
                    <th>تاريخ الدفعة</th>
                    <th>مبلغ الدفعة</th>
                    <th>البيان</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customer->paymentTransactions as $trans)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $trans->payment_date }}</td>
                        <td>
                            <span class="text-danger">-{{ number_format($trans->amount, 2) }}</span>
                        </td>
                        <td>{{ $trans->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    
        <p class="total">إجمالي الرصيد : {{ number_format($customer->balance, 2) }} EGP</p>
    
        <!-- Note -->
        <div class="note">
            <p>مع أطيب التحيات، وخالص الشكر لتعاملكم الكريم معنا.</p>
        </div>
    </div>
</body>
</html>
