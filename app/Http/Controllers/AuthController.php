<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // نمایش فرم لاگین
    public function showLoginForm(Request $request)
    {
        // بررسی IP block
        $ip = $request->ip();
        if ($this->isIpBlocked($ip)) {
            return redirect()->back()
                ->with('error', 'دسترسی شما به دلیل تلاش‌های ناموفق متعدد موقتاً مسدود شده است. لطفاً بعداً تلاش کنید.');
        }

        return view('auth.main.login');
    }

    // پردازش لاگین با امنیت بالا
    public function login(Request $request)
    {
        $ip = $request->ip();
        $email = $request->input('email');

        // Rate limiting - حداکثر 5 تلاش در 15 دقیقه
        $key = 'login.attempts.' . $ip;
        $maxAttempts = 5;
        $decayMinutes = 15;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            
            // Block IP برای 30 دقیقه
            $this->blockIp($ip, 30);
            
            return back()->withErrors([
                'email' => "تعداد تلاش‌های ناموفق بیش از حد مجاز است. لطفاً {$minutes} دقیقه دیگر تلاش کنید.",
            ])->onlyInput('email');
        }

        // بررسی IP block
        if ($this->isIpBlocked($ip)) {
            return back()->withErrors([
                'email' => 'دسترسی شما موقتاً مسدود شده است. لطفاً بعداً تلاش کنید.',
            ])->onlyInput('email');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255',
        ], [
            'email.required' => 'ایمیل الزامی است.',
            'email.email' => 'فرمت ایمیل صحیح نیست.',
            'password.required' => 'رمز عبور الزامی است.',
            'password.min' => 'رمز عبور باید حداقل 6 کاراکتر باشد.',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key, $decayMinutes * 60);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // بررسی وجود کاربر قبل از تلاش برای ورود
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            RateLimiter::hit($key, $decayMinutes * 60);
            $this->logFailedAttempt($ip, $email);
            
            // تاخیر تصادفی برای جلوگیری از brute force
            usleep(rand(500000, 1500000)); // 0.5 تا 1.5 ثانیه
            
            return back()->withErrors([
                'email' => 'اطلاعات وارد شده صحیح نمی‌باشد.',
            ])->onlyInput('email');
        }

        // تلاش برای ورود
        if (Auth::attempt($credentials, $remember)) {
            // پاک کردن rate limiter در صورت موفقیت
            RateLimiter::clear($key);
            $this->clearFailedAttempts($ip);
            
            // Regenerate session برای جلوگیری از session fixation
            $request->session()->regenerate();
            
            // Log successful login
            $this->logSuccessfulLogin($ip, $user);
            
            return redirect()->intended('/');
        }

        // در صورت ناموفق بودن
        RateLimiter::hit($key, $decayMinutes * 60);
        $this->logFailedAttempt($ip, $email);
        
        // تاخیر تصادفی
        usleep(rand(500000, 1500000));
        
        // اگر تعداد تلاش‌ها زیاد شد، IP را block کن
        $attempts = RateLimiter::attempts($key);
        if ($attempts >= $maxAttempts) {
            $this->blockIp($ip, 30);
        }

        return back()->withErrors([
            'email' => 'اطلاعات وارد شده صحیح نمی‌باشد.',
        ])->onlyInput('email');
    }

    // بررسی IP block
    private function isIpBlocked($ip)
    {
        $blockKey = 'ip.blocked.' . $ip;
        return Cache::has($blockKey);
    }

    // Block کردن IP
    private function blockIp($ip, $minutes = 30)
    {
        $blockKey = 'ip.blocked.' . $ip;
        Cache::put($blockKey, true, now()->addMinutes($minutes));
    }

    // Log کردن تلاش ناموفق
    private function logFailedAttempt($ip, $email)
    {
        $logKey = 'login.failed.' . $ip;
        $attempts = Cache::get($logKey, []);
        $attempts[] = [
            'email' => $email,
            'ip' => $ip,
            'time' => now()->toDateTimeString(),
        ];
        Cache::put($logKey, array_slice($attempts, -10), now()->addHours(24)); // نگه داشتن 10 تلاش آخر
    }

    // Log کردن ورود موفق
    private function logSuccessfulLogin($ip, $user)
    {
        $logKey = 'login.success.' . $user->id;
        Cache::put($logKey, [
            'ip' => $ip,
            'time' => now()->toDateTimeString(),
        ], now()->addHours(24));
    }

    // پاک کردن تلاش‌های ناموفق
    private function clearFailedAttempts($ip)
    {
        Cache::forget('login.failed.' . $ip);
    }

    // نمایش فرم ثبت‌نام


    // پردازش ثبت‌نام

    // خروج کاربر
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('home');
    }
}
