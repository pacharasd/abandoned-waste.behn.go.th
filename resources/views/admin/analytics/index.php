<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Top Analytics Header & Export -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div>
            <h2 class="text-xl font-bold text-slate-900">รายงานและสถิติการจัดเก็บขยะ</h2>
            <p class="text-xs text-slate-400 mt-0.5">สรุปภาพรวม ประสิทธิภาพการดำเนินงาน และส่งออกข้อมูล</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/export/csv" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>ส่งออกรายงาน (Excel / CSV)</span>
            </a>
        </div>
    </div>

    <!-- KPIs Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">อัตราการจัดเก็บสำเร็จ</div>
            <?php 
            $rate = ($metrics['total'] ?? 0) > 0 ? (($metrics['completed'] ?? 0) / $metrics['total']) * 100 : 0;
            ?>
            <div class="text-3xl font-bold text-emerald-600 mt-1"><?= number_format($rate, 1) ?>%</div>
            <div class="text-xs text-slate-500 mt-1"><?= $metrics['completed'] ?? 0 ?> จากทั้งหมด <?= $metrics['total'] ?? 0 ?> รายการ</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">ขยะที่จัดเก็บได้จริงสะสม</div>
            <div class="text-3xl font-bold text-slate-900 mt-1"><?= number_format($metrics['actual_weight_total'] ?? 0, 1) ?> <span class="text-sm font-normal text-slate-500">กก.</span></div>
            <div class="text-xs text-slate-500 mt-1">ชั่งและตรวจวัดจริง ณ จุดปฏิบัติงาน</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">ขยะรอการจัดเก็บ</div>
            <?php 
            $pendingCount = ($metrics['pending'] ?? 0) + ($metrics['reviewing'] ?? 0) + ($metrics['in_progress'] ?? 0);
            ?>
            <div class="text-3xl font-bold text-amber-600 mt-1"><?= $pendingCount ?> <span class="text-sm font-normal text-slate-500">รายการ</span></div>
            <div class="text-xs text-slate-500 mt-1">อยู่ระหว่างรอรับเรื่องและดำเนินการ</div>
        </div>
    </div>

    <!-- Breakdown by Waste Type Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-base">สถิติจำแนกตามประเภทของขยะ</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">ประเภทขยะ</th>
                        <th class="px-6 py-3.5 text-center">จำนวนที่ได้รับแจ้ง</th>
                        <th class="px-6 py-3.5 text-right">น้ำหนักรวมโดยประมาณ/จริง (กก.)</th>
                        <th class="px-6 py-3.5 text-center">สัดส่วน (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php 
                    $grandTotal = $metrics['total'] > 0 ? $metrics['total'] : 1;
                    foreach ($wasteTypeStats as $ws): 
                        $pct = ($ws['count'] / $grandTotal) * 100;
                    ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span><?= htmlspecialchars($ws['name']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-800">
                                <?= $ws['count'] ?> รายการ
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-bold text-emerald-700">
                                <?= number_format($ws['total_weight'], 1) ?> กก.
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: <?= $pct ?>%;"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 font-mono"><?= number_format($pct, 1) ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
