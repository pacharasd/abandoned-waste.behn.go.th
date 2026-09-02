<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Top Header Card with Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-900">จัดการประเภทขยะ</h2>
            <p class="text-xs text-slate-400 mt-0.5">หมวดหมู่ขยะสำหรับการแจ้งเรื่องและการจัดเก็บของระบบ</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" data-modal-open="adminOrphanPosterModal" class="px-4 py-2.5 bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold rounded-xl border border-teal-200 transition flex items-center gap-2 w-fit">
                <i data-lucide="image" class="w-4 h-4 text-teal-600"></i>
                <span>ดูประกาศขยะกำพร้า</span>
            </button>
            <button type="button" data-modal-open="addTypeModal" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-sm shadow-emerald-600/20 w-fit">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>เพิ่มประเภทขยะใหม่</span>
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">ประเภทขยะทั้งหมด</div>
            <div class="text-2xl font-bold text-slate-900 mt-1"><?= count($wasteTypes) ?> <span class="text-sm font-normal text-slate-500">ประเภท</span></div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">เปิดใช้งานอยู่ (Active)</div>
            <?php 
            $activeCount = count(array_filter($wasteTypes, fn($t) => $t['is_active'] == 1));
            ?>
            <div class="text-2xl font-bold text-emerald-700 mt-1"><?= $activeCount ?> <span class="text-sm font-normal text-emerald-600/80">ประเภท</span></div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider">จำนวนรายการแจ้งสะสม</div>
            <?php 
            $totalReportsSum = array_sum(array_column($wasteTypes, 'reports_count'));
            ?>
            <div class="text-2xl font-bold text-blue-700 mt-1"><?= number_format($totalReportsSum) ?> <span class="text-sm font-normal text-blue-600/80">รายการ</span></div>
        </div>
    </div>

    <!-- Waste Types Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[750px]">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 whitespace-nowrap">ประเภทขยะ</th>
                        <th class="px-6 py-3.5">คำอธิบาย</th>
                        <th class="px-6 py-3.5 text-center whitespace-nowrap">รายการที่แจ้ง</th>
                        <th class="px-6 py-3.5 text-right whitespace-nowrap">น้ำหนักรวม (กก.)</th>
                        <th class="px-6 py-3.5 text-center whitespace-nowrap">สถานะ</th>
                        <th class="px-6 py-3.5 text-right whitespace-nowrap">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($wasteTypes)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">ยังไม่มีข้อมูลประเภทขยะในระบบ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($wasteTypes as $wt): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Type Name, Image & Icon -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($wt['image'])): ?>
                                            <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($wt['image']) ?>" 
                                                 alt="<?= htmlspecialchars($wt['name']) ?>" 
                                                 class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm flex-shrink-0">
                                        <?php else: ?>
                                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-base flex-shrink-0 border border-emerald-100">
                                                🏷️
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($wt['name']) ?></div>
                                            <div class="text-[10px] text-slate-400 font-mono">ID: #<?= $wt['id'] ?></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Description -->
                                <td class="px-6 py-4 text-xs text-slate-600 max-w-sm">
                                    <?= htmlspecialchars($wt['description'] ?: 'ไม่มีคำอธิบาย') ?>
                                </td>

                                <!-- Reports Count -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                        <?= number_format($wt['reports_count'] ?? 0) ?> รายการ
                                    </span>
                                </td>

                                <!-- Total Weight -->
                                <td class="px-6 py-4 font-mono font-bold text-xs text-emerald-700 text-right whitespace-nowrap">
                                    <?= number_format($wt['total_weight'] ?? 0, 1) ?>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <?php if (!empty($wt['is_active'])): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>เปิดใช้งาน</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>ปิดใช้งาน</span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" class="btn-edit-type px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center gap-1.5" data-type='<?= htmlspecialchars(json_encode($wt), ENT_QUOTES, 'UTF-8') ?>'>
                                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                            <span>แก้ไข</span>
                                        </button>
                                        <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/waste-types/<?= $wt['id'] ?>/delete" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบ/ปิดการใช้งานประเภทขยะนี้?');">
                                            <?= \App\Core\CSRF::field() ?>
                                            <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-xl transition">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
            </table>
        </div>

        <!-- Pagination Footer Bar -->
        <?php if (isset($paginator)): ?>
            <?= $paginator->render() ?>
        <?php endif; ?>
    </div>

