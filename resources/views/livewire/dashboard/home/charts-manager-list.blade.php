<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div wire:loading.class="cr-parent-spinner">
            {{spinner()}}
        </div>
        <div class="row">
            <div class="col-lg-10 mx-auto mt-3 shadow p-3 mb-5 bg-white rounded"><canvas id="orderStatsChart"></canvas></div>
        </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {

                        var orderStatsChart;
                        function destroyChart() {
                            if (orderStatsChart) {
                                orderStatsChart.destroy();  // Destroy the chart if it exists
                                orderStatsChart = null; // Nullify the reference to avoid reuse
                            }
                        }
                        function renderSubmitChart(data) {
                            destroyChart();
                            var chartData = data;
                            var ctx = document.getElementById('orderStatsChart').getContext('2d');
                            var dates = chartData.map(item => item.date);
                            var delivered = chartData.map(item => item.delivered.percentage);
                            var notDelivered = chartData.map(item => item.not_delivered.percentage);
                            var Canceled = chartData.map(item => item.cancel.percentage);
                            var deliveredCounts = chartData.map(item => item.delivered.count);
                            var notDeliveredCounts = chartData.map(item => item.not_delivered.count);
                            var CanceledCounts = chartData.map(item => item.cancel.count);

                            orderStatsChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: dates,
                                    datasets: [{
                                        barThickness: 40,
                                        label: 'انجام شده (%)',
                                        data: delivered,
                                        backgroundColor: 'rgba(41,119,34,0.7)',
                                        borderColor: 'rgba(111,238,93,0.7)',
                                        borderWidth: 1
                                    }, {
                                        barThickness: 40,
                                        label: 'کنسل شده (%)',
                                        data: Canceled,
                                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    }, {
                                        barThickness: 40,
                                        label: 'انجام نشده (%)',
                                        data: notDelivered,
                                        backgroundColor: 'rgba(250,244,244,0.7)',
                                        borderColor: 'rgb(243,227,230)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        x: { stacked: true,
                                            title: {
                                                display: true,
                                                text: 'تاریخ'
                                            },
                                        ticks: {
                                            font: {
                                                family: 'YekanBakh-Medium'
                                            }
                                        }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            stacked: true,
                                            max: 100,
                                            title: {
                                                display: true,
                                                text: 'درصد'
                                            },
                                            ticks: {
                                                font: {
                                                    family: 'YekanBakh-Medium'
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function (tooltipItem) {
                                                    var datasetIndex = tooltipItem.datasetIndex;
                                                    var index = tooltipItem.dataIndex;
                                                    if (datasetIndex === 0) {
                                                        return 'تعدادانجام داده شده: ' + deliveredCounts[index];
                                                    } else if (datasetIndex === 1) {
                                                        return 'تعداد کنسل شده: ' + CanceledCounts[index];
                                                    } else {
                                                        return 'تعداد انجام نشده: ' + notDeliveredCounts[index];
                                                    }
                                                }
                                            }
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
                        }
                        function renderRegionChart(data) {
                            destroyChart();
                            var chartData = data;
                            var ctx = document.getElementById('orderStatsChart').getContext('2d');
                            var regions = chartData.map(item => item.region);
                            var delivered = chartData.map(item => item.delivered.percentage);
                            var notDelivered = chartData.map(item => item.not_delivered.percentage);
                            var Canceled = chartData.map(item => item.cancel.percentage);
                            var deliveredCounts = chartData.map(item => item.delivered.count);
                            var notDeliveredCounts = chartData.map(item => item.not_delivered.count);
                            var CanceledCounts = chartData.map(item => item.cancel.count);
                            var driverAcceptCount = chartData.map(item => item.driver_accept.count);
                            var driverAccept = chartData.map(item => item.driver_accept.percentage);
                            console.log(regions)


                            orderStatsChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: regions,
                                    datasets: [{
                                        label: 'تحویل شده (%)',
                                        data: delivered,
                                        backgroundColor: 'rgba(41,119,34,0.7)',
                                        borderColor: 'rgba(111,238,93,0.7)',
                                        borderWidth: 1
                                    }, {
                                        label: 'کنسل شده (%)',
                                        data: Canceled,
                                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    },  {
                                        label: 'جاری (%)',
                                        data: driverAccept,
                                        backgroundColor: 'rgba(80,42,8,0.7)',
                                        borderColor: 'rgba(164,105,60,0.7)',
                                        borderWidth: 1
                                    },{
                                        label: 'تحویل داده نشده (%)',
                                        data: notDelivered,
                                        backgroundColor: 'rgba(26,143,255,0.7)',
                                        borderColor: 'rgba(152,199,245,0.7)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        x: { grid: {
                                                display: false
                                            },stacked: true ,
                                            title: {
                                                display: true,
                                                text: 'تاریخ'
                                            },
                                            ticks: {
                                                font: {
                                                    family: 'YekanBakh-Medium'
                                                }
                                            }},
                                        y: {
                                            beginAtZero: true,
                                            stacked: true,
                                            max: 100,
                                            title: {
                                                display: true,
                                                text: 'درصد'
                                            },
                                            ticks: {
                                                font: {
                                                    family: 'YekanBakh-Medium'
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function (tooltipItem) {
                                                    var datasetIndex = tooltipItem.datasetIndex;
                                                    var index = tooltipItem.dataIndex;
                                                    if (datasetIndex === 0) {
                                                        return 'تعداد تحویل داده شده: ' + deliveredCounts[index];
                                                    } else if (datasetIndex === 1) {
                                                        return 'تعداد کنسل شده: ' + CanceledCounts[index];
                                                    }  else if (datasetIndex === 2) {
                                                        return 'تعداد جاری: ' + driverAcceptCount[index];
                                                    }else {
                                                        return 'تعداد تحویل داده نشده: ' + notDeliveredCounts[index];
                                                    }
                                                }
                                            }
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
                        }
                        function renderCancelChart(data) {
                            if (!Array.isArray(data)) {
                                console.error("داده‌ها آرایه نیستند:", data);
                                return;
                            }
                            destroyChart();
                            console.log(data)
                            var chartData = data;
                            var ctx = document.getElementById('orderStatsChart').getContext('2d');
                            var labels = data.map(item => item.reason);
                            var counts = data.map(item => item.count);

                            orderStatsChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels:labels,
                                    datasets: [ {
                                        label: 'کنسل شده ',
                                        data: counts,
                                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    },]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        x: { grid: {
                                                display: false
                                            },
                                            title: {
                                                display: true,
                                                text: 'دلایل کنسلی'
                                            },
                                            ticks: {
                                                autoSkip: false, // برای نمایش همه لیبل‌ها
                                                maxRotation: 0,  // چرخش افقی
                                                minRotation: 0,
                                                font: {
                                                    size: 12  // تنظیم اندازه فونت
                                                    , family: 'YekanBakh-Medium'
                                                }
                                            }},
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'تعداد'
                                            },
                                            ticks: {
                                                font: {
                                                    family: 'YekanBakh-Medium'
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function (tooltipItem) {
                                                      return 'تعداد: ' + tooltipItem.raw;
                                                   }
                                            }
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
                        }
                        function renderTonajChart(data) {
                            destroyChart();
                            var chartData = data
                            var dates = chartData.map(item => item.date);
                            var legal = chartData.map(item => item.legal);
                            var illegal = chartData.map(item => item.illegal)
                            // var legalWeights = [];
                            // var nonLegalWeights = [];
                            // weightsData.forEach(function (item) {
                            //     dates.push(item.date); // تاریخ
                            //     if (item.legal == 1) {
                            //         legalWeights.push(item.weight); // وزن کاربران قانونی
                            //         nonLegalWeights.push(0); // برای کاربران غیرقانونی 0
                            //     } else {
                            //         nonLegalWeights.push(item.weight); // وزن کاربران غیرقانونی
                            //         legalWeights.push(0); // برای کاربران قانونی 0
                            //     }
                            // });
                            // ایجاد نمودار
                            var ctx = document.getElementById('orderStatsChart').getContext('2d');

                            orderStatsChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: dates, // تاریخ‌ها
                                    datasets: [{
                                        label: 'وزن کاربران صنفی',
                                        data: legal,
                                        borderColor: 'rgba(41,119,34,1)',
                                        backgroundColor: 'rgba(41,119,34,0.2)',
                                        fill: false,
                                        tension: 0.2
                                    }, {
                                        label: 'وزن کاربران ',
                                        data: illegal,
                                        borderColor: 'rgb(224,12,56)',
                                        backgroundColor: 'rgba(255,99,132,0.2)',
                                        fill: false,
                                        tension: 0.2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function (tooltipItem) {
                                                    return tooltipItem.dataset.label + ': ' + tooltipItem.raw + ' کیلوگرم';
                                                }
                                            }
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
                                    },
                                    scales: {
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'تاریخ'
                                            },
                                            ticks: {
                                                font: {
                                                    family: 'YekanBakh-Medium'
                                                }
                                            }
                                        },
                                        y: {
                                            title: {
                                                display: true,
                                                text: 'وزن (کیلوگرم)'
                                            },
                                            ticks: {
                                                font: {
                                                    family: 'YekanBakh-Medium'
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                        if ({{$this->type}} == 0 || {{$this->type}} == 1)
                        {
                            renderSubmitChart(@json($this->data));
                        }
                        else if ({{$this->type}} == 2) {
                            renderTonajChart(@json($this->data))
                        }
                        else if ({{$this->type}} == 3) {
                            renderRegionChart(@json($this->data))
                        }
                        else {
                            renderCancelChart(@json($this->data))
                        }

                        Livewire.on('chartChanged', function (data) {

                           chartData = JSON.parse(data)
                            if (  chartData.type == 0 || chartData.type == 1){
                                renderSubmitChart(chartData.data);
                            }else if (chartData.type == 2) {
                                renderTonajChart(chartData.data)
                            }
                            else if (chartData.type == 3) {
                                renderRegionChart(chartData.data)
                            }
                            else {
                                renderCancelChart(chartData.data)
                            }
                            orderStatsChart.resize()

                        });
                        document.getElementById('downloadChart').addEventListener('click', function () {
                            // تبدیل نمودار به تصویر Base64
                            var image = orderStatsChart.toBase64Image();

                            // ایجاد یک لینک موقت برای دانلود
                            var link = document.createElement('a');
                            link.href = image;
                            link.download = 'chart.png';
                            link.click();
                        });
                    });
                </script>
    </div>
    <div class="cr-card-footer">
    </div>
</div>
