<div>
    <div class="cr-overlay"></div>
    <div class="cr-dashboard-section">
        <div class="cr-layout-section">
            <livewire:dashboard.layouts.sidebar />
            <livewire:dashboard.layouts.navbar :$breadCrumb />
            <div class="cr-container-section">
                @if(in_array(auth()->user()->getRoles(0),['superadmin','admin','accountants','financial_manager']))
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="cr-balance">
                                <div class="cr-content">
                                    <div class="cr-icon">
                                        <i class="bx bxs-wallet"></i>
                                    </div>
                                    <div class="cr-text">
                                        <strong>موجودی کیف پول آپ</strong>
                                        <span>{{$jNow}}</span>
                                    </div>
                                </div>
                                <div class="cr-amount cr-negative" style="height: 42px">
                                    <strong>{{number_format($asanpardakht)}}</strong>
                                    <span>تومان</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="cr-balance">
                                <div class="cr-content">
                                    <div class="cr-icon">
                                        <i class="bx bxs-wallet"></i>
                                    </div>
                                    <div class="cr-text">
                                        <strong>موجودی کیف پول کاربران</strong>
                                        <span>{{$jNow}}</span>
                                    </div>
                                </div>
                                <div class="cr-amount" style="height: 42px">
                                    <strong>{{number_format($bazistWallet)}}</strong>
                                    <span>تومان</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="cr-balance">
                                <div class="cr-content">
                                    <div class="cr-icon">
                                        <i class="bx bxs-mobile"></i>
                                    </div>
                                    <div class="cr-text">
                                        <strong><a href="{{env('INAX_URL')}}" target="_blank">موجودی شارژ/اینترنت</a></strong>
                                        <span>{{$jNow}}</span>
                                    </div>
                                </div>
                                <div class="cr-amount" style="height: 42px">
                                    <strong>{{$inaxBalance === false ? 'خطا در استعلام' : number_format($inaxBalance)}}</strong>
                                    <span>تومان</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="cr-balance">
                                <div class="cr-content">
                                    <div class="cr-icon">
                                        <i class="bx bxs-mobile"></i>
                                    </div>
                                    <div class="cr-text">
                                        <strong><a href="{{env('KAVEHNEGAR_URL')}}" target="_blank">موجودی پنل پیامک</a></strong>
                                        <span>{{$jNow}}</span>
                                    </div>
                                </div>
                                <div class="cr-amount" style="height: 42px">
                                    <strong>{{$kavehnegarBalance === false ? 'خطا در استعلام' : number_format($kavehnegarBalance)}}</strong>
                                    <span>تومان</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-7 col-12">
                            <div class="cr-card">
                                <div class="cr-card-header">
                                    <div class="cr-title">
                                        <div>
                                            <i class="bx bxs-group"></i>
                                            <strong>آمار کاربران</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="cr-card-body">
                                    <canvas id="C_Chart_Users"></canvas>
                                </div>
                            </div>
                            <div class="cr-card">
                                <div class="cr-progress-section">
                                    <div class="cr-card-header">
                                        <div class="cr-title">
                                            <div>
                                                <i class="bx bx-message"></i>
                                                <strong>آخرین نظرات کاربران</strong>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="cr-card-body " id="paginated-list">
                                    <div class="table-responsive text-center text-nowrap">
                                        <div wire:loading>
                                            {{spinner()}}
                                        </div>
                                        <table class="cr-table table">
                                            <thead>
                                            <tr>
                                                <th>ارسال توسط</th>
                                                <th>شماره موبایل</th>
                                                <th>متن پیام</th>
                                                <th>تاریخ ارسال</th>
                                            </tr>
                                            </thead>
                                            @if($this->comments->count())
                                                <tbody>
                                                @foreach($this->comments as $notification)
                                                    <tr>
                                                        <td>{{$notification->user->name .' '.$notification->user->lastname}}</td>
                                                        <td>{{$notification->user->mobile}}</td>
                                                        <td><div class="cr-message">{{substr($notification->comment,0,55)}}</div></td>
                                                        <td>{{\Verta::instance($notification->created_at)->format('Y/m/d H:i')}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                            <div class="cr-actions mx-1">
                                                <a href="{{route('d.submit-surveys')}}" class="cr-action cr-primary">
                                                    <span>مشاهده همه</span>
                                                </a>
                                            </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-12">
                            <div class="row">
                                <div class="col-lg-7 col-12">
                                    <div class="cr-card">
                                        <div class="cr-progress-section">
                                            <div class="cr-card-header">
                                                <div class="cr-title">
                                                    <div>
                                                        <i class="bx bx-server"></i>
                                                        <strong>حجم سرور (GB)</strong>
                                                    </div>
                                                    <div>({{ $diskTotalSpace }} / {{ $diskTotalSpace - $diskFreeSpace }})</div>
                                                </div>
                                            </div>
                                            <div class="cr-bar">
                                                <div class="cr-progress progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: {{($diskFreeSpace/$diskTotalSpace)*100}}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-12">
                                    <div class="cr-card">
                                        <div class="cr-stock-section">
                                            <ul>
                                                <li>
                                                    <span>انبار آزادی (روزانه)</span>
                                                    <strong>{{$warehouseAzadiToday ?? 0}} <small></small></strong>
                                                </li>
                                                <li>
                                                    <span>انبار میامی (روزانه)</span>
                                                    <strong>{{$warehouseMayameyToday ?? 0}} <small></small></strong>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cr-card">
                                <div class="cr-card-header">
                                    <div class="cr-title">
                                        <div>
                                            <i class="bx bx-pie-chart"></i>
                                            <strong>آمار کلی</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="cr-info-section">
                                    <ul>
                                        <li>
                                            <span>درخواست ها <small>(روزانه)</small></span>
                                            <strong>{{$submitCount}}</strong>
                                        </li>
                                        <li>
                                            <span>درخواست های انجام شده <small>(روزانه)</small></span>
                                            <strong>{{$submitDoneCount}}</strong>
                                        </li>
                                        <li>
                                            <span>رانندگان حاضر</span>
                                            <strong>{{$driverCount}}</strong>
                                        </li>
                                        <li>
                                            <span>کل کاربران</span>
                                            <strong>{{number_format($userCount)}}</strong>
                                        </li>
                                        <li>
                                            <span>پشتیبانی</span>
                                            <strong>{{number_format($ticketCount)}} <a href="{{route('d.contacts')}}">تیکت</a></strong>
                                        </li>
                                        <li>
                                            <span>واریزی به کارت (تومان)</span>
                                            <strong>{{number_format($cardToCard)}}</strong>
                                        </li>
                                        <li>
                                            <span>تناژ (امروز)</span>
                                            <strong>{{number_format($weights)}}</strong>
                                        </li>
                                        <li>
                                            <span>تناژ صنفی (امروز)</span>
                                            <strong>{{number_format($weightsLegal)}}</strong>
                                        </li>
                                        <li>
                                            <span>تناژ شهروندی(امروز)</span>
                                            <strong>{{number_format($weightsNotLegal)}}</strong>
                                        </li>
                                    </ul>
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
                datasets: [{
                    label: 'شهروند',
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
                },
                    {
                        label: 'صنفی',
                        data: guild,
                        backgroundColor: function(context) {
                            let ctx = context.chart.ctx;
                            let index = context.dataIndex;
                            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
                            gradient.addColorStop(0, 'rgb(210, 60, 105)');
                            gradient.addColorStop(1, 'rgb(180, 45, 85)');
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
    @endif
</div>
