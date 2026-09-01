<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Core\ActivityLogger;
use App\Core\Paginator;
use App\Models\WasteType;

class WasteTypeController {
    public function index(): void {
        $allWasteTypes = WasteType::allWithStats();
        $page = max(1, (int)Request::input('page', 1));
        $perPage = max(1, (int)Request::input('per_page', 8));
        $paginator = Paginator::fromArray($allWasteTypes, $page, $perPage);
        $wasteTypes = $paginator->items;
        $unreadCount = NotificationService::getUnreadCount(Auth::id());

        View::render('admin.waste_types.index', [
            'title' => 'จัดการประเภทขยะ | Admin Portal',
            'wasteTypes' => $wasteTypes,
            'paginator' => $paginator,
            'unreadCount' => $unreadCount
        ]);
    }

    public function store(): void {
        $name = trim(Request::input('name', ''));
        $description = trim(Request::input('description', ''));
        $icon = trim(Request::input('icon', 'trash-2'));
        $isActive = Request::input('is_active', '1');

        if (empty($name)) {
            Response::redirect('/admin/waste-types', 'กรุณาระบุชื่อประเภทขยะ', 'danger');
        }

        $imagePath = null;
        $file = Request::file('image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (in_array($file['type'], $allowedTypes)) {
                $uploadDir = BASE_PATH . '/public/uploads/waste_types/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'type_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $imagePath = 'uploads/waste_types/' . $filename;
                }
            }
        }

        $id = WasteType::create([
            'name' => $name,
            'description' => $description,
            'icon' => $icon ?: 'trash-2',
            'image' => $imagePath,
            'is_active' => (int)$isActive
        ]);

        ActivityLogger::log('create_waste_type', "Admin เพิ่มประเภทขยะใหม่: '{$name}' (ID: {$id})", Auth::id());

        Response::redirect('/admin/waste-types', "เพิ่มประเภทขยะ '{$name}' เรียบร้อยแล้ว", 'success');
    }

    public function update(int $id): void {
        $type = WasteType::findById($id);
        if (!$type) {
            Response::redirect('/admin/waste-types', 'ไม่พบประเภทขยะที่ระบุ', 'warning');
        }

        $name = trim(Request::input('name', ''));
        $description = trim(Request::input('description', ''));
        $icon = trim(Request::input('icon', 'trash-2'));
        $isActive = Request::input('is_active', '1');

        if (empty($name)) {
            Response::redirect('/admin/waste-types', 'กรุณาระบุชื่อประเภทขยะ', 'danger');
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'icon' => $icon ?: 'trash-2',
            'is_active' => (int)$isActive
        ];

        $file = Request::file('image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (in_array($file['type'], $allowedTypes)) {
                $uploadDir = BASE_PATH . '/public/uploads/waste_types/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'type_' . $id . '_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $data['image'] = 'uploads/waste_types/' . $filename;
                }
            }
        }

        WasteType::update($id, $data);

        ActivityLogger::log('update_waste_type', "Admin แก้ไขประเภทขยะ: '{$name}' (ID: {$id})", Auth::id());

        Response::redirect('/admin/waste-types', "บันทึกการแก้ไขประเภทขยะ '{$name}' เรียบร้อยแล้ว", 'success');
    }


    public function delete(int $id): void {
        $type = WasteType::findById($id);
        if (!$type) {
            Response::redirect('/admin/waste-types', 'ไม่พบประเภทขยะที่ระบุ', 'warning');
        }

        WasteType::delete($id);
        ActivityLogger::log('delete_waste_type', "Admin ลบ/ปิดการใช้งานประเภทขยะ: '{$type['name']}' (ID: {$id})", Auth::id());

        Response::redirect('/admin/waste-types', "ลบหรือปิดการใช้งานประเภทขยะ '{$type['name']}' เรียบร้อยแล้ว", 'success');
    }
}
