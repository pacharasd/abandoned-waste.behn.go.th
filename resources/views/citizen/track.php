<?php ob_start(); ?>

<div class="py-10 lg:py-14 bg-slate-50 min-h-[80vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Tracking Header & Search Bar -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80">
            <div class="max-w-2xl mx-auto text-center space-y-2 mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">ติดตามสถานะการจัดเก็บขยะ</h1>
                <p class="text-slate-500 text-xs sm:text-sm">กรอกเลขที่รายการแจ้งจัดเก็บ หรือเบอร์โทรศัพท์ที่ใช้แจ้งเรื่อง</p>
            </div>

            <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/track" method="GET" class="max-w-xl mx-auto">
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" required placeholder="เช่น WB-2026-000001 หรือ 0812345678"
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                    </div>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20">
                        <span>ค้นหา</span>
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($searchWarning)): ?>
            <!-- Security / Validation Warning -->
            <div class="bg-amber-50 border border-amber-200 rounded-3xl p-6 text-center space-y-2">
                <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="shield-alert" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-amber-900"><?= htmlspecialchars($searchWarning) ?></h3>
                <p class="text-xs text-amber-700">เพื่อความปลอดภัยและการคุ้มครองข้อมูลส่วนบุคคล (PDPA) กรุณาระบุเบอร์โทรศัพท์ที่ถูกต้องแบบเต็มจำนวน</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($search) && empty($report) && empty($phoneReports) && empty($searchWarning)): ?>
            <!-- Not Found State -->
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-4">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="search-x" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">ไม่พบข้อมูลสำหรับคำค้น "<?= htmlspecialchars($search) ?>"</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">กรุณาตรวจสอบความถูกต้องของเลขที่รายการ (เช่น WB-2026-000001) หรือเบอร์โทรศัพท์อีกครั้ง</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($phoneReports) && empty($report)): ?>
            <!-- Multiple Reports Found by Phone -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">พบ <?= count($phoneReports) ?> รายการที่แจ้งด้วยเบอร์นี้:</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($phoneReports as $pr): ?>
                        <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <div class="font-mono font-bold text-emerald-700 text-base"><?= htmlspecialchars($pr['report_number']) ?></div>
                                <div class="text-xs text-slate-600 mt-0.5">🏷️ <?= htmlspecialchars($pr['waste_type_name']) ?> • 📍 <?= htmlspecialchars($pr['address']) ?></div>
                                <div class="text-xs text-slate-400 mt-1">วันที่แจ้ง: <?= date('d/m/Y H:i', strtotime($pr['created_at'])) ?></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-slate-100 text-slate-800 text-xs font-semibold rounded-full">
                                    <?= htmlspecialchars($pr['status']) ?>
                                </span>
                                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/track?search=<?= urlencode($pr['report_number']) ?>" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl transition">
                                    ดูรายละเอียด
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($report)): ?>
            <!-- Detailed Report Status View -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden space-y-6 p-6 sm:p-8">

                <!-- Header Info -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">เลขที่รายการ</div>
                        <h2 class="text-2xl sm:text-3xl font-mono font-bold text-slate-900 mt-1">
                            <?= htmlspecialchars($report['report_number']) ?>
                        </h2>
                        <div class="text-xs text-slate-500 mt-1">
                            แจ้งเมื่อ: <?= date('d/m/Y H:i น.', strtotime($report['submitted_at'] ?? $report['created_at'])) ?>
                        </div>
                    </div>

                    <div>
                        <?php
                        $statusBadgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                        if ($report['status'] === 'จัดเก็บเรียบร้อยแล้ว') {
                            $statusBadgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-300';
                        } elseif (in_array($report['status'], ['รับงานแล้ว', 'กำลังเดินทาง', 'กำลังดำเนินการ'])) {
                            $statusBadgeClass = 'bg-blue-50 text-blue-800 border-blue-200';
                        } elseif ($report['status'] === 'ยกเลิก') {
                            $statusBadgeClass = 'bg-rose-50 text-rose-800 border-rose-200';
                        }
                        ?>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-sm font-bold border <?= $statusBadgeClass ?>">
                            <span class="w-2.5 h-2.5 rounded-full <?= $report['status'] === 'จัดเก็บเรียบร้อยแล้ว' ? 'bg-emerald-500' : ($report['status'] === 'ยกเลิก' ? 'bg-rose-500' : 'bg-amber-500') ?>"></span>
                            <?= htmlspecialchars($report['status']) ?>
                        </span>
                    </div>
                </div>

                <!-- Compact Sleek Workflow Stepper -->
                <?php
                $steps = [
                    'รอรับเรื่อง' => ['label' => 'แจ้งเรื่อง', 'desc' => 'รับข้อมูลเข้าระบบ'],
                    'กำลังตรวจสอบ' => ['label' => 'ตรวจสอบ', 'desc' => 'เจ้าหน้าที่รับเรื่อง'],
                    'กำลังดำเนินการ' => ['label' => 'เข้าจัดเก็บ', 'desc' => 'กำลังดำเนินการ'],
                    'จัดเก็บเรียบร้อยแล้ว' => ['label' => 'เสร็จสิ้น', 'desc' => 'จัดเก็บเรียบร้อย']
                ];
                
                $currentIdx = 0;
                if ($report['status'] === 'กำลังตรวจสอบ') $currentIdx = 1;
                elseif ($report['status'] === 'กำลังดำเนินการ') $currentIdx = 2;
                elseif ($report['status'] === 'จัดเก็บเรียบร้อยแล้ว') $currentIdx = 3;
                $isCancelled = ($report['status'] === 'ยกเลิก');
                ?>

                <div class="bg-slate-50/90 rounded-2xl p-4 sm:p-5 border border-slate-200/70">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                            <i data-lucide="git-commit" class="w-4 h-4 text-emerald-600"></i>
                            <span>ขั้นตอนการดำเนินงาน</span>
                        </div>
                        <?php if ($isCancelled): ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700">
                                รายการถูกยกเลิก
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100/90 text-emerald-800">
                                ขั้นตอนที่ <?= min($currentIdx + 1, 4) ?> จาก 4
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($isCancelled): ?>
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                            <span>รายการนี้ถูกยกเลิกการจัดเก็บ กรุณาติดต่อเทศบาลหรือยื่นเรื่องแจ้งใหม่</span>
                        </div>
                    <?php else: ?>
                        <!-- Minimalist Stepper Track -->
                        <div class="relative px-2 sm:px-6">
                            <!-- Track Line -->
                            <div class="absolute left-6 right-6 top-3.5 -translate-y-1/2 h-1 bg-slate-200 z-0 rounded-full"></div>
                            <div class="absolute left-6 top-3.5 -translate-y-1/2 h-1 bg-emerald-500 z-0 rounded-full transition-all duration-500" 
                                 style="width: calc(<?= ($currentIdx / 3) * 100 ?>% - 12px);"></div>

                            <!-- Steps Grid -->
                            <div class="relative z-10 grid grid-cols-4 gap-1">
                                <?php $stepI = 0; foreach ($steps as $stKey => $stepData): ?>
                                    <?php 
                                        $isDone = ($stepI < $currentIdx);
                                        $isCurrent = ($stepI === $currentIdx);
                                        $isUpcoming = ($stepI > $currentIdx);
                                    ?>
                                    <div class="flex flex-col items-center text-center">
                                        <!-- Step Circle Node -->
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300 <?= 
                                            $isDone 
                                                ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-100' 
                                                : ($isCurrent 
                                                    ? 'bg-emerald-600 text-white shadow-md ring-4 ring-emerald-100 scale-110' 
                                                    : 'bg-white border-2 border-slate-300 text-slate-400') 
                                        ?>">
                                            <?php if ($isDone): ?>
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            <?php else: ?>
                                                <span><?= $stepI + 1 ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Step Label -->
                                        <span class="text-[11px] sm:text-xs font-semibold mt-2 <?= 
                                            $isCurrent ? 'text-emerald-700 font-bold' : ($isDone ? 'text-slate-800' : 'text-slate-400') 
                                        ?>">
                                            <?= htmlspecialchars($stepData['label']) ?>
                                        </span>
                                    </div>
                                <?php $stepI++; endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-slate-400 mb-1.5">ประเภทขยะที่แจ้ง</div>
                            <?php if (!empty($report['items'])): ?>
                                <div class="flex flex-wrap gap-1.5">
                                    <?php foreach ($report['items'] as $it): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-lg text-xs font-semibold">
                                            <span>🏷️</span>
                                            <span><?= htmlspecialchars($it['waste_type_name']) ?></span>
                                            <span class="text-emerald-700 font-mono text-[11px] font-normal">(~<?= number_format($it['estimated_weight'], 1) ?> กก.)</span>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-sm font-bold text-slate-800 mt-0.5">🏷️ <?= htmlspecialchars($report['waste_type_name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">สถานที่จัดเก็บ</div>
                            <div class="text-sm text-slate-800 mt-0.5">📍 <?= htmlspecialchars($report['address']) ?></div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">รายละเอียดเพิ่มเติม</div>
                            <div class="text-sm text-slate-600 mt-0.5"><?= htmlspecialchars($report['description'] ?: 'ไม่มีข้อมูลเพิ่มเติม') ?></div>
                        </div>
                    </div>


                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-slate-400">น้ำหนักประมาณการ</div>
                                <div class="text-sm font-bold text-slate-800 mt-0.5"><?= number_format($report['estimated_weight'], 1) ?> กก.</div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">น้ำหนักจัดเก็บจริง</div>
                                <div class="text-sm font-bold text-emerald-700 mt-0.5">
                                    <?= $report['actual_weight'] !== null ? number_format($report['actual_weight'], 1) . ' กก.' : 'รอชั่งน้ำหนักจริง' ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400">หน่วยงานผู้รับผิดชอบ</div>
                            <div class="text-sm font-medium text-slate-800 mt-0.5">
                                🏢 ฝ่ายบริหารจัดการและจัดเก็บขยะ
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map View -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">พิกัดบนแผนที่</h4>
                    <div class="rounded-2xl overflow-hidden border border-slate-200 h-[260px]" id="trackLocationMap"></div>
                </div>

                <!-- Photos (Before & After) -->
                <?php if (!empty($report['images'])): ?>
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">รูปภาพประกอบการดำเนินงาน</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach ($report['images'] as $imgIndex => $img): ?>
                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold <?= $img['image_type'] === 'after' ? 'text-emerald-700' : 'text-amber-700' ?>">
                                            <?= $img['image_type'] === 'after' ? '✅ หลังจัดเก็บ (After)' : '📷 ก่อนจัดเก็บ (Before)' ?>
                                        </span>
                                        <span class="text-[10px] text-slate-400"><?= date('d/m/Y H:i', strtotime($img['created_at'])) ?></span>
                                    </div>
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($img['image_path']) ?>" target="_blank" class="block group relative rounded-xl overflow-hidden mb-2.5">
                                        <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($img['image_path']) ?>" 
                                             alt="Report Photo" 
                                             class="w-full h-48 object-cover rounded-xl border border-slate-200 group-hover:scale-105 transition duration-300">
                                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-medium gap-1">
                                            <i data-lucide="maximize-2" class="w-4 h-4"></i> ขยายดูรูปขนาดเต็ม
                                        </div>
                                    </a>
                                    <div class="flex items-center justify-end">
                                        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($img['image_path']) ?>" 
                                           download="ขยะไร้บ้าน_<?= htmlspecialchars($report['report_number']) ?>_<?= $img['image_type'] ?>_<?= $imgIndex + 1 ?>.jpg" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-slate-700 rounded-xl text-xs font-medium transition shadow-sm">
                                            <i data-lucide="download" class="w-3.5 h-3.5 text-emerald-600"></i>
                                            <span>ดาวน์โหลดรูปนี้</span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Status History Timeline -->
                <?php if (!empty($report['history'])): ?>
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">ประวัติการดำเนินงาน (Timeline)</h4>
                        <div class="space-y-4">
                            <?php foreach ($report['history'] as $h): ?>
                                <div class="flex items-start gap-4">
                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0 ring-4 ring-emerald-50"></div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">
                                            <?= htmlspecialchars($h['new_status']) ?>
                                        </div>
                                        <?php if (!empty($h['note'])): ?>
                                            <div class="text-xs text-slate-600 mt-0.5"><?= htmlspecialchars($h['note']) ?></div>
                                        <?php endif; ?>
                                        <div class="text-[10px] text-slate-400 mt-1">
                                            <?= date('d/m/Y H:i น.', strtotime($h['created_at'])) ?>
                                            <?= $h['changed_by_name'] ? " • โดย {$h['changed_by_name']}" : '' ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </div>
</div>

<?php if (!empty($report)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = <?= (float)$report['latitude'] ?>;
    const lng = <?= (float)$report['longitude'] ?>;

    const map = L.map('trackLocationMap', {
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
<?php endif; ?>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/citizen.php';
?>
