<?php ob_start(); ?>

<div class="py-8 lg:py-12 bg-slate-50 min-h-[85vh] flex items-center justify-center">
    <div class="max-w-xl w-full mx-auto px-4 sm:px-6 space-y-5">

        <!-- Main Ticket / Receipt Card -->
        <div class="bg-white rounded-3xl shadow-lg border border-slate-200/80 p-6 sm:p-8 relative overflow-hidden" id="ticketSlipCard">
            <!-- Top Gradient Strip -->
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

            <!-- Card Header: Branding & Status -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/nonthaburi-logo.png" alt="เทศบาลนครนนทบุรี" class="w-8 h-8 object-contain bg-white rounded-full p-0.5 border border-slate-200">
                    <div>
                        <span class="font-bold text-slate-800 text-xs sm:text-sm block leading-tight">ระบบแจ้งจัดเก็บขยะไร้บ้าน</span>
                        <span class="text-[11px] text-emerald-700 font-medium">เทศบาลนครนนทบุรี</span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full text-[11px] font-bold">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>ส่งเรื่องสำเร็จ</span>
                </span>
            </div>

            <!-- Ticket Core Body -->
            <div class="py-5 space-y-4">
                <!-- Report Number Box -->
                <div class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100 flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-semibold text-emerald-800/80 uppercase tracking-wider">เลขที่รายการแจ้ง</div>
                        <div class="text-2xl sm:text-3xl font-mono font-extrabold text-emerald-700 tracking-tight" id="reportNumberText">
                            <?= htmlspecialchars($report['report_number']) ?>
                        </div>
                    </div>
                    <button type="button" id="copyReportBtn" class="px-3 py-2 bg-white hover:bg-emerald-100 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-800 transition flex items-center gap-1.5 shadow-sm active:scale-95">
                        <i data-lucide="copy" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span id="copyBtnText">คัดลอก</span>
                    </button>
                </div>

                <!-- Key Details Grid -->
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[11px]">ประเภทขยะ</span>
                        <span class="font-bold text-slate-800 block mt-0.5 truncate" title="<?= htmlspecialchars($report['waste_type_name'] ?? 'ทั่วไป') ?>">
                            🏷️ <?= htmlspecialchars($report['waste_type_name'] ?? 'ทั่วไป') ?>
                        </span>
                        <?php if (!empty($report['estimated_weight'])): ?>
                            <span class="text-emerald-700 text-[10px] font-medium">(~<?= number_format($report['estimated_weight'], 1) ?> กก.)</span>
                        <?php endif; ?>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[11px]">สถานะเริ่มต้น</span>
                        <span class="font-bold text-amber-700 block mt-0.5">
                            ⏳ <?= htmlspecialchars($report['status']) ?>
                        </span>
                        <span class="text-[10px] text-slate-400">รอเจ้าหน้าที่ตรวจสอบ</span>
                    </div>

                    <div class="col-span-2 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[11px]">สถานที่จัดเก็บ</span>
                        <span class="font-semibold text-slate-800 block mt-0.5 leading-relaxed text-xs">
                            📍 <?= htmlspecialchars($report['address']) ?>
                        </span>
                    </div>

                    <?php if (!empty($report['reporter_name'])): ?>
                        <div class="col-span-2 px-1 flex items-center justify-between text-[11px] text-slate-500">
                            <span>ผู้แจ้ง: <strong class="text-slate-700 font-medium"><?= htmlspecialchars($report['reporter_name']) ?></strong></span>
                            <span>โทร: <strong class="text-slate-700 font-medium"><?= htmlspecialchars($report['reporter_phone'] ?? '-') ?></strong></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tracking QR Code Banner inside ticket -->
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between gap-3">
                    <div class="text-left space-y-0.5">
                        <div class="text-xs font-bold text-slate-800 flex items-center gap-1">
                            <i data-lucide="qr-code" class="w-3.5 h-3.5 text-emerald-600"></i>
                            <span>สแกนติดตามสถานะ</span>
                        </div>
                        <p class="text-[10px] text-slate-500 leading-tight">
                            ใช้กล้องมือถือสแกนเพื่อตรวจสอบสถานะงานได้ตลอดเวลา
                        </p>
                        <div class="text-[10px] text-slate-400 font-mono pt-0.5">
                            วันที่แจ้ง: <?= date('d/m/Y H:i น.', strtotime($report['submitted_at'] ?? $report['created_at'])) ?>
                        </div>
                    </div>
                    <?php 
                        $trackingUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($baseUrl ?: '') . '/track?search=' . urlencode($report['report_number']);
                        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($trackingUrl);
                    ?>
                    <img src="<?= $qrApiUrl ?>" alt="QR Code" class="w-14 h-14 rounded-lg bg-white p-1 border border-slate-200 flex-shrink-0 shadow-sm" crossorigin="anonymous">
                </div>

                <!-- Attached Photos (if any) -->
                <?php if (!empty($report['images'])): ?>
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-left space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                            <span class="flex items-center gap-1"><i data-lucide="image" class="w-3.5 h-3.5 text-emerald-600"></i> รูปภาพที่แนบ (<?= count($report['images']) ?> รูป)</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <?php foreach ($report['images'] as $imgIdx => $img): ?>
                                <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($img['image_path']) ?>" alt="รูปขยะ" class="w-full h-24 object-cover">
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($img['image_path']) ?>" 
                                       download="ขยะ_<?= htmlspecialchars($report['report_number']) ?>_<?= $imgIdx + 1 ?>.jpg" 
                                       class="absolute bottom-1 right-1 px-2 py-1 bg-slate-900/80 hover:bg-emerald-600 text-white rounded text-[10px] flex items-center gap-1 transition">
                                        <i data-lucide="download" class="w-3 h-3"></i> โหลดรูป
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Subtle Card Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                <span>เทศบาลนครนนทบุรี</span>
                <span>ขอบคุณที่ร่วมดูแลความสะอาด</span>
            </div>
        </div>

        <!-- Integrated Clean Action Buttons -->
        <div class="space-y-2.5">
            <!-- Row 1: Main Actions (Side by Side) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/track?search=<?= urlencode($report['report_number']) ?>" 
                   class="py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-2xl transition flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20 active:scale-95 text-sm">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    <span>ตรวจสอบสถานะตอนนี้</span>
                </a>

                <button type="button" id="downloadSlipBtn" 
                        class="py-3.5 px-4 bg-white hover:bg-emerald-50 text-emerald-800 border border-emerald-200 hover:border-emerald-300 font-semibold rounded-2xl transition flex items-center justify-center gap-2 shadow-sm active:scale-95 text-sm">
                    <i data-lucide="download" class="w-4 h-4 text-emerald-600" id="downloadSlipIcon"></i>
                    <span id="downloadSlipBtnText">บันทึกรูปภาพบัตรแจ้ง</span>
                </button>
            </div>

            <!-- Row 2: Secondary / Back to Home -->
            <div class="text-center pt-1">
                <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" 
                   class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 font-medium py-1.5 px-3 rounded-lg hover:bg-slate-200/60 transition">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>กลับสู่หน้าหลัก</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script <?= \App\Core\CSP::nonceAttr() ?>>
