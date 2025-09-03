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
        <div class="col-md-12">
            {{-- <div class="card">
                <div class="card-header">
                    <input type="text" class="form-control datefilter" placeholder="اختر الفترة" style="width: 250px;">
                </div>
                <div class="card-body">
                    <canvas id="profitLossChart" width="200" height="200"></canvas>
                </div>
            </div> --}}
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
        @if (auth()->user()->type == 1)
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
        @endif
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

    // Flatpickr لتحديد المدة
    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: [new Date(new Date().setDate(new Date().getDate() - 6)), new Date()],
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                let start = selectedDates[0].toISOString().split('T')[0];
                let end = selectedDates[1].toISOString().split('T')[0];
                loadChart(start, end);
            }
        }
    });

    // تحميل افتراضي
    let defaultStart = new Date(new Date().setDate(new Date().getDate() - 6)).toISOString().split('T')[0];
    let defaultEnd = new Date().toISOString().split('T')[0];
    loadChart(defaultStart, defaultEnd);

    function loadChart(start, end) {
        fetch(`/dashboard/sales-chart?start=${start}&end=${end}`)
        .then(res => res.json())
        .then(data => {
            let labels = data.map(d => d.day);
            let sales = data.map(d => d.total_sales);
            let netProfit = data.map(d => d.net_profit);
            let profitRatio = data.map(d => d.profit_ratio);

            if (chart) chart.destroy();

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
                            label: 'صافي الربح',
                            data: netProfit,
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
                        title: { display: true, text: `إحصائيات من ${start} إلى ${end}` },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.label === 'نسبة الربحية %') return context.parsed.y + '%';
                                    return context.dataset.label + ': ' + context.parsed.y;
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



    // let chartInstance = null;

    // // دالة تحميل البيانات
    // function loadProfitLossChart(start = null, end = null) {
    //     $.ajax({
    //         url: "{{ route('profit.loss.chart') }}",
    //         method: "POST",
    //         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //         data: { start: start, end: end },
    //         success: function (response) {
    //             // backend يرجع { summary: {...}, pie_chart: {...} }
    //             let data = response.pie_chart ?? response; // توافقيّة إذا أرسلنا مباشرة بنية chart قديمة
    //             let summary = response.summary ?? null;

    //             // تحديث ملخص النصوص إذا كانت عناصر الـ DOM موجودة (اختياري)
    //             if (summary) {
    //                 // تأكد أنّ هذه العناصر موجودة في HTML أو أنشئها مسبقاً
    //                 if ($('#profitStatus').length) {
    //                     let statusText = summary.status ? summary.status : '';
    //                     if (summary.net_margin_percent !== null && summary.net_margin_percent !== undefined) {
    //                         statusText += ' — هامش صافي ' + summary.net_margin_percent + '%';
    //                     }
    //                     $('#profitStatus').text(statusText);
    //                 }
    //                 if ($('#revenue').length) $('#revenue').text(summary.revenue ?? 0);
    //                 if ($('#totalCosts').length) $('#totalCosts').text(summary.total_costs ?? 0);
    //                 if ($('#netProfit').length) $('#netProfit').text(summary.net_profit ?? 0);
    //                 if ($('#periodLabel').length) $('#periodLabel').text(summary.period ?? '');
    //             }

    //             let ctx = document.getElementById("profitLossChart").getContext("2d");

    //             if (chartInstance) {
    //                 chartInstance.destroy(); // نحذف القديم لو موجود
    //             }

    //             // إصلاح callback للـ tooltip ليتناسب مع Chart.js v3+
    //             chartInstance = new Chart(ctx, {
    //                 type: "doughnut",
    //                 data: data,
    //                 options: {
    //                     responsive: true,
    //                     maintainAspectRatio: false,
    //                     cutout: "65%", // تصغير الدائرة الداخلية
    //                     plugins: {
    //                         legend: { position: "bottom" },
    //                         title: {
    //                             display: true,
    //                             text: "نسبة الأرباح مقابل الخسائر"
    //                         },
    //                         tooltip: {
    //                             callbacks: {
    //                                 label: function(context) {
    //                                     // قيمة القطعة
    //                                     let value = context.parsed;
    //                                     // مجموع كل القيم في الـ dataset الحالي
    //                                     let dataset = context.dataset.data;
    //                                     let total = dataset.reduce(function(sum, val) {
    //                                         // تأكد من تحويل القيم إلى أرقام
    //                                         return sum + (Number(val) || 0);
    //                                     }, 0);
    //                                     let percent = total ? ((Number(value) / total) * 100).toFixed(2) : '0.00';
    //                                     let label = context.label || '';
    //                                     return label + ': ' + (Number(value) || 0) + ' (' + percent + '%)';
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             });
    //         },
    //         error: function(xhr){
    //             console.error("Error loading profit/loss chart:", xhr.responseText || xhr.statusText);
    //         }
    //     });
    // }

    // // أول تحميل بدون تاريخ (كل الأوقات)
    // loadProfitLossChart();

    // // تهيئة Flatpickr
    // flatpickr(".datefilter", {
    //     mode: "range",
    //     dateFormat: "Y-m-d",
    //     onChange: function(selectedDates) {
    //         if (selectedDates.length === 2) {
    //             // استخدم toISOString لنتيجة ثابتة بصيغة YYYY-MM-DD
    //             let start = selectedDates[0].toISOString().slice(0,10);
    //             let end = selectedDates[1].toISOString().slice(0,10);
    //             loadProfitLossChart(start, end);
    //         } else {
    //             loadProfitLossChart(); // رجوع للوضع الافتراضي
    //         }
    //     }
    // });

});
</script>
@endsection