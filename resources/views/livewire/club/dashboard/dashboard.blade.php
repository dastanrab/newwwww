<div>
    <div class="cr-overlay"></div>
    <div class="cr-dashboard-section">
        <div class="cr-layout-section">
            <livewire:club.layouts.sidebar />
            <livewire:club.layouts.navbar :$breadCrumb />
            <div class="cr-container-section">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="cr-balance">
                                <div class="cr-content">
                                    <div class="cr-icon">
                                        <i class="bx bx-box"></i>
                                    </div>
                                    <div class="cr-text">
                                        <strong>کل آیتم ها</strong>
                                        <span>{{$jNow}}</span>
                                    </div>
                                </div>
                                <div class="cr-amount cr-negative">
                                    <strong>{{number_format($itemCount)}}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="cr-balance">
                                <div class="cr-content">
                                    <div class="cr-icon">
                                        <i class="bx bxs-offer"></i>
                                    </div>
                                    <div class="cr-text">
                                        <strong>کل تخفیف ها</strong>
                                        <span>{{$jNow}}</span>
                                    </div>
                                </div>
                                <div class="cr-amount">
                                    <strong>{{number_format($offerCount)}}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="cr-balance">
                                <div class="cr-content">
                                    <div class="cr-icon">
                                        <i class="bx bxs-wallet"></i>
                                    </div>
                                    <div class="cr-text">
                                        <strong>موجودی کیف پول</strong>
                                        <span>{{$jNow}}</span>
                                    </div>
                                </div>
                                <div class="cr-amount">
                                    <strong>{{number_format($wallet)}}</strong>
                                    <span>تومان</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <div class="cr-card">
                                <div class="cr-card-header">
                                    <div class="cr-title">
                                        <div>
                                            <i class="bx bxs-group"></i>
                                            <strong>آمار تخفیف ها</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="cr-card-body">
                                    <canvas id="C_Chart_Users"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.layouts.footer/>
            </div>
        </div>
    </div>
    @script
    <script>
        let user = [];
        let guild = [];
        {{--let month = @json($chartUserCount->pluck('date'));--}}
        let month = [];
        let ctx = document.getElementById('C_Chart_Users').getContext('2d');
        let C_Chart_Users = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: month,
                datasets: [{
                    label: 'تخفیف ها',
                    data: user,
                    backgroundColor: function(context) {
                        let ctx = context.chart.ctx;
                        let index = context.dataIndex;
                        let gradient = ctx.createLinearGradient(0, 0, 0, 90);
                        gradient.addColorStop(0, 'rgb(90, 220, 115)');
                        gradient.addColorStop(1, 'rgb(45, 180, 75)');
                        return gradient;
                    },
                    borderWidth: 0,
                    borderRadius: 2,
                }
                ]
            },
            options: {
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        beginAtZero: true,
                        ticks: {
                            font: {
                                family: 'YekanBakh-Medium'
                            },
                        },

                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, .05)',
                        },
                        beginAtZero: true,
                        ticks: {
                            font: {
                                family: 'YekanBakh-Medium'
                            }
                        },
                    },
                },
                plugins: {
                    title: {
                        display: false,
                    },
                    legend: {
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: 'YekanBakh-Medium'
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endscript
</div>