function copyReportNumber() {
    const text = document.getElementById('reportNumberText').innerText.trim();
    navigator.clipboard.writeText(text).then(() => {
        document.getElementById('copyBtnText').innerText = 'คัดลอกแล้ว!';
        setTimeout(() => {
            document.getElementById('copyBtnText').innerText = 'คัดลอก';
        }, 2500);
    });
}
document.getElementById('copyReportBtn')?.addEventListener('click', copyReportNumber);

function downloadSlipImage() {
    const slipElement = document.getElementById('ticketSlipCard');
    const btn = document.getElementById('downloadSlipBtn');
    const btnText = document.getElementById('downloadSlipBtnText');
    const originalText = btnText.innerText;

    btnText.innerText = 'กำลังสร้างรูปภาพ...';
    btn.disabled = true;
    btn.classList.add('opacity-75');

    const reportNum = "<?= htmlspecialchars($report['report_number']) ?>";
    const filename = "บัตรแจ้งขยะไร้บ้าน_" + reportNum + ".png";

    if (typeof html2canvas !== 'undefined') {
        html2canvas(slipElement, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = filename;
            link.href = canvas.toDataURL('image/png');
            link.click();

            btnText.innerText = '✅ บันทึกรูปภาพสำเร็จ!';
            btn.classList.remove('opacity-75');
            setTimeout(() => {
                btnText.innerText = originalText;
                btn.disabled = false;
            }, 3000);
        }).catch(err => {
            console.error('html2canvas error:', err);
            fallbackCanvasDownload(reportNum, filename, btn, btnText, originalText);
        });
    } else {
        fallbackCanvasDownload(reportNum, filename, btn, btnText, originalText);
    }
}

