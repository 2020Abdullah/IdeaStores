@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css-rtl/flatpickr.min.css') }}">
<style>
    @media (max-width: 768px) {
        #salesChart {
            height: 250px !important;
        }
    }

    @media (min-width: 769px) {
        #salesChart {
            height: 400px !important;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard">
    <h2>إحصائيات</h2>
    <div class="row text-center">
        <div class="col-md-4">
            <div class="card">
                <a href="{{ route('supplier.index') }}">
                    <div class="card-body">
                        <h4 class="mb-2">الموردين</h4>
                        <h3>{{ $suppliersCount }}</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="{{ route('supplier.invoice.index') }}">
                    <div class="card-body">
                        <h4 class="mb-2">فواتير الموردين</h4>
                        <h3>{{ $invoicesCount }}</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="{{ route('customer.index') }}">
                    <div class="card-body">
                        <h4 class="mb-2">العملاء</h4>
                        <h3>{{ $customerCount }}</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="{{ route('customer.invoice.index') }}">
                    <div class="card-body">
                        <h4 class="mb-2">فواتير المبيعات</h4>
                        <h3>{{ $customerInvoiceCount }}</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="{{ route('customer.invoice.index') }}">
                    <div class="card-body">
                        <h4 class="mb-2">المبيعات</h4>
                        <h3>{{ number_format($salesCount) }}</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="#">
                    <div class="card-body">
                        <h4 class="mb-2">الأرباح</h4>
                        <h3>{{ number_format($profitCount) }}</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="{{ route('dues.debts') }}">
                    <div class="card-body">
                        <h4 class="mb-2">مستحقاتنا</h4>
                        <h3>{{ number_format($receivables) }}</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="{{ route('external.debts') }}">
                    <div class="card-body">
                        <h4 class="mb-2">الديون الخارجية</h4>
                        <h3>{{ number_format($payables) }}</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="{{ route('expenses.items') }}">
                    <div class="card-body">
                        <h4 class="mb-2">المصروفات</h4>
                        <h3>{{ number_format($totalExpenses) }}</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>تحليل المبيعات</h5>
            <input type="text" id="dateRange" class="form-control" placeholder="اختر الفترة" style="width: 250px;">
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="120"></canvas>
        </div>
    </div>    
    
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/flatpickr.js') }}"></script>
<script src="{{ asset('assets/js/chart.js') }}"></script>
<script>
$(function() {
    let ctx = document.getElementById('salesChart').getContext('2d');
    let chart;

    // تهيئة Flatpickr
    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: [new Date(new Date().setDate(new Date().getDate() - 6)), new Date()],
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                let start = selectedDates[0].toLocaleDateString('en-CA'); 
                let end = selectedDates[1].toLocaleDateString('en-CA');
                loadChart(start, end);
            }
        }
    });

    // تحميل افتراضي عند الدخول
    let defaultStart = new Date(new Date().setDate(new Date().getDate() - 6)).toLocaleDateString('en-CA');
    let defaultEnd = new Date().toLocaleDateString('en-CA');
    loadChart(defaultStart, defaultEnd);

    function loadChart(start, end) {
        fetch(`/dashboard/sales-chart?start=${start}&end=${end}`)
            .then(response => response.json())
            .then(data => {
                let labels = data.map(item => item.day);
                let sales = data.map(item => parseFloat(item.total_sales));
                let profit = data.map(item => parseFloat(item.total_profit));

                // حساب النسبة لكل يوم
                let profitRatio = sales.map((s, i) => {
                    return s > 0 ? ((profit[i] / s) * 100).toFixed(2) : 0;
                });

                if (chart) {
                    chart.destroy();
                }

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'المبيعات',
                                data: sales,
                                backgroundColor: '#4e73df',
                                yAxisID: 'y'
                            },
                            {
                                label: 'الأرباح',
                                data: profit,
                                backgroundColor: '#1cc88a',
                                yAxisID: 'y'
                            },
                            {
                                label: 'نسبة الربحية %',
                                data: profitRatio,
                                type: 'line',
                                borderColor: '#f6c23e',
                                backgroundColor: '#f6c23e',
                                yAxisID: 'y1',
                                tension: 0.4,
                                pointBackgroundColor: '#f6c23e',
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            title: {
                                display: true,
                                text: `إحصائيات من ${start} إلى ${end}`
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataset.label === 'نسبة الربحية %') {
                                            return context.parsed.y + '%';
                                        }
                                        return context.dataset.label + ': ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'المبيعات والأرباح'
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'النسبة %'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            });
    }
});


</script>
@endsection