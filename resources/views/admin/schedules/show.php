<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/schedules" class="hover:text-emerald-600 transition">จัดการรอบวันจัดเก็บ</a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">รายละเอียดรอบ</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($schedule['title']) ?></h2>
        </div>

        <div class="flex items-center gap-3">
            <?php
            $statusBadge = [
                'active' => ['class' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'label' => '🟢 เปิดรับแจ้งอยู่ (Active)'],
                'upcoming' => ['class' => 'bg-blue-50 text-blue-800 border-blue-200', 'label' => '🔵 รอบถัดไป (Upcoming)'],
                'collecting' => ['class' => 'bg-amber-50 text-amber-800 border-amber-200', 'label' => '🟡 กำลังจัดเก็บ (Collecting)'],
                'completed' => ['class' => 'bg-slate-100 text-slate-600 border-slate-200', 'label' => '⚪ จัดเก็บเสร็จสิ้น (Completed)'],
                'cancelled' => ['class' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => '🔴 ยกเลิก (Cancelled)']
            ][$schedule['status']] ?? ['class' => 'bg-slate-100 text-slate-600 border-slate-200', 'label' => $schedule['status']];
            ?>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border <?= $statusBadge['class'] ?>">
                <?= $statusBadge['label'] ?>
            </span>

            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/schedules" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>กลับหน้ารายการ</span>
            </a>
        </div>
    </div>

    <!-- Schedule Info & Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">วันจัดเก็บ</div>
            <div class="text-lg font-bold text-slate-900 mt-1"><?= date('d/m/Y', strtotime($schedule['collection_date'])) ?></div>
            <div class="text-xs text-slate-500 mt-0.5"><?= date('H:i', strtotime($schedule['start_time'])) ?> - <?= date('H:i', strtotime($schedule['end_time'])) ?> น.</div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">วันปิดรับแจ้งล่วงหน้า</div>
            <?php if (!empty($schedule['cutoff_date'])): ?>
                <div class="text-lg font-bold text-slate-900 mt-1"><?= date('d/m/Y', strtotime($schedule['cutoff_date'])) ?></div>
                <div class="text-xs text-amber-700 mt-0.5">เวลา <?= date('H:i น.', strtotime($schedule['cutoff_date'])) ?></div>
            <?php else: ?>
                <div class="text-lg font-bold text-slate-400 mt-1">ไม่กำหนด</div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">รายการขยะในรอบนี้</div>
            <div class="text-2xl font-bold text-emerald-700 mt-1"><?= count($reports) ?> <span class="text-sm font-normal text-slate-500">รายการ</span></div>
            <div class="text-xs text-slate-500 mt-0.5">จัดเก็บแล้ว <?= $schedule['completed_reports_count'] ?? 0 ?> รายการ</div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-teal-600 uppercase tracking-wider">น้ำหนักรวมในรอบ</div>
            <div class="text-2xl font-bold text-teal-700 mt-1"><?= number_format($schedule['total_weight'] ?? 0, 1) ?> <span class="text-sm font-normal text-teal-600/80">กก.</span></div>
            <div class="text-xs text-slate-500 mt-0.5">จัดเก็บจริงแล้ว <?= number_format($schedule['completed_weight'] ?? 0, 1) ?> กก.</div>
        </div>
    </div>

    <!-- Quick Status Update Form -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/schedules/<?= $schedule['id'] ?>/update" method="POST" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <?= \App\Core\CSRF::field() ?>
            <input type="hidden" name="title" value="<?= htmlspecialchars($schedule['title']) ?>">
            <input type="hidden" name="collection_date" value="<?= htmlspecialchars($schedule['collection_date']) ?>">
            <input type="hidden" name="start_time" value="<?= htmlspecialchars($schedule['start_time']) ?>">
            <input type="hidden" name="end_time" value="<?= htmlspecialchars($schedule['end_time']) ?>">
            <input type="hidden" name="area_zone" value="<?= htmlspecialchars($schedule['area_zone']) ?>">
            <input type="hidden" name="cutoff_date" value="<?= htmlspecialchars($schedule['cutoff_date'] ?? '') ?>">
            <input type="hidden" name="description" value="<?= htmlspecialchars($schedule['description'] ?? '') ?>">

            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-700 whitespace-nowrap">เปลี่ยนสถานะรอบจัดเก็บ:</span>
                <select name="status" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="upcoming" <?= $schedule['status'] === 'upcoming' ? 'selected' : '' ?>>🔵 รอบถัดไป (Upcoming)</option>
                    <option value="active" <?= $schedule['status'] === 'active' ? 'selected' : '' ?>>🟢 เปิดรับแจ้งอยู่ (Active)</option>
                    <option value="collecting" <?= $schedule['status'] === 'collecting' ? 'selected' : '' ?>>🟡 กำลังจัดเก็บวันนี้ (Collecting)</option>
                    <option value="completed" <?= $schedule['status'] === 'completed' ? 'selected' : '' ?>>⚪ จัดเก็บเสร็จสิ้น (Completed)</option>
                    <option value="cancelled" <?= $schedule['status'] === 'cancelled' ? 'selected' : '' ?>>🔴 ยกเลิก (Cancelled)</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                อัปเดตสถานะรอบ
            </button>
        </form>
    </div>

    <!-- Map of Waste Reports in this Cycle -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="map" class="w-4 h-4 text-emerald-600"></i>
                <span>แผนที่จุดขยะที่ต้องเข้าจัดเก็บในรอบนี้ (<?= count($reports) ?> จุด)</span>
            </h3>
        </div>
        <div id="scheduleReportsMap" class="w-full h-80 rounded-xl overflow-hidden border border-slate-200 bg-slate-100"></div>
    </div>

    <!-- Linked Reports Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="inbox" class="w-4 h-4 text-emerald-600"></i>
                <span>รายการขยะที่ผูกกับรอบนี้</span>
            </h3>
            <span class="text-xs text-slate-500"><?= count($reports) ?> รายการ</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[750px]">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 whitespace-nowrap">เลขที่รายการ</th>
                        <th class="px-6 py-3.5 whitespace-nowrap">ผู้แจ้ง</th>
                        <th class="px-6 py-3.5">สถานที่จัดเก็บ</th>
                        <th class="px-6 py-3.5 whitespace-nowrap">ประเภทขยะ</th>
                        <th class="px-6 py-3.5 text-right whitespace-nowrap">น้ำหนัก (กก.)</th>
                        <th class="px-6 py-3.5 text-center whitespace-nowrap">สถานะ</th>
                        <th class="px-6 py-3.5 text-right whitespace-nowrap">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">ยังไม่มีรายการขยะที่ผูกกับรอบจัดเก็บนี้</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $r): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 font-mono font-bold text-emerald-700 whitespace-nowrap">
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/<?= $r['id'] ?>" class="hover:underline">
                                        <?= htmlspecialchars($r['report_number']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800"><?= htmlspecialchars($r['reporter_name']) ?></div>
                                    <div class="text-xs text-slate-400"><?= htmlspecialchars($r['reporter_phone']) ?></div>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 max-w-xs truncate" title="<?= htmlspecialchars($r['address']) ?>">
                                    📍 <?= htmlspecialchars($r['address']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    🏷️ <?= htmlspecialchars($r['waste_type_name']) ?>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap font-mono font-semibold text-emerald-700">
                                    <?= number_format($r['actual_weight'] ?? $r['estimated_weight'] ?? 0, 1) ?>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold <?= $r['status'] === 'จัดเก็บเรียบร้อยแล้ว' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' ?>">
                                        <?= htmlspecialchars($r['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/<?= $r['id'] ?>" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        <span>เปิดดู</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer Bar -->
        <?php if (isset($paginator)): ?>
            <?= $paginator->render() ?>
        <?php endif; ?>
    </div>

</div>

<!-- Leaflet Map for this Schedule -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('scheduleReportsMap', {
        preferCanvas: true,
        zoomAnimation: true,
        fadeAnimation: true
    }).setView([13.8621, 100.5134], 12);
    setTimeout(() => map.invalidateSize(), 150);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const reports = <?= json_encode($reports, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const markers = [];

    reports.forEach(r => {
        if (r.latitude && r.longitude) {
            const marker = L.marker([parseFloat(r.latitude), parseFloat(r.longitude)]).addTo(map);
            marker.bindPopup(`
                <div class="text-xs p-1">
                    <strong class="text-emerald-700">${r.report_number}</strong><br>
                    <span>${r.address}</span><br>
                    <span class="text-slate-500">ประเภท: ${r.waste_type_name}</span>
                </div>
            `);
            markers.push(marker);
        }
    });

    if (markers.length > 0) {
        const group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.2));
    }
});
</script>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
