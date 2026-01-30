<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تلاش‌های شما مسدود شد</title>
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .throttle-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .throttle-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .throttle-body {
            padding: 40px 30px;
            text-align: center;
        }

        .lock-icon {
            font-size: 64px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .throttle-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }

        .throttle-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .time-alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .time-alert .material-icons {
            color: #856404;
            font-size: 24px;
        }

        .time-text {
            font-size: 1.1rem;
            color: #856404;
            font-weight: 500;
        }

        .time-text strong {
            font-size: 1.3rem;
        }

        .footer-note {
            font-size: 0.9rem;
            color: #999;
            margin-top: 20px;
        }

        .countdown {
            font-size: 2rem;
            font-weight: 700;
            color: #dc3545;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .throttle-card {
                margin: 10px;
            }
            
            .throttle-header {
                padding: 20px;
            }
            
            .throttle-body {
                padding: 30px 20px;
            }
            
            .lock-icon {
                font-size: 48px;
            }
            
            .throttle-title {
                font-size: 1.5rem;
            }
            
            .throttle-message {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="throttle-card">
        <div class="throttle-header">
            <i class="material-icons lock-icon">lock</i>
            <h1 class="mb-0">دسترسی مسدود</h1>
        </div>
        
        <div class="throttle-body">
            <h2 class="throttle-title">تلاش‌های شما مسدود شد</h2>
            
            <p class="throttle-message">
                شما بیش از حد مجاز تلاش کردید. لطفاً پس از 
                <strong>{{ $minutes }} دقیقه</strong> 
                دوباره تلاش کنید.
            </p>
            
            <div class="countdown" id="countdown">
                {{ $minutes * 60 }}
            </div>
            
            <div class="time-alert">
                <i class="material-icons">schedule</i>
                <span class="time-text">
                    زمان باقی‌مانده: <strong>{{ $minutes }} دقیقه</strong>
                </span>
            </div>
            
            <div class="footer-note">
                <i class="material-icons" style="font-size: 16px; vertical-align: middle;">security</i>
                برای امنیت حساب شما، این محدودیت اعمال شده است.
            </div>
        </div>
    </div>

    <script>
        // Countdown timer
        let totalSeconds = {{ $minutes * 60 }};
        const countdownElement = document.getElementById('countdown');
        
        const updateCountdown = () => {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            
            countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (totalSeconds > 0) {
                totalSeconds--;
                setTimeout(updateCountdown, 1000);
            } else {
                // Auto refresh when time is up
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        };
        
        updateCountdown();
        
        // Prevent back button
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</body>
</html>
