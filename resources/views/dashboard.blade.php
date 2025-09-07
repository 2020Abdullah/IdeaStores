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
    #profitLossChart {
        max-width: 200px;
        max-height: 200px;
        margin: auto;
    }
</style>
@endsection

@section('content')
<div class="dashboard">
    <h2>إحصائيات</h2>
    <div class="row text-center">
        @if (auth()->user()->type == 1)
            <div class="col-md-12">
                <div class="card">
                    {{-- <div class="card-header">
                        <input type="text" class="form-control datefilter" placeholder="اختر الفترة" style="width: 250px;">
                    </div> --}}
                    <div class="card-body">
                        <canvas id="profitLossChart" width="200" height="200"></canvas>
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
        @endif
        @if (auth()->user()->type == 1)
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
        @endif
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
                <a href="{{ route('dues.debts') }}">
                    <div class="card-body">
                        <h4 class="mb-2">مستحقاتنا</h4>
                        <h3>{{ number_format($receivables) }}</h3>
                    </div>
                </a>
            </div>
        </div>
        @if (auth()->user()->type == 1)
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
                            <h3>{{ number_format(-$totalExpenses) }}</h3>
                        </div>
                    </a>
                </div>
            </div>
        @endif
    </div>    
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/flatpickr.js') }}"></script>
<script src="{{ asset('assets/js/chart.js') }}"></script>
@if (auth()->user()->type == 1)
<script>
    $(function() {
    // ================== Sales Chart ==================
    let ctx = document.getElementById('salesChart')?.getContext('2d');
    let chart;

    if (ctx) {
        // flatpickr لتحديد المدى
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d", // YYYY-MM-DD مناسب للداتابيز
            onClose: function(selectedDates, dateStr, instance) {
                if (!dateStr) return;

                let dates = dateStr.split(" to ");
                if (dates.length === 2) {
                    let start = dates[0];
                    let end = dates[1];

                    loadChart(start, end); // عند اختيار التاريخ
                }
            }
        });

        // عرض افتراضي عند فتح الصفحة بدون فلتر
        loadChart(); 
    }

    function loadChart(start = null, end = null) {
        // إعداد الرابط مع التأكد من وجود start و end
        let url = '/dashboard/sales-chart';
        if (start && end) {
            url += `?start=${start}&end=${end}`;
        }

        fetch(url)
        .then(res => res.json())
        .then(data => {
            if (!ctx) return;

            let labels = data.map(d => d.day);
            let costs = data.map(d => d.total_costs);
            let netProfit = data.map(d => d.net_profit);
            let profitRatio = data.map(d => d.profit_ratio);

            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'إجمالي التكاليف', data: costs, backgroundColor: '#4e73df', yAxisID: 'y' },
                        { label: 'صافي الربح', data: netProfit, backgroundColor: '#1cc88a', yAxisID: 'y' },
                        { label: 'نسبة الربحية %', data: profitRatio, type: 'line', borderColor: '#f6c23e', backgroundColor: '#f6c23e', yAxisID: 'y1', tension: 0.4, pointBackgroundColor: '#f6c23e', fill: false }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: start && end ? `إحصائيات من ${start} إلى ${end}` : 'إحصائيات آخر البيانات' },
                        tooltip: { 
                            callbacks: { 
                                label: function(context) { 
                                    return context.dataset.label === 'نسبة الربحية %' 
                                        ? context.parsed.y + '%' 
                                        : context.dataset.label + ': ' + context.parsed.y; 
                                } 
                            } 
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, position: 'left', title: { display: true, text: 'القيمة' } },
                        y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'النسبة %' }, grid: { drawOnChartArea: false } }
                    }
                }
            });
        })
        .catch(err => console.error('Error loading chart:', err));
    }

    // ================== Profit/Loss Chart ==================
    let chartInstance = null;
    let ctx2 = document.getElementById("profitLossChart")?.getContext("2d");

    if (ctx2) {
        // تحميل الرسم البياني مباشرة بدون فلتر
        loadProfitLossChart();
    }

    function loadProfitLossChart() {
        $.ajax({
            url: "{{ route('profit.loss.chart') }}",
            method: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {}, // بدون start أو end
            success: function(response) {
                if (!ctx2) return;

                let data = response.pie_chart ?? response;

                if (chartInstance) chartInstance.destroy();

                chartInstance = new Chart(ctx2, {
                    type: "doughnut",
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: "65%",
                        plugins: {
                            legend: { position: "bottom" },
                            title: { display: true, text: `مقارنة صافي الربح بإجمالي التكاليف` },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let value = context.parsed;
                                        let dataset = context.dataset.data;
                                        let total = dataset.reduce((sum, val) => sum + (Number(val) || 0), 0);
                                        let percent = total ? ((value / total) * 100).toFixed(2) : 0;
                                        return context.label + ': ' + value + ' (' + percent + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            },
            error: function(xhr){
                console.error("Error loading profit/loss chart:", xhr.responseText || xhr.statusText);
            }
        });
    }


    });
</script>
@endif
@endsection