<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\ActivityLogger;
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

        if (empty($email) || empty($password)) {
            Response::redirect('/login', 'กรุณากรอกอีเมลและรหัสผ่าน', 'danger');
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Response::redirect('/login', 'อีเมลหรือรหัสผ่านไม่ถูกต้อง', 'danger');
        }

        Auth::login($user);
        ActivityLogger::log('login', "ผู้ดูแลระบบ {$user['name']} เข้าสู่ระบบสำเร็จ", (int)$user['id']);

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