// Fallback high-resolution canvas generator if html2canvas is not available
function fallbackCanvasDownload(reportNum, filename, btn, btnText, originalText) {
    const canvas = document.createElement('canvas');
    canvas.width = 800;
    canvas.height = 1000;
    const ctx = canvas.getContext('2d');

    // Background gradient
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Header bar
    const grad = ctx.createLinearGradient(0, 0, canvas.width, 0);
    grad.addColorStop(0, '#059669');
    grad.addColorStop(1, '#0d9488');
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, canvas.width, 120);

    // Header text
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 30px Kanit, sans-serif';
    ctx.fillText('ระบบแจ้งจัดเก็บขยะไร้บ้าน', 40, 55);
    ctx.font = '20px Kanit, sans-serif';
    ctx.fillText('เทศบาลนครนนทบุรี', 40, 90);

    // Card Box
    ctx.fillStyle = '#f8fafc';
    ctx.strokeStyle = '#e2e8f0';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.roundRect(40, 160, 720, 740, 20);
    ctx.fill();
    ctx.stroke();

    // Success text
    ctx.fillStyle = '#059669';
    ctx.font = 'bold 24px Kanit, sans-serif';
    ctx.fillText('✓ ได้รับเรื่องแจ้งของท่านแล้ว', 60, 210);

    // Report number
    ctx.fillStyle = '#64748b';
    ctx.font = '16px Kanit, sans-serif';
    ctx.fillText('เลขที่รายการแจ้งจัดเก็บ:', 60, 260);
    ctx.fillStyle = '#047857';
    ctx.font = 'bold 38px monospace';
    ctx.fillText(reportNum, 60, 310);

    // Details
    ctx.fillStyle = '#334155';
    ctx.font = '18px Kanit, sans-serif';
    ctx.fillText('วันที่แจ้ง: <?= date('d/m/Y H:i น.', strtotime($report['submitted_at'] ?? $report['created_at'])) ?>', 60, 360);
    ctx.fillText('ประเภทขยะ: <?= addslashes(htmlspecialchars($report['waste_type_name'] ?? 'ทั่วไป')) ?>', 60, 400);
    ctx.fillText('สถานะเริ่มต้น: <?= addslashes(htmlspecialchars($report['status'])) ?>', 60, 440);

    // Address wrapped
    ctx.fillText('สถานที่: <?= addslashes(htmlspecialchars(mb_substr($report['address'], 0, 45))) ?>', 60, 480);
    <?php if (mb_strlen($report['address']) > 45): ?>
        ctx.fillText('<?= addslashes(htmlspecialchars(mb_substr($report['address'], 45, 45))) ?>', 125, 510);
    <?php endif; ?>

    // Footer info
    ctx.fillStyle = '#94a3b8';
    ctx.font = '16px Kanit, sans-serif';
    ctx.fillText('เทศบาลนครนนทบุรี • ร่วมสร้างเมืองสะอาด ปลอดขยะตกค้าง', 40, 950);

    const link = document.createElement('a');
    link.download = filename;
    link.href = canvas.toDataURL('image/png');
    link.click();

    btnText.innerText = '✅ บันทึกรูปภาพสำเร็จ!';
    btn.classList.remove('opacity-75');
    setTimeout(() => {
        btnText.innerText = originalText;
        btn.disabled = false;
    }, 3000);
}
</script>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/citizen.php';
?>
