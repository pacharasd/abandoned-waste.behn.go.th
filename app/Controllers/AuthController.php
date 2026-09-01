<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\ActivityLogger;
use App\Core\RateLimiter;
use App\Models\User;

class AuthController {
    public function showLogin(): void {
        if (Auth::check()) {
            Response::redirect('/admin/dashboard');
        }

        View::render('auth.login', [
            'title' => 'เข้าสู่ระบบผู้ดูแลระบบ | ระบบแจ้งจัดเก็บขยะไร้บ้าน'
        ]);
    }

    public function login(): void {
        $email = trim(Request::input('email', ''));
        $password = Request::input('password', '');
        $ip = Request::ip();
        $rateLimitKey = 'login:' . md5($email . '|' . $ip);

        // Check Brute-Force lockout (Max 5 attempts in 15 minutes)
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5, 900)) {
            $remaining = RateLimiter::availableIn($rateLimitKey);
            $minutes = max(1, (int)ceil($remaining / 60));
            ActivityLogger::log('login_blocked', "บล็อกการพยายามล็อกอินซ้ำเกินกำหนดสำหรับอีเมล '{$email}' จาก IP {$ip}", null);
            Response::redirect('/login', "คุณระบุรหัสผ่านผิดเกิน 5 ครั้ง เพื่อความปลอดภัยกรุณารออีก {$minutes} นาทีก่อนลองใหม่", 'danger');
        }

        if (empty($email) || empty($password)) {
            Response::redirect('/login', 'กรุณากรอกอีเมลและรหัสผ่าน', 'danger');
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            RateLimiter::hit($rateLimitKey, 900);
            ActivityLogger::log('login_failed', "ล็อกอินไม่สำเร็จสำหรับอีเมล '{$email}' จาก IP {$ip}", $user ? (int)$user['id'] : null);
            Response::redirect('/login', 'อีเมลหรือรหัสผ่านไม่ถูกต้อง', 'danger');
        }

        // Clear rate limiter upon success
        RateLimiter::clear($rateLimitKey);

        Auth::login($user);
        ActivityLogger::log('login', "ผู้ดูแลระบบ {$user['name']} เข้าสู่ระบบสำเร็จ (IP: {$ip})", (int)$user['id']);

        Response::redirect('/admin/dashboard', 'ยินดีต้อนรับเข้าสู่ระบบจัดการผู้ดูแลระบบ', 'success');
    }

    public function logout(): void {
        if (Auth::check()) {
            $user = Auth::user();
            ActivityLogger::log('logout', "ผู้ใช้งาน {$user['name']} ออกจากระบบ", (int)$user['id']);
            Auth::logout();
        }
        Response::redirect('/login', 'ออกจากระบบเรียบร้อยแล้ว', 'info');
    }
}
