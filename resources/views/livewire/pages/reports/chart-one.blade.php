
<div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-gradient-primary text-white rounded-top-4">
                        <h4 class="mb-0 text-center">
                            <i class="fas fa-chart-pie me-2"></i>
                            آمار و تحلیل ساکنین
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- فیلترها -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info rounded-3" role="alert" style="font-size: 30px">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>تعداد کل ساکنین:</strong> {{ number_format($totalResidents) }} نفر
                                </div>
                            </div>
                        </div>

{{--                        <div class="row mb-4">--}}
{{--                            <div class="col-md-4 mb-3">--}}
{{--                                <label for="ageFilter" class="form-label fw-bold">--}}
{{--                                    <i class="fas fa-calendar-alt text-primary me-1"></i>--}}
{{--                                    رنج سنی:--}}
{{--                                </label>--}}
{{--                                <select wire:model.live="ageFilter" id="ageFilter" class="form-select form-select-lg rounded-3 shadow-sm">--}}
{{--                                    @foreach($ageRanges as $key => $value)--}}
{{--                                        <option value="{{ $key }}">{{ $value }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}

{{--                            <div class="col-md-4 mb-3">--}}
{{--                                <label for="referralFilter" class="form-label fw-bold">--}}
{{--                                    <i class="fas fa-users text-success me-1"></i>--}}
{{--                                    نحوه آشنایی:--}}
{{--                                </label>--}}
{{--                                <select wire:model.live="referralFilter" id="referralFilter" class="form-select form-select-lg rounded-3 shadow-sm">--}}
{{--                                    @foreach($referralSources as $key => $value)--}}
{{--                                        <option value="{{ $key }}">{{ $value }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}

{{--                            <div class="col-md-4 mb-3">--}}
{{--                                <label for="jobFilter" class="form-label fw-bold">--}}
{{--                                    <i class="fas fa-briefcase text-warning me-1"></i>--}}
{{--                                    شغل:--}}
{{--                                </label>--}}
{{--                                <select wire:model.live="jobFilter" id="jobFilter" class="form-select form-select-lg rounded-3 shadow-sm">--}}
{{--                                    @foreach($jobs as $key => $value)--}}
{{--                                        <option value="{{ $key }}">{{ $value }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <!-- چارت‌ها -->
                        <div class="row">
                            <!-- چارت رنج سنی -->
                            <div class="col-lg-4 mb-4">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-header bg-light border-0 rounded-top-3">
                                        <h5 class="card-title mb-0 text-center text-primary">
                                            <i class="fas fa-birthday-cake me-2"></i>
                                            آمار سنی
                                        </h5>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <div style="position: relative; height: 300px; width: 100%;">
                                            <canvas id="ageChart" wire:key="age-chart-{{ $ageFilter }}"></canvas>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light border-0 rounded-bottom-3">
                                        <div class="row text-center">

                                            <div class="col-12">
                                                <div class="d-flex justify-content-center align-items-center" style="font-size: 20px">
                                                    <span class="badge bg-primary ms-2">
                                                        <i class="fas fa-users me-1"></i>
                                                        {{ number_format($ageStats['filled']) }} نفر
                                                    </span>
                                                    <small class="text-success fw-bold ">
                                                        {{ $ageStats['percentage'] }}% از کل ساکنین
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- چارت نحوه آشنایی -->
                            <div class="col-lg-4 mb-4">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-header bg-light border-0 rounded-top-3">
                                        <h5 class="card-title mb-0 text-center text-success">
                                            <i class="fas fa-handshake me-2"></i>
                                          آمار نحوه آشنایی
                                        </h5>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <div style="position: relative; height: 300px; width: 100%;">
                                            <canvas id="referralChart" wire:key="referral-chart-{{ $referralFilter }}"></canvas>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light border-0 rounded-bottom-3">
                                        <div class="row text-center">

                                            <div class="col-12">
                                                <div class="d-flex justify-content-center align-items-center"  style="font-size: 20px">
                                                    <span class="badge bg-success ms-2">
                                                        <i class="fas fa-handshake me-1"></i>
                                                        {{ number_format($referralStats['filled']) }} نفر
                                                    </span>
                                                    <small class="text-success fw-bold">
                                                        {{ $referralStats['percentage'] }}% از کل ساکنین
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- چارت شغل -->
                            <div class="col-lg-4 mb-4">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-header bg-light border-0 rounded-top-3">
                                        <h5 class="card-title mb-0 text-center text-warning">
                                            <i class="fas fa-user-tie me-2"></i>
                                            آمار شغلی
                                        </h5>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <div style="position: relative; height: 300px; width: 100%;">
                                            <canvas id="jobChart" wire:key="job-chart-{{ $jobFilter }}"></canvas>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light border-0 rounded-bottom-3">
                                        <div class="row text-center">

                                            <div class="col-12">
                                                <div class="d-flex justify-content-center align-items-center"  style="font-size: 20px">
                                                    <span class="badge bg-info ms-2">
                                                        <i class="fas fa-briefcase me-1"></i>
                                                        {{ number_format($jobStats['filled']) }} نفر
                                                    </span>
                                                    <small class="text-success fw-bold">
                                                        {{ $jobStats['percentage'] }}% از کل ساکنین
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- اسکریپت چارت -->
    <script>
        let ageChart, referralChart, jobChart;
        let chartsInitialized = false;

        // تابع برای destroy کردن چارت‌ها
        function destroyCharts() {
            if (ageChart) {
                ageChart.destroy();
                ageChart = null;
            }
            if (referralChart) {
                referralChart.destroy();
                referralChart = null;
            }
            if (jobChart) {
                jobChart.destroy();
                jobChart = null;
            }
        }

        function initCharts() {
            // اول چارت‌های قبلی را destroy می‌کنیم
            destroyCharts();

            // تنظیمات رنگ‌های زیبا
            const colors = {
                age: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40'],
                referral: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40', '#9966FF', '#FF99CC', '#87CEEB', '#98FB98', '#DDA0DD'],
                job: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40', '#9966FF', '#FF99CC']
            };

            // بخش مربوط به چارت سن در جاوا اسکریپت را اینطور آپدیت کنید:

// چارت سن
            const ageData = @json($ageData);
            const ageCtx = document.getElementById('ageChart');

            if (ageCtx && ageData.data && ageData.data.length > 0) {
                ageChart = new Chart(ageCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ageData.labels.map(label => {
                            switch(label) {
                                case '16-19': return '16-19 سال';
                                case '20-23': return '20-23 سال';
                                case '24-27': return '24-27 سال';
                                case '28-31': return '28-31 سال';
                                case '32-35': return '32-35 سال';
                                case '36-39': return '36-39 سال';
                                case '40-43': return '40-43 سال';
                                case '44-47': return '44-47 سال';
                                case '48+': return 'بالای 48 سال';
                                default: return label;
                            }
                        }),
                        datasets: [{
                            data: ageData.data,
                            backgroundColor: colors.age,
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverBorderWidth: 5,
                            hoverBorderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12,
                                        family: 'IRANSans, Arial'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed * 100) / total).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' نفر (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            duration: 1000
                        }
                    }
                });
            }

            // چارت نحوه آشنایی
            const referralData = @json($referralData);
            const referralCtx = document.getElementById('referralChart');

            if (referralCtx && referralData.data && referralData.data.length > 0) {
                referralChart = new Chart(referralCtx, {
                    type: 'doughnut',
                    data: {
                        labels: referralData.labels,
                        datasets: [{
                            data: referralData.data,
                            backgroundColor: colors.referral,
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverBorderWidth: 5,
                            hoverBorderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12,
                                        family: 'IRANSans, Arial'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed * 100) / total).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' نفر (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            duration: 1000
                        }
                    }
                });
            }

            // چارت شغل
            const jobData = @json($jobData);
            const jobCtx = document.getElementById('jobChart');

            if (jobCtx && jobData.data && jobData.data.length > 0) {
                jobChart = new Chart(jobCtx, {
                    type: 'doughnut',
                    data: {
                        labels: jobData.labels,
                        datasets: [{
                            data: jobData.data,
                            backgroundColor: colors.job,
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverBorderWidth: 5,
                            hoverBorderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12,
                                        family: 'IRANSans, Arial'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed * 100) / total).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' نفر (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            duration: 1000
                        }
                    }
                });
            }

            chartsInitialized = true;
        }

        // اجرای اولیه چارت‌ها
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initCharts, 100);
        });

        // اجرا برای livewire navigation
        document.addEventListener('livewire:navigated', function() {
            setTimeout(initCharts, 100);
        });

        // به‌روزرسانی چارت‌ها هنگام تغییر فیلتر
        document.addEventListener('livewire:updated', function() {
            setTimeout(initCharts, 200);
        });

        // پاک کردن چارت‌ها قبل از navigate
        document.addEventListener('livewire:navigate', function() {
            destroyCharts();
        });
    </script>
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .card {
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .text-primary {
            color: #667eea !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        /* فونت فارسی */
        body, .form-select, .card-title, .form-label {
            font-family: 'IRANSans', Arial, sans-serif;
        }

        /* انیمیشن لودینگ */
        .card-body {
            position: relative;
        }

        .card-body::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 1;
            opacity: 0;
            pointer-events: none;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .loading .card-body::before {
            opacity: 1;
        }
    </style>
</div>
