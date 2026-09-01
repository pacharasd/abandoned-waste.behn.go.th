<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Search & Filter Card -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Search term -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">ค้นหาคำสำคัญ</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="เลขที่, ชื่อผู้แจ้ง, เบอร์โทร, สถานที่"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">สถานะงาน</label>
                    <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                        <option value="">-- ทุกสถานะ --</option>
                        <?php 
                        $statusOptions = ['รอรับเรื่อง', 'กำลังตรวจสอบ', 'กำลังดำเนินการ', 'จัดเก็บเรียบร้อยแล้ว', 'ยกเลิก'];
                        foreach ($statusOptions as $st):
                        ?>
                            <option value="<?= $st ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Waste Type Filter -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">ประเภทขยะ</label>
                    <select name="waste_type_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                        <option value="">-- ทุกประเภทขยะ --</option>
                        <?php foreach ($wasteTypes as $wt): ?>
                            <option value="<?= $wt['id'] ?>" <?= ($filters['waste_type_id'] ?? '') == $wt['id'] ? 'selected' : '' ?>><?= htmlspecialchars($wt['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Date Range & Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2 border-t border-slate-100">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <span>ตั้งแต่วันที่:</span>
                        <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <span>ถึง:</span>
                        <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                        ล้างตัวกรอง
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                        <span>กรองข้อมูล</span>
                    </button>
                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/export/csv?<?= http_build_query($filters) ?>" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition flex items-center gap-1.5">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Export CSV</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Reports Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-base">รายการแจ้งจัดเก็บขยะทั้งหมด (<?= count($reports) ?> รายการ)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[950px]">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 whitespace-nowrap">เลขที่รายการ</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">ผู้แจ้ง / เบอร์ติดต่อ</th>
                        <th class="px-5 py-3.5">สถานที่จัดเก็บ</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">ประเภทขยะ</th>
                        <th class="px-5 py-3.5 whitespace-nowrap text-right">น้ำหนัก (กก.)</th>
                        <th class="px-5 py-3.5 whitespace-nowrap text-center min-w-[170px]">สถานะ</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">วันที่แจ้ง</th>
                        <th class="px-5 py-3.5 text-right whitespace-nowrap">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <span>ไม่พบรายการที่ตรงกับเงื่อนไขการค้นหา</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $r): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Report Number -->
                                <td class="px-5 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/<?= $r['id'] ?>" class="text-emerald-700 hover:text-emerald-800 hover:underline">
                                        <?= htmlspecialchars($r['report_number']) ?>
                                    </a>
                                </td>

                                <!-- Reporter Info -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-xs"><?= htmlspecialchars($r['reporter_name']) ?></div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5"><?= htmlspecialchars($r['reporter_phone']) ?></div>
                                </td>

                                <!-- Address -->
                                <td class="px-5 py-4 max-w-xs" title="<?= htmlspecialchars($r['address']) ?>">
                                    <div class="truncate text-xs text-slate-700"><?= htmlspecialchars($r['address']) ?></div>
                                </td>

                                <!-- Waste Type -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium">
                                        🏷️ <?= htmlspecialchars($r['waste_type_name']) ?>
                                    </span>
                                </td>

                                <!-- Weight -->
                                <td class="px-5 py-4 text-xs font-mono whitespace-nowrap text-right">
                                    <?php if ($r['actual_weight'] !== null): ?>
                                        <span class="text-emerald-700 font-bold text-sm"><?= number_format($r['actual_weight'], 1) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400"><?= number_format($r['estimated_weight'], 1) ?> <span class="text-[10px]">(ประมาณ)</span></span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Badge -->
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

                                <!-- Submitted Date -->
                                <td class="px-5 py-4 text-xs text-slate-400 whitespace-nowrap font-mono">
                                    <?= date('d/m/Y H:i', strtotime($r['submitted_at'] ?? $r['created_at'])) ?>
                                </td>

                                <!-- Action Button -->
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/<?= $r['id'] ?>" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition inline-flex items-center gap-1 shadow-sm shadow-emerald-600/20">
                                        <span>จัดการรายการ</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
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

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
