<?php ob_start(); ?>

<div class="space-y-8">

    <!-- Top Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Card 1: Total -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between gap-3">
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">รายการแจ้งทั้งหมด</div>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-1"><?= number_format($metrics['total'] ?? 0) ?></div>
                <div class="text-[11px] text-slate-500 mt-0.5">รวมทุกสถานะ</div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="inbox" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </div>

        <!-- Card 2: Pending / Reviewing -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between gap-3">
            <div>
                <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider">รอรับเรื่อง / ตรวจสอบ</div>
                <div class="text-xl sm:text-2xl font-bold text-amber-700 mt-1"><?= number_format(($metrics['pending'] ?? 0) + ($metrics['reviewing'] ?? 0)) ?></div>
                <div class="text-[11px] text-amber-600/80 mt-0.5">ต้องการการตรวจสอบ</div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </div>

        <!-- Card 3: In Progress / Assigned -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between gap-3">
            <div>
                <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider">กำลังดำเนินการ</div>
                <div class="text-xl sm:text-2xl font-bold text-blue-700 mt-1"><?= number_format(($metrics['assigned'] ?? 0) + ($metrics['in_progress'] ?? 0)) ?></div>
                <div class="text-[11px] text-blue-600/80 mt-0.5">เจ้าหน้าที่รับงานแล้ว</div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <i data-lucide="truck" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </div>

        <!-- Card 4: Completed -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between gap-3">
            <div>
                <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">จัดเก็บเรียบร้อยแล้ว</div>
                <div class="text-xl sm:text-2xl font-bold text-emerald-700 mt-1"><?= number_format($metrics['completed'] ?? 0) ?></div>
                <div class="text-[11px] text-emerald-600/80 mt-0.5">สำเร็จสมบูรณ์</div>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics (Weights) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
        <div class="bg-gradient-to-r from-emerald-800 to-teal-700 text-white p-5 sm:p-6 rounded-2xl shadow-sm flex items-center justify-between gap-3">
            <div>
                <div class="text-xs text-emerald-200 uppercase font-semibold tracking-wider">น้ำหนักจัดเก็บจริงรวม (Actual Weight)</div>
                <div class="text-2xl sm:text-3xl font-bold mt-1"><?= number_format($metrics['actual_weight_total'] ?? 0, 2) ?> <span class="text-xs sm:text-base font-normal">กก.</span></div>
                <div class="text-[11px] sm:text-xs text-emerald-100/80 mt-1">ชั่งและยืนยันโดยเจ้าหน้าที่ภาคสนาม</div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="scale" class="w-6 h-6 sm:w-7 sm:h-7 text-emerald-300"></i>
            </div>
        </div>

        <div class="bg-slate-900 text-white p-5 sm:p-6 rounded-2xl shadow-sm flex items-center justify-between gap-3">
            <div>
                <div class="text-xs text-slate-400 uppercase font-semibold tracking-wider">น้ำหนักประมาณการรวม (Estimated Weight)</div>
                <div class="text-2xl sm:text-3xl font-bold mt-1"><?= number_format($metrics['estimated_weight_total'] ?? 0, 2) ?> <span class="text-xs sm:text-base font-normal">กก.</span></div>
                <div class="text-[11px] sm:text-xs text-slate-400 mt-1">ประเมินโดยประชาชนผู้แจ้งเรื่อง</div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="calculator" class="w-6 h-6 sm:w-7 sm:h-7 text-slate-300"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Trend Chart (2 cols) -->
        <div class="lg:col-span-2 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">สถิติจำนวนการแจ้งและการจัดเก็บรายเดือน</h3>
                    <p class="text-xs text-slate-400 mt-0.5">แนวโน้มย้อนหลัง 6 เดือน</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Waste Types Distribution (1 col) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base mb-1">สัดส่วนประเภทขยะ</h3>
                <p class="text-xs text-slate-400 mb-4">จำแนกตามประเภทที่ได้รับแจ้ง</p>
                <div class="h-52 relative">
                    <canvas id="wasteTypeChart"></canvas>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                <?php foreach (array_slice($wasteTypeStats, 0, 4) as $wt): ?>
                    <div class="flex items-center gap-1.5 text-slate-600 truncate">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                        <span class="truncate"><?= htmlspecialchars($wt['name']) ?>: <strong><?= $wt['count'] ?></strong></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Live Leaflet Map Overview for Admin -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">แผนที่พิกัดรายการแจ้งทั้งหมด</h3>
                <p class="text-xs text-slate-400 mt-0.5">คลิกที่หมุดเพื่อดูข้อมูลผู้แจ้ง และมอบหมายงานได้ทันที</p>
            </div>
            <div class="flex items-center gap-2">
                <select id="mapStatusFilter" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all">ทุกสถานะ</option>
                    <option value="pending">รอรับเรื่อง / ตรวจสอบ</option>
                    <option value="in_progress">กำลังดำเนินการ</option>
                    <option value="completed">จัดเก็บเรียบร้อยแล้ว</option>
                </select>
            </div>
        </div>
        <div class="rounded-xl overflow-hidden border border-slate-200 h-[380px]" id="adminOverviewMap"></div>
    </div>

    <!-- Recent Submissions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">รายการแจ้งจัดเก็บล่าสุด</h3>
                <p class="text-xs text-slate-400 mt-0.5">รายการที่ส่งเข้ามาในระบบล่าสุด</p>
            </div>
            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                <span>ดูทั้งหมด (<?= $metrics['total'] ?> รายการ)</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[900px]">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 whitespace-nowrap">เลขที่รายการ</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">ผู้แจ้ง</th>
                        <th class="px-5 py-3.5">สถานที่</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">ประเภทขยะ</th>
                        <th class="px-5 py-3.5 whitespace-nowrap text-right">น้ำหนัก (กก.)</th>
                        <th class="px-5 py-3.5 whitespace-nowrap text-center min-w-[160px]">สถานะ</th>
                        <th class="px-5 py-3.5 text-right whitespace-nowrap">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($recentReports)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">ยังไม่มีรายการแจ้งในระบบ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentReports as $r): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/<?= $r['id'] ?>" class="text-emerald-700 hover:text-emerald-800 hover:underline">
                                        <?= htmlspecialchars($r['report_number']) ?>
                                    </a>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-xs"><?= htmlspecialchars($r['reporter_name']) ?></div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5"><?= htmlspecialchars($r['reporter_phone']) ?></div>
                                </td>
                                <td class="px-5 py-4 max-w-xs truncate" title="<?= htmlspecialchars($r['address']) ?>">
                                    <div class="truncate text-xs text-slate-700"><?= htmlspecialchars($r['address']) ?></div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium">
                                        🏷️ <?= htmlspecialchars($r['waste_type_name']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-mono text-xs whitespace-nowrap text-right">
                                    <?php if ($r['actual_weight'] !== null): ?>
                                        <span class="text-emerald-700 font-bold text-sm"><?= number_format($r['actual_weight'], 1) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400"><?= number_format($r['estimated_weight'], 1) ?> <span class="text-[10px]">(ประมาณ)</span></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <?php
                                    $badge = 'bg-slate-100 text-slate-700 border-slate-200';
                                    $dot = 'bg-slate-400';

                                    if ($r['status'] === 'รอรับเรื่อง') {
                                        $badge = 'bg-amber-50 text-amber-800 border-amber-300/80 shadow-sm shadow-amber-500/10';
                                        $dot = 'bg-amber-500 animate-pulse';
                                    } elseif ($r['status'] === 'กำลังตรวจสอบ') {
                                        $badge = 'bg-yellow-50 text-yellow-800 border-yellow-300 shadow-sm';
                                        $dot = 'bg-yellow-500';
                                    } elseif ($r['status'] === 'กำลังดำเนินการ') {
                                        $badge = 'bg-blue-50 text-blue-800 border-blue-300 shadow-sm shadow-blue-500/10';
                                        $dot = 'bg-blue-500 animate-pulse';
                                    } elseif ($r['status'] === 'จัดเก็บเรียบร้อยแล้ว') {
                                        $badge = 'bg-emerald-50 text-emerald-800 border-emerald-300 shadow-sm shadow-emerald-500/10';
                                        $dot = 'bg-emerald-500';
                                    } elseif ($r['status'] === 'ยกเลิก') {
                                        $badge = 'bg-rose-50 text-rose-800 border-rose-300 shadow-sm';
                                        $dot = 'bg-rose-500';
                                    }
                                    ?>
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold border <?= $badge ?>">
                                        <span class="w-2 h-2 rounded-full <?= $dot ?> flex-shrink-0"></span>
                                        <span class="whitespace-nowrap"><?= htmlspecialchars($r['status']) ?></span>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/<?= $r['id'] ?>" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition inline-flex items-center gap-1 shadow-sm shadow-emerald-600/20">
                                        <span>จัดการ</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
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

<!-- Admin Map & Chart JS Scripts -->
<script <?= \App\Core\CSP::nonceAttr() ?>>
document.addEventListener('DOMContentLoaded', function() {

    // 1. Chart: Monthly Trend (Line Chart)
    const monthlyData = <?= json_encode($monthlyTrend) ?>;
    const ctxMonthly = document.getElementById('monthlyTrendChart').getContext('2d');
    
    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: monthlyData.map(d => d.month_name || d.month_key),
            datasets: [
                {
                    label: 'จำนวนที่แจ้งทั้งหมด',
                    data: monthlyData.map(d => d.total_reports),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'จัดเก็บเรียบร้อยแล้ว',
                    data: monthlyData.map(d => d.completed_reports),
                    borderColor: '#0284c7',
                    backgroundColor: 'transparent',
                    borderDash: [5, 5],
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // 2. Chart: Waste Types Distribution (Doughnut Chart)
    const wasteTypeData = <?= json_encode($wasteTypeStats) ?>;
    const ctxWaste = document.getElementById('wasteTypeChart').getContext('2d');

    new Chart(ctxWaste, {
        type: 'doughnut',
        data: {
            labels: wasteTypeData.map(w => w.name),
            datasets: [{
                data: wasteTypeData.map(w => w.count),
                backgroundColor: ['#059669', '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 3. Leaflet Map for Admin
    const adminMap = L.map('adminOverviewMap', {
        preferCanvas: true,
        zoomAnimation: true,
        fadeAnimation: true
    }).setView([13.8621, 100.5134], 12);
    setTimeout(() => adminMap.invalidateSize(), 150);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(adminMap);

    const allPoints = <?= json_encode(array_map(function($r) use ($baseUrl) {
        return [
            'id' => $r['id'],
            'report_number' => $r['report_number'],
            'status' => $r['status'],
            'waste_type' => $r['waste_type_name'],
            'address' => $r['address'],
            'lat' => (float)$r['latitude'],
            'lng' => (float)$r['longitude'],
            'reporter_name' => $r['reporter_name'],
            'reporter_phone' => $r['reporter_phone'],
            'detail_url' => ($baseUrl ?: '') . '/admin/reports/' . $r['id']
        ];
    }, $allReports)) ?>;

    let markerObjects = [];

    function renderMarkers(statusFilter) {
        // Clear existing markers
        markerObjects.forEach(m => adminMap.removeLayer(m));
        markerObjects = [];

        allPoints.forEach(pt => {
            if (statusFilter === 'pending' && !['รอรับเรื่อง', 'กำลังตรวจสอบ'].includes(pt.status)) return;
            if (statusFilter === 'in_progress' && !['กำลังดำเนินการ'].includes(pt.status)) return;
            if (statusFilter === 'completed' && pt.status !== 'จัดเก็บเรียบร้อยแล้ว') return;

            let pinColorClass = 'pin-amber';
            if (pt.status === 'จัดเก็บเรียบร้อยแล้ว') pinColorClass = 'pin-emerald';
            else if (pt.status === 'กำลังดำเนินการ') pinColorClass = 'pin-blue';
            else if (pt.status === 'ยกเลิก') pinColorClass = 'pin-rose';

            const icon = L.divIcon({
                className: 'admin-pin',
                html: `<div class="map-marker-pin-sm ${pinColorClass}"><div class="map-marker-dot"></div></div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            const marker = L.marker([pt.lat, pt.lng], { icon: icon }).addTo(adminMap);
            marker.bindPopup(`
                <div class="map-popup-card">
                    <div class="map-popup-title">${AppSecurity.escapeHtml(pt.report_number)}</div>
                    <div class="map-popup-type">🏷️ ${AppSecurity.escapeHtml(pt.waste_type)}</div>
                    <div class="map-popup-addr">📍 ${AppSecurity.escapeHtml(pt.address)}</div>
                    <div class="map-popup-meta">👤 ผู้แจ้ง: ${AppSecurity.escapeHtml(pt.reporter_name)} (${AppSecurity.escapeHtml(pt.reporter_phone)})</div>
                    <a href="${AppSecurity.escapeHtml(pt.detail_url)}" class="map-popup-link-dark text-white font-bold">
                        เปิดดูและจัดการรายการ
                    </a>
                </div>
            `);
            markerObjects.push(marker);
        });


        if (markerObjects.length > 0) {
            const group = L.featureGroup(markerObjects);
            adminMap.fitBounds(group.getBounds().pad(0.1));
        }
    }

    renderMarkers('all');

    document.getElementById('mapStatusFilter')?.addEventListener('change', function() {
        renderMarkers(this.value);
    });
});
</script>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
