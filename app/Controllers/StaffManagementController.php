<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Core\ActivityLogger;
use App\Models\User;

class StaffManagementController {
    public function index(): void {
        $staffList = User::getAllStaff();
        $unreadCount = NotificationService::getUnreadCount(Auth::id());

        View::render('admin.staff.index', [
            'title' => 'จัดการเจ้าหน้าที่จัดเก็บขยะ | Admin Portal',
            'staffList' => $staffList,
            'unreadCount' => $unreadCount
        ]);
    }

    public function store(): void {
        $name = trim(Request::input('name', ''));
        $email = trim(Request::input('email', ''));
        $phone = trim(Request::input('phone', ''));
        $password = Request::input('password', '');

        if (empty($name) || empty($email) || empty($password)) {
            Response::redirect('/admin/staff', 'กรุณากรอกข้อมูลให้ครบถ้วน (ชื่อ, อีเมล, รหัสผ่าน)', 'danger');
        }

        // Check email uniqueness
        if (User::findByEmail($email)) {
            Response::redirect('/admin/staff', 'อีเมลนี้มีอยู่ในระบบแล้ว', 'danger');
        }

        $id = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => 'staff'
        ]);

        ActivityLogger::log('create_staff', "เพิ่มเจ้าหน้าที่ใหม่: {$name} ({$email})", Auth::id());

        Response::redirect('/admin/staff', "เพิ่มเจ้าหน้าที่ '{$name}' เรียบร้อยแล้ว", 'success');
    }

    public function update(int $id): void {
        $user = User::findById($id);
        if (!$user || $user['role'] !== 'staff') {
            Response::redirect('/admin/staff', 'ไม่พบข้อมูลเจ้าหน้าที่', 'warning');
        }

        $name = trim(Request::input('name', ''));
        $email = trim(Request::input('email', ''));
        $phone = trim(Request::input('phone', ''));
        $password = Request::input('password', '');

        // Email conflict check
        $existing = User::findByEmail($email);
        if ($existing && $existing['id'] != $id) {
            Response::redirect('/admin/staff', 'อีเมลนี้ถูกใช้งานโดยบัญชีอื่นแล้ว', 'danger');
        }

        User::update($id, [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => 'staff'
        ]);

        ActivityLogger::log('update_staff', "แก้ไขข้อมูลเจ้าหน้าที่: {$name}", Auth::id());

        Response::redirect('/admin/staff', "อัปเดตข้อมูลเจ้าหน้าที่ '{$name}' เรียบร้อยแล้ว", 'success');
    }

    public function delete(int $id): void {
        $user = User::findById($id);
        if ($user && $user['role'] === 'staff') {
            User::delete($id);
            ActivityLogger::log('delete_staff', "ลบบัญชีเจ้าหน้าที่: {$user['name']}", Auth::id());
            Response::redirect('/admin/staff', "ลบบัญชีเจ้าหน้าที่ '{$user['name']}' เรียบร้อยแล้ว", 'success');
        }

        Response::redirect('/admin/staff', 'ไม่สามารถลบบัญชีนี้ได้', 'danger');
    }
}
