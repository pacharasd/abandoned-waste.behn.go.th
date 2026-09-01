<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div class="flex items-center gap-4">
            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">รายการแจ้งจัดเก็บขยะ</div>
                <h2 class="text-2xl font-mono font-bold text-slate-900 mt-0.5"><?= htmlspecialchars($report['report_number']) ?></h2>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <?php
            $badge = 'bg-slate-100 text-slate-700';
            if ($report['status'] === 'รอรับเรื่อง') $badge = 'bg-amber-50 text-amber-800 border border-amber-200';
            if ($report['status'] === 'กำลังตรวจสอบ') $badge = 'bg-yellow-50 text-yellow-800 border border-yellow-200';
            if ($report['status'] === 'กำลังดำเนินการ') $badge = 'bg-blue-50 text-blue-800 border border-blue-200';
            if ($report['status'] === 'จัดเก็บเรียบร้อยแล้ว') $badge = 'bg-emerald-50 text-emerald-800 border border-emerald-300';
            if ($report['status'] === 'ยกเลิก') $badge = 'bg-rose-50 text-rose-800 border border-rose-200';
            ?>
            <span class="px-4 py-2 rounded-xl text-xs font-bold <?= $badge ?>">
                สถานะ: <?= htmlspecialchars($report['status']) ?>
            </span>
        </div>
    </div>

    <!-- Main 2-Column Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column (7 cols): Details, Photos, History -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Information Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80 space-y-6">
                <h3 class="font-bold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-emerald-600"></i>
                    <span>ข้อมูลการแจ้งเรื่อง</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="text-xs text-slate-400">ชื่อผู้แจ้งเรื่อง</div>
                        <div class="font-bold text-slate-800 mt-1"><?= htmlspecialchars($report['reporter_name']) ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">เบอร์โทรศัพท์</div>
                        <div class="font-mono text-slate-800 mt-1 font-bold flex items-center gap-2">
                            <span><?= htmlspecialchars($report['reporter_phone']) ?></span>
                            <a href="tel:<?= preg_replace('/[^0-9]/', '', $report['reporter_phone']) ?>" class="p-1 text-emerald-600 hover:bg-emerald-50 rounded">
                                <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Multiple Waste Types & Weights Breakdown Table -->
                    <div class="sm:col-span-2">
                        <div class="text-xs font-semibold text-slate-500 mb-2">รายการประเภทขยะและน้ำหนัก (Breakdown)</div>
                        <div class="bg-slate-50 rounded-2xl border border-slate-200/80 overflow-hidden">
                            <table class="w-full text-xs">
                                <thead class="bg-slate-100/90 text-slate-600 font-semibold border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left">ประเภทขยะ</th>
                                        <th class="px-4 py-2.5 text-right">ประมาณการ (กก.)</th>
                                        <th class="px-4 py-2.5 text-right">จัดเก็บจริง (กก.)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <?php if (!empty($report['items'])): ?>
                                        <?php foreach ($report['items'] as $item): ?>
                                            <tr>
                                                <td class="px-4 py-2.5 font-bold text-slate-900 flex items-center gap-2">
                                                    <span class="text-sm">🏷️</span>
                                                    <span><?= htmlspecialchars($item['waste_type_name']) ?></span>
                                                </td>
                                                <td class="px-4 py-2.5 font-mono text-right text-slate-600">
                                                    <?= number_format($item['estimated_weight'], 1) ?>
                                                </td>
                                                <td class="px-4 py-2.5 font-mono font-bold text-right <?= $item['actual_weight'] !== null ? 'text-emerald-700' : 'text-slate-400' ?>">
                                                    <?= $item['actual_weight'] !== null ? number_format($item['actual_weight'], 1) : '-' ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td class="px-4 py-2.5 font-bold text-slate-900">🏷️ <?= htmlspecialchars($report['waste_type_name']) ?></td>
                                            <td class="px-4 py-2.5 font-mono text-right"><?= number_format($report['estimated_weight'], 1) ?></td>
                                            <td class="px-4 py-2.5 font-mono text-right"><?= $report['actual_weight'] !== null ? number_format($report['actual_weight'], 1) : '-' ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="bg-slate-100/60 font-bold border-t border-slate-200 text-slate-900">
                                    <tr>
                                        <td class="px-4 py-2.5">รวมทั้งหมด</td>
                                        <td class="px-4 py-2.5 font-mono text-right text-slate-800"><?= number_format($report['estimated_weight'], 1) ?> กก.</td>
                                        <td class="px-4 py-2.5 font-mono text-right text-emerald-700"><?= $report['actual_weight'] !== null ? number_format($report['actual_weight'], 1) . ' กก.' : 'ยังไม่ชั่ง' ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="text-xs text-slate-400">สถานที่จัดเก็บ / จุดสังเกต</div>
                        <div class="text-slate-800 mt-1 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            📍 <?= htmlspecialchars($report['address']) ?>
                        </div>
                    </div>
                    <?php if (!empty($report['description'])): ?>
                        <div class="sm:col-span-2">
                            <div class="text-xs text-slate-400">รายละเอียดเพิ่มเติมจากผู้แจ้ง</div>
                            <div class="text-slate-700 mt-1 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <?= htmlspecialchars($report['description']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Photos Comparison (Before & After) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80 space-y-4">
                <h3 class="font-bold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4 text-emerald-600"></i>
                    <span>รูปภาพประกอบการดำเนินงาน (Before / After)</span>
                </h3>

                <?php if (empty($report['images'])): ?>
                    <div class="p-8 text-center text-slate-400 text-sm">
                        ไม่มีรูปภาพแนบในรายการนี้
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($report['images'] as $img): ?>
                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold <?= $img['image_type'] === 'after' ? 'text-emerald-700' : 'text-amber-700' ?>">
                                        <?= $img['image_type'] === 'after' ? '✅ หลังจัดเก็บ (After Photo)' : '📷 ก่อนจัดเก็บ (Before Photo)' ?>
                                    </span>
                                    <span class="text-[10px] text-slate-400"><?= date('d/m/Y H:i', strtotime($img['created_at'])) ?></span>
                                </div>
                                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($img['image_path']) ?>" target="_blank">
                                    <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($img['image_path']) ?>" 
                                         alt="Photo" 
                                         class="w-full h-48 object-cover rounded-xl border border-slate-200 hover:opacity-90 transition">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Status History Timeline -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80 space-y-4">
                <h3 class="font-bold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-emerald-600"></i>
                    <span>ประวัติการดำเนินงาน (Timeline)</span>
                </h3>

                <div class="space-y-4 pl-2">
                    <?php if (empty($report['history'])): ?>
                        <div class="text-xs text-slate-400">ยังไม่มีบันทึกประวัติ</div>
                    <?php else: ?>
                        <?php foreach ($report['history'] as $h): ?>
                            <div class="flex items-start gap-4 text-sm">
                                <div class="w-3 h-3 rounded-full bg-emerald-600 mt-1.5 flex-shrink-0 ring-4 ring-emerald-50"></div>
                                <div class="flex-1 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                    <div class="flex items-center justify-between">
                                        <div class="font-bold text-slate-900 text-xs">
                                            <?= htmlspecialchars($h['new_status']) ?>
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            <?= date('d/m/Y H:i น.', strtotime($h['created_at'])) ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($h['note'])): ?>
                                        <div class="text-xs text-slate-600 mt-1"><?= htmlspecialchars($h['note']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Right Column (5 cols): Map & Admin Direct Action -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Location Map Card -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 space-y-3">
                <h4 class="font-bold text-slate-900 text-sm flex items-center justify-between">
                    <span>พิกัดสถานที่</span>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" target="_blank" class="text-xs text-emerald-600 hover:underline flex items-center gap-1 font-normal">
                        <span>เปิด Google Maps</span>
                        <i data-lucide="external-link" class="w-3 h-3"></i>
                    </a>
                </h4>
                <div class="rounded-xl overflow-hidden border border-slate-200 h-[220px]" id="detailMap"></div>
                <div class="text-[11px] font-mono text-slate-500 text-center">
                    Lat: <?= $report['latitude'] ?> | Lng: <?= $report['longitude'] ?>
                </div>
            </div>

            <!-- Unified Admin Action Card (Manage & Complete Job) -->
            <div class="bg-gradient-to-br from-white to-emerald-50/40 p-6 rounded-2xl shadow-sm border-2 border-emerald-500/20 space-y-5">
                <h4 class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-200 flex items-center gap-2">
                    <i data-lucide="check-square" class="w-4 h-4 text-emerald-600"></i>
                    <span>จัดการสถานะและบันทึกผลการจัดเก็บ</span>
                </h4>

                <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/<?= $report['id'] ?>/status" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <?= \App\Core\CSRF::field() ?>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">สถานะการดำเนินงาน *</label>
                        <select name="status" id="statusSelect" required class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <?php 
                            $statusList = ['รอรับเรื่อง', 'กำลังตรวจสอบ', 'กำลังดำเนินการ', 'จัดเก็บเรียบร้อยแล้ว', 'ยกเลิก'];
                            foreach ($statusList as $stItem):
                            ?>
                                <option value="<?= $stItem ?>" <?= $report['status'] === $stItem ? 'selected' : '' ?>><?= $stItem ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Individual Item Actual Weights (if multiple items exist) -->
                    <?php if (!empty($report['items']) && count($report['items']) > 1): ?>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                            <label class="block text-xs font-bold text-slate-800">
                                ชั่งน้ำหนักจริงแยกตามประเภท (กก.)
                            </label>
                            <?php foreach ($report['items'] as $it): ?>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs text-slate-700 font-medium truncate">🏷️ <?= htmlspecialchars($it['waste_type_name']) ?></span>
                                    <div class="relative w-28 flex-shrink-0">
                                        <input type="number" step="0.1" min="0" 
                                               name="item_actual_weights[<?= $it['id'] ?>]" 
                                               value="<?= $it['actual_weight'] ?? '' ?>" 
                                               placeholder="<?= number_format($it['estimated_weight'], 1) ?>"
                                               class="w-full pl-2.5 pr-8 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-[10px] text-slate-400 font-semibold">กก.</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                น้ำหนักขยะที่จัดเก็บได้จริง (กิโลกรัม)
                            </label>
                            <div class="relative">
                                <input type="number" step="0.1" min="0" name="actual_weight" value="<?= $report['actual_weight'] ?? '' ?>" placeholder="เช่น 35.5"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-slate-400 font-semibold">กก.</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            อัปโหลดรูปภาพหลังจัดเก็บ (After Photo)
                        </label>
                        <input type="file" name="after_image" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">บันทึกหมายเหตุการดำเนินงาน</label>
                        <textarea name="note" rows="2" placeholder="เช่น ดำเนินการจัดเก็บและทำความสะอาดเรียบร้อยแล้ว"
                                  class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>บันทึกการดำเนินการ</span>
                    </button>
                </form>
            </div>


        </div>

    </div>

</div>

<!-- Leaflet Detail Map Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = <?= (float)$report['latitude'] ?>;
    const lng = <?= (float)$report['longitude'] ?>;

    const map = L.map('detailMap', {
        preferCanvas: true,
        zoomAnimation: true,
        fadeAnimation: true
    }).setView([lat, lng], 15);
    setTimeout(() => map.invalidateSize(), 150);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("<b><?= htmlspecialchars($report['report_number']) ?></b><br><?= htmlspecialchars($report['address']) ?>")
        .openPopup();
});
</script>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
