<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Auth;
use App\Core\NotificationService;
use App\Core\ActivityLogger;
use App\Core\Paginator;
use App\Core\Validator;
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

        $validator = Validator::make([
            'name' => $name,
            'description' => $description,
            'icon' => $icon
        ], [
            'name' => 'required|min:2|max:150',
            'description' => 'max:1000',
            'icon' => 'max:50'
        ]);

        if ($validator->fails()) {
            Response::redirect('/admin/waste-types', $validator->allErrors()[0] ?? 'กรุณาระบุชื่อประเภทขยะให้ถูกต้อง', 'danger');
        }

        $imagePath = null;
        $file = Request::file('image');
        if ($file) {
            $imagePath = Request::validateAndUploadImage($file, 'uploads/waste_types', 5 * 1024 * 1024);
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

        $validator = Validator::make([
            'name' => $name,
            'description' => $description,
            'icon' => $icon
        ], [
            'name' => 'required|min:2|max:150',
            'description' => 'max:1000',
            'icon' => 'max:50'
        ]);

        if ($validator->fails()) {
            Response::redirect('/admin/waste-types', $validator->allErrors()[0] ?? 'กรุณาระบุชื่อประเภทขยะให้ถูกต้อง', 'danger');
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'icon' => $icon ?: 'trash-2',
            'is_active' => (int)$isActive
        ];

        $file = Request::file('image');
        if ($file) {
            $uploadedPath = Request::validateAndUploadImage($file, 'uploads/waste_types', 5 * 1024 * 1024);
            if ($uploadedPath) {
                $data['image'] = $uploadedPath;
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
