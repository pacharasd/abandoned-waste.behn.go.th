<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Top Header with Add Staff Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div>
            <h2 class="text-xl font-bold text-slate-900">จัดการเจ้าหน้าที่จัดเก็บขยะ</h2>
            <p class="text-xs text-slate-400 mt-0.5">รายชื่อเจ้าหน้าที่ภาคสนาม และภาระงานที่รับผิดชอบ</p>
        </div>

        <button type="button" onclick="document.getElementById('addStaffModal').classList.remove('hidden')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-sm">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>เพิ่มเจ้าหน้าที่ใหม่</span>
        </button>
    </div>

    <!-- Staff Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">ชื่อ - นามสกุล</th>
                        <th class="px-6 py-3.5">อีเมลสำหรับเข้าสู่ระบบ</th>
                        <th class="px-6 py-3.5">เบอร์โทรศัพท์</th>
                        <th class="px-6 py-3.5 text-center">งานที่กำลังทำ (Active)</th>
                        <th class="px-6 py-3.5 text-center">งานที่เสร็จแล้ว (Completed)</th>
                        <th class="px-6 py-3.5 text-right">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($staffList)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">ยังไม่มีข้อมูลเจ้าหน้าที่ในระบบ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($staffList as $stf): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs">
                                        <?= mb_substr($stf['name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($stf['name']) ?></div>
                                        <div class="text-xs text-slate-400">เจ้าหน้าที่ภาคสนาม</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                    <?= htmlspecialchars($stf['email']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                    <?= htmlspecialchars($stf['phone'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= ($stf['active_jobs_count'] ?? 0) > 0 ? 'bg-blue-50 text-blue-800 border border-blue-200' : 'bg-slate-100 text-slate-500' ?>">
                                        <?= $stf['active_jobs_count'] ?? 0 ?> งาน
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <?= $stf['completed_jobs_count'] ?? 0 ?> งาน
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($stf)) ?>)" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs transition">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/staff/<?= $stf['id'] ?>/delete" method="POST" onsubmit="return confirm('ยืนยันการลบบัญชีเจ้าหน้าที่นี้หรือไม่?')" class="inline">
                                            <?= \App\Core\CSRF::field() ?>
                                            <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs transition">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Add Staff -->
<div id="addStaffModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-base">เพิ่มเจ้าหน้าที่ใหม่</h3>
            <button type="button" onclick="document.getElementById('addStaffModal').classList.add('hidden')" class="p-1 text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/staff" method="POST" class="space-y-4">
            <?= \App\Core\CSRF::field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">ชื่อ - นามสกุล *</label>
                <input type="text" name="name" required placeholder="เช่น นายวิชัย สายลุย"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">อีเมลสำหรับเข้าสู่ระบบ *</label>
                <input type="email" name="email" required placeholder="wichai@waste.local"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">เบอร์โทรศัพท์</label>
                <input type="tel" name="phone" placeholder="08x-xxx-xxxx"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">รหัสผ่านเริ่มต้น *</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('addStaffModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                    ยกเลิก
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold">
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Staff -->
<div id="editStaffModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-base">แก้ไขข้อมูลเจ้าหน้าที่</h3>
            <button type="button" onclick="document.getElementById('editStaffModal').classList.add('hidden')" class="p-1 text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editStaffForm" method="POST" class="space-y-4">
            <?= \App\Core\CSRF::field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">ชื่อ - นามสกุล *</label>
                <input type="text" name="name" id="editName" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">อีเมลสำหรับเข้าสู่ระบบ *</label>
                <input type="email" name="email" id="editEmail" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">เบอร์โทรศัพท์</label>
                <input type="tel" name="phone" id="editPhone"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">เปลี่ยนรหัสผ่าน (เว้นว่างหากไม่เปลี่ยน)</label>
                <input type="password" name="password" placeholder="••••••••"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('editStaffModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                    ยกเลิก
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold">
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(staff) {
    document.getElementById('editName').value = staff.name;
    document.getElementById('editEmail').value = staff.email;
    document.getElementById('editPhone').value = staff.phone || '';
    document.getElementById('editStaffForm').action = '<?= htmlspecialchars($baseUrl ?: '') ?>/admin/staff/' + staff.id + '/update';
    document.getElementById('editStaffModal').classList.remove('hidden');
}
</script>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
