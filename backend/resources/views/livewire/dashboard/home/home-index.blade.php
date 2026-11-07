<div>
    <div class="cr-overlay"></div>
    <div class="cr-dashboard-section">
        <div class="cr-layout-section">
            <livewire:dashboard.layouts.sidebar />
            <livewire:dashboard.layouts.navbar :$breadCrumb />

            <div class="cr-container-section">
                @if(in_array(auth()->user()->getRoles(0),['superadmin','admin','accountants','financial_manager']))

                    <!-- ردیف اول: کارت‌های موجودی مالی و پنل‌ها -->
                    <div class="container-fluid">
                        <div class="row">
                            <!-- کارت ۱: کیف پول آپ (تم بنفش/نیلی) -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="cr-balance cr-theme-indigo">
                                    <i class="bx bxs-wallet cr-bg-watermark"></i>
                                    <div class="cr-content">
                                        <div class="cr-icon">
                                            <i class="bx bxs-wallet"></i>
                                        </div>
                                        <div class="cr-text">
                                            <strong>موجودی کیف پول آپ</strong>
                                            <span><i class="bx bx-calendar"></i> {{$jNow}}</span>
                                        </div>
                                    </div>
                                    <div class="cr-amount cr-negative">
                                        <strong>{{number_format($asanpardakht)}}</strong>
                                        <span>تومان</span>
                                    </div>
                                </div>
                            </div>

                            <!-- کارت ۲: کیف پول کاربران (تم زمردی) -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="cr-balance cr-theme-emerald">
                                    <i class="bx bxs-wallet-alt cr-bg-watermark"></i>
                                    <div class="cr-content">
                                        <div class="cr-icon">
                                            <i class="bx bxs-wallet-alt"></i>
                                        </div>
                                        <div class="cr-text">
                                            <strong>موجودی کیف پول کاربران</strong>
                                            <span><i class="bx bx-calendar"></i> {{$jNow}}</span>
                                        </div>
                                    </div>
                                    <div class="cr-amount">
                                        <strong>{{number_format($aniroobWallet)}}</strong>
                                        <span>تومان</span>
                                    </div>
                                </div>
                            </div>

                            <!-- کارت ۳: موجودی شارژ/اینترنت (تم آبی فیروزه‌ای) -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="cr-balance cr-theme-cyan">
                                    <i class="bx bxs-mobile cr-bg-watermark"></i>
                                    <div class="cr-content">
                                        <div class="cr-icon">
                                            <i class="bx bxs-mobile"></i>
                                        </div>
                                        <div class="cr-text">
                                            <strong>
                                                <a href="{{env('INAX_URL')}}" target="_blank">
                                                    موجودی شارژ/اینترنت <i class="bx bx-link-external"></i>
                                                </a>
                                            </strong>
                                            <span><i class="bx bx-calendar"></i> {{$jNow}}</span>
                                        </div>
                                    </div>
                                    <div class="cr-amount {{$inaxBalance === false ? 'cr-error' : ''}}">
                                        <strong>{{$inaxBalance === false ? 'خطا در استعلام' : number_format($inaxBalance)}}</strong>
                                        @if($inaxBalance !== false)
                                            <span>تومان</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- کارت ۴: پنل پیامک (تم کهربایی/نارنجی) -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="cr-balance cr-theme-amber">
                                    <i class="bx bxs-message-dots cr-bg-watermark"></i>
                                    <div class="cr-content">
                                        <div class="cr-icon">
                                            <i class="bx bxs-message-dots"></i>
                                        </div>
                                        <div class="cr-text">
                                            <strong>
                                                <a href="{{env('KAVEHNEGAR_URL')}}" target="_blank">
                                                    موجودی پنل پیامک <i class="bx bx-link-external"></i>
                                                </a>
                                            </strong>
                                            <span><i class="bx bx-calendar"></i> {{$jNow}}</span>
                                        </div>
                                    </div>
                                    <div class="cr-amount {{$kavehnegarBalance === false ? 'cr-error' : ''}}">
                                        <strong>{{$kavehnegarBalance === false ? 'خطا در استعلام' : number_format($kavehnegarBalance)}}</strong>
                                        @if($kavehnegarBalance !== false)
                                            <span>تومان</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ردیف دوم: وضعیت سرور و موجودی انبارها (نوار وضعیت میانی) -->
{{--                    <div class="container-fluid">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-lg-7 col-12">--}}
{{--                                <div class="cr-card">--}}
{{--                                    <div class="cr-progress-section">--}}
{{--                                        <div class="cr-card-header">--}}
{{--                                            <div class="cr-title">--}}
{{--                                                <div>--}}
{{--                                                    <i class="bx bx-server"></i>--}}
{{--                                                    <strong>وضعیت فضای سرور (GB)</strong>--}}
{{--                                                </div>--}}
{{--                                                <div>--}}
{{--                                                    <span>مصرف شده: </span>--}}
{{--                                                    <strong>{{ $diskTotalSpace - $diskFreeSpace }}</strong> از <strong>{{ $diskTotalSpace }}</strong>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="cr-bar" style="padding-top: 15px;">--}}
{{--                                            <div class="cr-progress progress" style="height: 10px;">--}}
{{--                                                <div class="progress-bar" role="progressbar"--}}
{{--                                                     aria-valuenow="{{ (($diskTotalSpace - $diskFreeSpace) / $diskTotalSpace) * 100 }}"--}}
{{--                                                     aria-valuemin="0"--}}
{{--                                                     aria-valuemax="100"--}}
{{--                                                     style="width: {{ ($diskFreeSpace / $diskTotalSpace) * 100 }}%">--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-lg-5 col-12">--}}
{{--                                <div class="cr-card">--}}
{{--                                    <div class="cr-stock-section">--}}
{{--                                        <ul>--}}
{{--                                            <li>--}}
{{--                                                <span><i class="bx bx-package"></i> انبار آزادی (روزانه)</span>--}}
{{--                                                <strong>{{$warehouseAzadiToday ?? 0}} <small></small></strong>--}}
{{--                                            </li>--}}
{{--                                            <li>--}}
{{--                                                <span><i class="bx bx-package"></i> انبار میامی (روزانه)</span>--}}
{{--                                                <strong>{{$warehouseMayameyToday ?? 0}} <small></small></strong>--}}
{{--                                            </li>--}}
{{--                                        </ul>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <!-- ردیف سوم: نمودار آمار کاربران و لیست آمار کلی در کنار هم -->
                    <div class="container-fluid">
                        <div class="row">
                            <!-- ستون آمار کلی سیستم (سمت راست در RTL) -->
                            <div class="col-lg-5 col-12">
                                <div class="cr-card cr-analytics-card">
                                    <div class="cr-card-header">
                                        <div class="cr-title">
                                            <div class="cr-header-badge">
                                                <span class="cr-title-icon"><i class="bx bx-grid-alt"></i></span>
                                                <div>
                                                    <strong>آمار کلی سیستم</strong>
                                                    <small class="cr-subtitle">گزارش زنده عملکرد و عملیات</small>
                                                </div>
                                            </div>
                                            <span class="cr-live-indicator">
                            <i class="cr-pulse"></i> بروزرسانی آنی
                        </span>
                                        </div>
                                    </div>

                                    <div class="cr-info-section">
                                        <!-- شبکه ۲ ستونه شاخص‌های کلیدی -->
                                        <div class="cr-stats-grid">
                                            <div class="cr-stat-box">
                                                <div class="cr-stat-icon cr-indigo">
                                                    <i class="bx bx-git-pull-request"></i>
                                                </div>
                                                <div class="cr-stat-details">
                                                    <span>درخواست‌ها <small>(روزانه)</small></span>
                                                    <strong>{{$submitCount}}</strong>
                                                </div>
                                            </div>

                                            <div class="cr-stat-box">
                                                <div class="cr-stat-icon cr-emerald">
                                                    <i class="bx bx-check-double"></i>
                                                </div>
                                                <div class="cr-stat-details">
                                                    <span>انجام شده <small>(روزانه)</small></span>
                                                    <strong>{{$submitDoneCount}}</strong>
                                                </div>
                                            </div>

                                            <div class="cr-stat-box">
                                                <div class="cr-stat-icon cr-cyan">
                                                    <i class="bx bxs-truck"></i>
                                                </div>
                                                <div class="cr-stat-details">
                                                    <span>رانندگان حاضر</span>
                                                    <strong>{{$driverCount}}</strong>
                                                </div>
                                            </div>

                                            <div class="cr-stat-box">
                                                <div class="cr-stat-icon cr-violet">
                                                    <i class="bx bxs-group"></i>
                                                </div>
                                                <div class="cr-stat-details">
                                                    <span>کل کاربران</span>
                                                    <strong>{{number_format($userCount)}}</strong>
                                                </div>
                                            </div>

                                            <div class="cr-stat-box">
                                                <div class="cr-stat-icon cr-rose">
                                                    <i class="bx bx-support"></i>
                                                </div>
                                                <div class="cr-stat-details">
                                                    <span>پشتیبانی</span>
                                                    <div class="cr-stat-inline">
                                                        <strong>{{number_format($ticketCount)}}</strong>
                                                        <a href="{{route('d.contacts')}}" class="cr-ticket-badge">
                                                            تیکت <i class="bx bx-left-arrow-alt"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="cr-stat-box">
                                                <div class="cr-stat-icon cr-amber">
                                                    <i class="bx bx-credit-card"></i>
                                                </div>
                                                <div class="cr-stat-details">
                                                    <span>واریزی به کارت <small>(تومان)</small></span>
                                                    <strong>{{number_format($cardToCard)}}</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- پنل ویژه تناژ روزانه (در پایین کارت) -->
                                        <div class="cr-tonnage-banner">
                                            <div class="cr-tonnage-header">
                                                <span><i class="bx bx-layer"></i> تفکیک تناژ ثبت‌شده امروز</span>
                                                <span class="cr-tonnage-unit">واحد: کیلوگرم / تن</span>
                                            </div>
                                            <div class="cr-tonnage-metrics">
                                                <div class="cr-tonnage-item cr-total">
                                                    <span>تناژ کل</span>
                                                    <strong>{{number_format($weights)}}</strong>
                                                </div>
                                                <div class="cr-tonnage-divider"></div>
                                                <div class="cr-tonnage-item">
                                                    <span>تناژ صنفی</span>
                                                    <strong class="cr-text-emerald">{{number_format($weightsLegal)}}</strong>
                                                </div>
                                                <div class="cr-tonnage-divider"></div>
                                                <div class="cr-tonnage-item">
                                                    <span>تناژ شهروندی</span>
                                                    <strong class="cr-text-indigo">{{number_format($weightsNotLegal)}}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ستون نمودار کاربران (سمت چپ در RTL) -->
                            <div class="col-lg-7 col-12">
                                <div class="cr-card cr-chart-showcase">
                                    <!-- هدر کارت -->
                                    <div class="cr-chart-top">
                                        <div class="cr-chart-heading">
                                            <div class="cr-glow-icon">
                                                <i class="bx bx-trending-up"></i>
                                            </div>
                                            <div>
                                                <strong>روند رشد و جذب کاربران</strong>
                                                <span>تحلیل موجی تغییرات ثبت‌نام شهروندی و صنفی</span>
                                            </div>
                                        </div>
                                        <div class="cr-period-badge">
                                            <i class="bx bx-calendar-alt"></i> گزارش دوره‌ای
                                        </div>
                                    </div>

                                    <!-- نوار خلاصه مجموع آمار در بالای نمودار -->
                                    <div class="cr-chart-summary">
                                        <div class="cr-summary-item cr-citizen">
                                            <div class="cr-summary-label">
                                                <span class="cr-ring-dot"></span>
                                                <span>مجموع شهروندان</span>
                                            </div>
                                            <strong>{{ number_format($chartUserCount->sum('non_legal')) }} <small>کاربر</small></strong>
                                        </div>

                                        <div class="cr-summary-item cr-guild">
                                            <div class="cr-summary-label">
                                                <span class="cr-ring-dot"></span>
                                                <span>مجموع اصناف</span>
                                            </div>
                                            <strong>{{ number_format($chartUserCount->sum('legal')) }} <small>واحد</small></strong>
                                        </div>

                                        <div class="cr-summary-item cr-combined">
                                            <div class="cr-summary-label">
                                                <i class="bx bx-layer-plus"></i>
                                                <span>برآیند کل دوره</span>
                                            </div>
                                            <strong>{{ number_format($chartUserCount->sum('non_legal') + $chartUserCount->sum('legal')) }} <small>ثبت‌نام</small></strong>
                                        </div>
                                    </div>

                                    <!-- بوم نمودار موجی -->
                                    <div class="cr-chart-stage">
                                        <canvas id="C_Chart_Users" height="135"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <livewire:dashboard.layouts.footer/>
            </div>
        </div>
    </div>

    @if(in_array(auth()->user()->getRoles(0),['superadmin','admin']))
        @script
        <script>
            let user = [{{$chartUserCount->pluck('non_legal')->implode(',')}}];
            let guild = [{{$chartUserCount->pluck('legal')->implode(',')}}];
            let month = @json($chartUserCount->pluck('date'));
            let ctx = document.getElementById('C_Chart_Users').getContext('2d');

            let C_Chart_Users = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: month,
                    datasets: [
                        {
                            label: 'شهروند',
                            data: user,
                            backgroundColor: function(context) {
                                let ctx = context.chart.ctx;
                                let gradient = ctx.createLinearGradient(0, 0, 0, 300);
                                gradient.addColorStop(0, 'rgb(99, 102, 241)');
                                gradient.addColorStop(1, 'rgb(79, 70, 229)');
                                return gradient;
                            },
                            borderWidth: 0,
                            borderRadius: 6,
                            barPercentage: 0.65,
                        },
                        {
                            label: 'صنفی',
                            data: guild,
                            backgroundColor: function(context) {
                                let ctx = context.chart.ctx;
                                let gradient = ctx.createLinearGradient(0, 0, 0, 300);
                                gradient.addColorStop(0, 'rgb(16, 185, 129)');
                                gradient.addColorStop(1, 'rgb(5, 150, 105)');
                                return gradient;
                            },
                            borderWidth: 0,
                            borderRadius: 6,
                            barPercentage: 0.65,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            beginAtZero: true,
                            ticks: {
                                color: '#64748b',
                                font: {
                                    family: 'YekanBakh-Medium'
                                },
                            },
                        },
                        y: {
                            grid: {
                                color: 'rgba(226, 232, 240, 0.7)',
                                borderDash: [4, 4]
                            },
                            beginAtZero: true,
                            ticks: {
                                color: '#64748b',
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
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                color: '#334155',
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
    @endif
</div>
