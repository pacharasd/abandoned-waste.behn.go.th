<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Staff Header Card -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
        <div>
            <div class="text-xs text-slate-400 font-semibold uppercase">รายการงานที่ได้รับมอบหมาย</div>
            <h2 class="text-xl font-bold text-slate-900 mt-0.5">งานของฉัน (<?= count($jobs) ?> รายการ)</h2>
        </div>
        <div class="flex items-center gap-1.5 bg-emerald-50 px-3 py-1.5 rounded-xl text-emerald-800 text-xs font-bold border border-emerald-200">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
            <span>พร้อมปฏิบัติงาน</span>
        </div>
    </div>

    <!-- Quick Status Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/dashboard" class="px-3.5 py-2 rounded-xl font-semibold whitespace-nowrap transition <?= empty($statusFilter) ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200' ?>">
            ทั้งหมด (<?= count($jobs) ?>)
        </a>
        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/dashboard?status=กำลังดำเนินการ" class="px-3.5 py-2 rounded-xl font-semibold whitespace-nowrap transition <?= $statusFilter === 'กำลังดำเนินการ' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200' ?>">
            กำลังดำเนินการ
        </a>
        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/dashboard?status=มอบหมายงานแล้ว" class="px-3.5 py-2 rounded-xl font-semibold whitespace-nowrap transition <?= $statusFilter === 'มอบหมายงานแล้ว' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200' ?>">
            งานใหม่ที่ต้องรับ
        </a>
        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/dashboard?status=จัดเก็บเรียบร้อยแล้ว" class="px-3.5 py-2 rounded-xl font-semibold whitespace-nowrap transition <?= $statusFilter === 'จัดเก็บเรียบร้อยแล้ว' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200' ?>">
            เสร็จสิ้นแล้ว
        </a>
    </div>

    <!-- Jobs Cards List -->
    <div class="space-y-4">
        <?php if (empty($jobs)): ?>
            <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-400 space-y-3">
                <i data-lucide="clipboard-x" class="w-10 h-10 mx-auto text-slate-300"></i>
                <div class="text-sm font-medium">ไม่มีรายการงานที่ต้องปฏิบัติในหมวดหมู่นี้</div>
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $j): ?>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-300 transition space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="font-mono font-bold text-base text-slate-900">
                                <?= htmlspecialchars($j['report_number']) ?>
                            </span>
                            <div class="text-xs font-semibold text-emerald-700 mt-0.5">
                                🏷️ <?= htmlspecialchars($j['waste_type_name']) ?>
                            </div>
                        </div>

                        <?php
                        $badge = 'bg-slate-100 text-slate-700';
                        if ($j['status'] === 'มอบหมายงานแล้ว') $badge = 'bg-indigo-50 text-indigo-800 border border-indigo-200';
                        if ($j['status'] === 'รับงานแล้ว' || $j['status'] === 'กำลังดำเนินการ' || $j['status'] === 'กำลังเดินทาง') $badge = 'bg-blue-50 text-blue-800 border border-blue-200';
                        if ($j['status'] === 'จัดเก็บเรียบร้อยแล้ว') $badge = 'bg-emerald-50 text-emerald-800 border border-emerald-300';
                        ?>
                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badge ?>">
                            <?= htmlspecialchars($j['status']) ?>
                        </span>
                    </div>

                    <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-start gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5"></i>
                        <span class="line-clamp-2"><?= htmlspecialchars($j['address']) ?></span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                        <div>ประมาณ: <strong><?= number_format($j['estimated_weight'], 1) ?> กก.</strong></div>
                        <div><?= date('d/m/Y H:i', strtotime($j['created_at'])) ?></div>
                    </div>

                    <!-- Action Button -->
                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/jobs/<?= $j['id'] ?>" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm">
                        <i data-lucide="navigation" class="w-4 h-4"></i>
                        <span>เปิดหน้ารายละเอียด & ปฏิบัติงาน</span>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/staff.php';
?>