</div>

<!-- Modal: Add Waste Type -->
<div id="addTypeModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-base">เพิ่มประเภทขยะใหม่</h3>
            <button type="button" data-modal-close="addTypeModal" class="p-1 text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/waste-types" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?= \App\Core\CSRF::field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">ชื่อประเภทขยะ *</label>
                <input type="text" name="name" required placeholder="เช่น ขยะสารเคมีอันตราย"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">คำอธิบาย / ตัวอย่างขยะ</label>
                <textarea name="description" rows="2" placeholder="ระบุตัวอย่างประเภทขยะ เช่น กระป๋องสเปรย์, แบตเตอรี่, หลอดไฟ"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">รูปภาพประกอบประเภทขยะ</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">สถานะการใช้งาน</label>
                <select name="is_active" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                    <option value="1">เปิดใช้งาน (Active)</option>
                    <option value="0">ปิดใช้งานชั่วคราว (Inactive)</option>
                </select>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" data-modal-close="addTypeModal" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                    ยกเลิก
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold">
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Waste Type -->
<div id="editTypeModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-base">แก้ไขประเภทขยะ</h3>
            <button type="button" data-modal-close="editTypeModal" class="p-1 text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editTypeForm" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?= \App\Core\CSRF::field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">ชื่อประเภทขยะ *</label>
                <input type="text" name="name" id="editTypeName" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">คำอธิบาย / ตัวอย่างขยะ</label>
                <textarea name="description" id="editTypeDescription" rows="2"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">เปลี่ยนรูปภาพประกอบ (หากต้องการเปลี่ยน)</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">สถานะการใช้งาน</label>
                <select name="is_active" id="editTypeIsActive" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                    <option value="1">เปิดใช้งาน (Active)</option>
                    <option value="0">ปิดใช้งานชั่วคราว (Inactive)</option>
                </select>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" data-modal-close="editTypeModal" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">
                    ยกเลิก
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold">
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Official Poster View for Admin -->
<div id="adminOrphanPosterModal" class="modal-backdrop-auto fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full max-h-[90vh] overflow-hidden flex flex-col shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center">
                    <i data-lucide="image" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">โปสเตอร์ขยะกำพร้า เทศบาลนครนนทบุรี</h4>
                    <span class="text-[10px] text-slate-500">ขยะที่รีไซเคิลไม่ได้ ซาเล้งไม่รับ</span>
                </div>
            </div>
            <button type="button" data-modal-close="adminOrphanPosterModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-2 bg-slate-100 flex items-center justify-center max-h-[70vh]">
            <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/orphan_waste_guide.jpg" alt="ประกาศขยะกำพร้า เทศบาลนครนนทบุรี" class="w-full h-auto rounded-xl shadow-xs">
        </div>
        <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">สำนักการสาธารณสุขและสิ่งแวดล้อม</span>
            <button type="button" data-modal-close="adminOrphanPosterModal" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>
</div>

<script <?= \App\Core\CSP::nonceAttr() ?>>
function openEditTypeModal(wt) {
    document.getElementById('editTypeName').value = wt.name;
    document.getElementById('editTypeDescription').value = wt.description || '';
    document.getElementById('editTypeIsActive').value = wt.is_active ? '1' : '0';
    document.getElementById('editTypeForm').action = '<?= htmlspecialchars($baseUrl ?: '') ?>/admin/waste-types/' + wt.id + '/update';
    document.getElementById('editTypeModal').classList.remove('hidden');
}

document.querySelectorAll('.btn-edit-type').forEach(btn => {
    btn.addEventListener('click', function() {
        const wt = JSON.parse(this.getAttribute('data-type'));
        openEditTypeModal(wt);
    });
});
</script>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
