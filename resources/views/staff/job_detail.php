<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Top Action Nav -->
    <div class="flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/dashboard" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700 transition">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div class="text-center">
            <div class="text-xs text-slate-400">เลขที่รายการ</div>
            <div class="font-mono font-bold text-slate-900 text-base"><?= htmlspecialchars($report['report_number']) ?></div>
        </div>
        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 text-xs font-bold rounded-lg border border-emerald-200">
            <?= htmlspecialchars($report['status']) ?>
        </span>
    </div>

    <!-- Quick Communication & GPS Navigation Bar -->
    <div class="grid grid-cols-2 gap-3">
        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" target="_blank" class="p-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs flex flex-col items-center justify-center gap-1.5 shadow-md shadow-emerald-600/20 text-center">
            <i data-lucide="navigation" class="w-6 h-6"></i>
            <span>นำทางด้วย GPS</span>
        </a>

        <a href="tel:<?= preg_replace('/[^0-9]/', '', $report['reporter_phone']) ?>" class="p-4 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-xs flex flex-col items-center justify-center gap-1.5 shadow-md shadow-slate-900/20 text-center">
            <i data-lucide="phone-call" class="w-6 h-6 text-emerald-400"></i>
            <span>โทรหาผู้แจ้ง</span>
        </a>
    </div>

    <!-- Location & Details Card -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-4">
        <h3 class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
            <span>ตำแหน่งและรายละเอียดขยะ</span>
        </h3>

        <!-- Map -->
        <div class="rounded-xl overflow-hidden border border-slate-200 h-[220px]" id="staffJobMap"></div>

        <div class="space-y-3 text-xs">
            <div>
                <span class="text-slate-400">สถานที่:</span>
                <div class="font-semibold text-slate-800 mt-0.5"><?= htmlspecialchars($report['address']) ?></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-slate-400">ประเภทขยะ:</span>
                    <div class="font-bold text-emerald-700 mt-0.5">🏷️ <?= htmlspecialchars($report['waste_type_name']) ?></div>
                </div>
                <div>
                    <span class="text-slate-400">น้ำหนักประมาณ:</span>
                    <div class="font-bold text-slate-800 mt-0.5"><?= number_format($report['estimated_weight'], 1) ?> กก.</div>
                </div>
            </div>
            <div>
                <span class="text-slate-400">ผู้แจ้ง:</span>
                <div class="font-medium text-slate-800 mt-0.5"><?= htmlspecialchars($report['reporter_name']) ?> (<?= htmlspecialchars($report['reporter_phone']) ?>)</div>
            </div>
            <?php if (!empty($report['description'])): ?>
                <div>
                    <span class="text-slate-400">รายละเอียดเพิ่มเติม:</span>
                    <div class="text-slate-700 mt-0.5 bg-slate-50 p-2.5 rounded-lg border border-slate-100"><?= htmlspecialchars($report['description']) ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Before Photos Preview -->
    <?php if (!empty($report['images'])): ?>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-3">
            <h3 class="font-bold text-slate-900 text-sm">รูปภาพจุดทิ้งขยะ</h3>
            <div class="grid grid-cols-2 gap-3">
                <?php foreach ($report['images'] as $img): ?>
                    <div class="space-y-1">
                        <div class="text-[10px] font-bold text-slate-500"><?= $img['image_type'] === 'after' ? 'หลังจัดเก็บ' : 'ก่อนจัดเก็บ' ?></div>
                        <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($img['image_path']) ?>" class="w-full h-32 object-cover rounded-xl border border-slate-200">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Field Operation Workflow Updates -->
    <?php if ($report['status'] !== 'จัดเก็บเรียบร้อยแล้ว' && $report['status'] !== 'ยกเลิก'): ?>
        
        <!-- STEP 1-3: Progress Status Updates -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-4">
            <h3 class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-100 flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4 text-emerald-600"></i>
                <span>อัปเดตขั้นตอนการทำงาน</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <!-- Accept Job -->
                <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/jobs/<?= $report['id'] ?>/status" method="POST">
                    <?= \App\Core\CSRF::field() ?>
                    <input type="hidden" name="status" value="รับงานแล้ว">
                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 <?= $report['status'] === 'รับงานแล้ว' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>1. รับงานแล้ว</span>
                    </button>
                </form>

                <!-- En Route -->
                <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/jobs/<?= $report['id'] ?>/status" method="POST">
                    <?= \App\Core\CSRF::field() ?>
                    <input type="hidden" name="status" value="กำลังเดินทาง">
                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 <?= $report['status'] === 'กำลังเดินทาง' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                        <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                        <span>2. กำลังเดินทาง</span>
                    </button>
                </form>

                <!-- In Progress -->
                <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/jobs/<?= $report['id'] ?>/status" method="POST">
                    <?= \App\Core\CSRF::field() ?>
                    <input type="hidden" name="status" value="กำลังดำเนินการ">
                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 <?= $report['status'] === 'กำลังดำเนินการ' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                        <i data-lucide="play" class="w-3.5 h-3.5"></i>
                        <span>3. กำลังดำเนินการ</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- STEP 4: Complete Job & Record Weight Form -->
        <div class="bg-gradient-to-br from-white to-emerald-50/50 p-6 rounded-3xl shadow-md border-2 border-emerald-500/30 space-y-4">
            <h3 class="font-bold text-emerald-950 text-base pb-2 border-b border-emerald-200 flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                <span>บันทึกผลการจัดเก็บและปิดงาน (Complete Job)</span>
            </h3>

            <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/jobs/<?= $report['id'] ?>/complete" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= \App\Core\CSRF::field() ?>

                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        น้ำหนักขยะที่จัดเก็บได้จริง (กิโลกรัม) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.1" min="0.1" name="actual_weight" required placeholder="เช่น 25.5"
                               class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs text-slate-400 font-bold">กก.</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        ถ่ายรูปภาพหลังจัดเก็บ (After Photo)
                    </label>
                    <input type="file" name="after_image" accept="image/*"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        หมายเหตุการปฏิบัติงาน
                    </label>
                    <textarea name="note" rows="2" placeholder="เช่น จัดเก็บและทำความสะอาดพื้นที่เรียบร้อย นำส่งโรงคัดแยกขยะเขต"
                              class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-5 h-5"></i>
                    <span>ยืนยันการจัดเก็บเสร็จสิ้นและปิดงาน</span>
                </button>
            </form>
        </div>

    <?php else: ?>
        <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-200 text-center space-y-2">
            <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto font-bold text-xl">
                ✓
            </div>
            <h4 class="font-bold text-emerald-900 text-base">งานนี้จัดเก็บเรียบร้อยแล้ว</h4>
            <p class="text-xs text-emerald-700 font-mono">น้ำหนักจริง: <?= number_format($report['actual_weight'], 1) ?> กก.</p>
        </div>
    <?php endif; ?>

</div>

<!-- Leaflet Map Script -->
<script <?= \App\Core\CSP::nonceAttr() ?>>
document.addEventListener('DOMContentLoaded', function() {
    const lat = <?= (float)$report['latitude'] ?>;
    const lng = <?= (float)$report['longitude'] ?>;

    const map = L.map('staffJobMap', {
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
require BASE_PATH . '/resources/views/layouts/staff.php';
?>
