<?php ob_start(); ?>

<div class="bg-slate-50 min-h-screen pb-20">

    <!-- Schedule Hero Header with Generous Bottom Padding for Card Overlap -->
    <section class="relative bg-gradient-to-b from-emerald-900 via-emerald-800 to-teal-900 text-white overflow-hidden pt-12 pb-24 sm:pt-16 sm:pb-32">
        <!-- Ambient Decorative Glows -->
        <div class="absolute inset-0 opacity-15 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-emerald-400 blur-3xl"></div>
            <div class="absolute top-1/2 -right-24 w-96 h-96 rounded-full bg-teal-300 blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-emerald-200 text-xs sm:text-sm font-medium backdrop-blur-md">
                <i data-lucide="calendar-check" class="w-4 h-4 text-emerald-300"></i>
                <span>จัดเก็บใหญ่เป็นประจำเดือนละ 1 ครั้ง • เทศบาลนครนนทบุรี</span>
            </div>

            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                ปฏิทินและรอบวันจัดเก็บขยะประจำเดือน
            </h1>

            <p class="text-emerald-100/80 text-xs sm:text-sm md:text-base max-w-2xl mx-auto font-light leading-relaxed">
                เทศบาลนครนนทบุรีเปิดบริการจัดเก็บขยะไร้บ้านและขยะชิ้นใหญ่เป็นประจำทุกเดือน ขอความร่วมมือประชาชนแจ้งเรื่องล่วงหน้าเพื่อการวางแผนเส้นทางจัดเก็บอย่างมีประสิทธิภาพ
            </p>
        </div>
    </section>

    <!-- Main Content Area with Seamless Card Float -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 sm:-mt-20 relative z-20 space-y-12">

        <!-- Featured Next Upcoming Schedule Card -->
        <?php if (!empty($nextSchedule)): ?>
            <?php
            $schedDate = strtotime($nextSchedule['collection_date']);
            $thaiMonths = [
                1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
                5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
                9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
            ];
            $thaiDays = [
                'Sunday' => 'วันอาทิตย์', 'Monday' => 'วันจันทร์', 'Tuesday' => 'วันอังคาร',
                'Wednesday' => 'วันพุธ', 'Thursday' => 'วันพฤหัสบดี', 'Friday' => 'วันศุกร์', 'Saturday' => 'วันเสาร์'
            ];
            $dayName = $thaiDays[date('l', $schedDate)] ?? '';
            $mNum = (int)date('n', $schedDate);
            $thaiDateFormatted = "{$dayName}ที่ " . date('j', $schedDate) . " " . ($thaiMonths[$mNum] ?? '') . " " . (date('Y', $schedDate) + 543);
            $timeFormatted = date('H:i', strtotime($nextSchedule['start_time'])) . ' - ' . date('H:i', strtotime($nextSchedule['end_time'])) . ' น.';
            ?>

            <div class="bg-white rounded-3xl shadow-2xl shadow-slate-900/10 border border-slate-200/80 overflow-hidden">
                <div class="p-6 sm:p-8 lg:p-10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        
                        <!-- Left: Schedule Info & Highlight -->
                        <div class="lg:col-span-7 space-y-4">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full text-xs font-bold uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>รอบจัดเก็บถัดไปที่เปิดรับเรื่อง</span>
                            </div>

                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">
                                <?= htmlspecialchars($nextSchedule['title']) ?>
                            </h2>

                        <div class="space-y-2.5 text-sm text-slate-600 pt-1">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-400 block">วันและเวลาที่ลงพื้นที่จัดเก็บ</span>
                                    <strong class="text-slate-800 text-base"><?= $thaiDateFormatted ?></strong>
                                    <span class="text-emerald-700 font-medium text-xs ml-1.5">(<?= $timeFormatted ?>)</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-400 block">พื้นที่ให้บริการ</span>
                                    <span class="text-slate-800 font-medium"><?= htmlspecialchars($nextSchedule['area_zone']) ?></span>
                                </div>
                            </div>

                            <?php if (!empty($nextSchedule['cutoff_date'])): ?>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="clock" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 block">กำหนดปิดรับแจ้งล่วงหน้า</span>
                                        <span class="text-amber-900 font-semibold">
                                            <?= date('d/m/Y เวลา H:i น.', strtotime($nextSchedule['cutoff_date'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($nextSchedule['description'])): ?>
                            <p class="text-xs text-slate-500 bg-slate-50 p-3.5 rounded-2xl border border-slate-100 leading-relaxed">
                                💡 <strong>คำแนะนำรอบนี้:</strong> <?= htmlspecialchars($nextSchedule['description']) ?>
                            </p>
                        <?php endif; ?>

                        <div class="pt-1 flex items-center gap-2 flex-wrap">
                            <button type="button" data-modal-open="scheduleOrphanPosterModal" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 text-xs font-bold transition">
                                <i data-lucide="image" class="w-4 h-4 text-teal-600"></i>
                                <span>ดูรายการประเภทขยะกำพร้าที่รับตามประกาศ</span>
                            </button>
                        </div>
                    </div>

                    <!-- Right: Countdown Widget & Action CTA -->
                    <div class="lg:col-span-5 bg-gradient-to-br from-emerald-800 to-teal-900 text-white p-6 sm:p-8 rounded-3xl text-center space-y-6 shadow-xl relative overflow-hidden">
                        <div class="space-y-1">
                            <div class="text-xs text-emerald-200 font-medium uppercase tracking-wider">นับถอยหลังสู่วันจัดเก็บ</div>
                            <div class="text-lg font-bold text-white">เตรียมขยะให้พร้อม</div>
                        </div>

                        <!-- Live Countdown Timer -->
                        <div class="grid grid-cols-4 gap-2" id="countdownContainer" data-target="<?= date('Y-m-d H:i:s', $schedDate) ?>">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-2.5 border border-white/15">
                                <div class="text-2xl sm:text-3xl font-mono font-extrabold text-white" id="countDays">--</div>
                                <div class="text-[10px] text-emerald-200 mt-0.5">วัน</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-2.5 border border-white/15">
                                <div class="text-2xl sm:text-3xl font-mono font-extrabold text-emerald-300" id="countHours">--</div>
                                <div class="text-[10px] text-emerald-200 mt-0.5">ชั่วโมง</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-2.5 border border-white/15">
                                <div class="text-2xl sm:text-3xl font-mono font-extrabold text-teal-200" id="countMins">--</div>
                                <div class="text-[10px] text-emerald-200 mt-0.5">นาที</div>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-2.5 border border-white/15">
                                <div class="text-2xl sm:text-3xl font-mono font-extrabold text-white" id="countSecs">--</div>
                                <div class="text-[10px] text-emerald-200 mt-0.5">วินาที</div>
                            </div>
                        </div>

                        <div class="space-y-2.5 pt-2">
                            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="w-full py-3.5 px-6 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl transition duration-200 flex items-center justify-center gap-2 shadow-lg shadow-emerald-950/30 hover:scale-[1.02]">
                                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                                <span>แจ้งจัดเก็บสำหรับรอบนี้</span>
                            </a>
                            <div class="text-[11px] text-emerald-200/80">
                                มีรายการแจ้งแล้วในรอบนี้: <strong class="text-white"><?= number_format($nextSchedule['reports_count'] ?? 0) ?></strong> รายการ (~<?= number_format($nextSchedule['total_weight'] ?? 0, 1) ?> กก.)
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Timeline of All Monthly Schedules -->
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div>
                <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">Monthly Schedule Timeline</div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">รอบการจัดเก็บขยะประจำเดือน</h2>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">ตารางรอบการลงพื้นที่จัดเก็บขยะชิ้นใหญ่และขยะตกค้างในเขตเทศบาลนครนนทบุรี</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-full font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> เปิดรับเรื่อง
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 border border-blue-200 text-blue-800 rounded-full font-medium">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> รอบถัดไป
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 border border-slate-200 text-slate-600 rounded-full font-medium">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span> เสร็จสิ้นแล้ว
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($allSchedules)): ?>
                <?php foreach ($allSchedules as $sched): ?>
                    <?php
                    $sDate = strtotime($sched['collection_date']);
                    $mIdx = (int)date('n', $sDate);
                    $mTitle = ($thaiMonths[$mIdx] ?? '') . ' ' . (date('Y', $sDate) + 543);
                    $isPast = ($sDate < strtotime('today'));
                    $statusBadge = [
                        'active' => ['class' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'label' => '🟢 เปิดรับแจ้งอยู่'],
                        'upcoming' => ['class' => 'bg-blue-50 text-blue-800 border-blue-200', 'label' => '🔵 เตรียมเปิดรับ'],
                        'collecting' => ['class' => 'bg-amber-50 text-amber-800 border-amber-200', 'label' => '🟡 กำลังจัดเก็บวันนี้'],
                        'completed' => ['class' => 'bg-slate-100 text-slate-600 border-slate-200', 'label' => '⚪ จัดเก็บเสร็จสิ้น'],
                        'cancelled' => ['class' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => '🔴 ยกเลิก']
                    ][$sched['status']] ?? ['class' => 'bg-slate-100 text-slate-600 border-slate-200', 'label' => $sched['status']];
                    ?>

                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 hover:shadow-md transition duration-200 flex flex-col justify-between space-y-4 relative group">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $statusBadge['class'] ?>">
                                    <?= $statusBadge['label'] ?>
                                </span>
                                <span class="text-xs font-mono text-slate-400">
                                    <?= date('d/m/Y', $sDate) ?>
                                </span>
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900 text-base group-hover:text-emerald-700 transition">
                                    <?= htmlspecialchars($sched['title']) ?>
                                </h3>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>เวลา <?= date('H:i', strtotime($sched['start_time'])) ?> - <?= date('H:i', strtotime($sched['end_time'])) ?> น.</span>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                <?= htmlspecialchars($sched['description'] ?: 'จัดเก็บครอบคลุมทุกตำบล/ชุมชนในเขตเทศบาลนครนนทบุรี') ?>
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                            <div class="text-slate-500">
                                แจ้งแล้ว: <strong class="text-slate-800"><?= number_format($sched['reports_count'] ?? 0) ?></strong> รายการ
                            </div>
                            <?php if ($sched['status'] === 'active' || $sched['status'] === 'upcoming'): ?>
                                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="text-emerald-700 font-bold hover:underline inline-flex items-center gap-1">
                                    <span>แจ้งเรื่อง</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-slate-400 font-medium">ปิดรับเรื่องแล้ว</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 bg-white rounded-3xl p-12 text-center text-slate-500 border border-slate-200">
                    <i data-lucide="calendar-x" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                    <p>ยังไม่มีข้อมูลรอบการจัดเก็บในระบบ</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Instructions & Best Practices Guide for Citizens -->
    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-6 sm:p-10 border border-emerald-200/80 space-y-6">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider">ข้อควรรู้และวิธีปฏิบัติ</span>
            <h3 class="text-2xl font-bold text-slate-900">ขั้นตอนการเตรียมขยะสำหรับรอบเก็บประจำเดือน</h3>
            <p class="text-xs sm:text-sm text-slate-600">ร่วมมือกันปฏิบัติตามแนวทางเพื่อให้การจัดเก็บเป็นระเบียบ รวดเร็ว และปลอดภัย</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2">
            <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-xs space-y-2.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg">
                    1
                </div>
                <h4 class="font-bold text-slate-900 text-sm">แจ้งเรื่องล่วงหน้าผ่านเว็บไซต์</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    ปักหมุดระบุตำแหน่งและประเภทขยะล่วงหน้า ก่อนถึงวันปิดรับแจ้ง เพื่อให้เจ้าหน้าที่วางแผนเส้นทางเดินรถได้อย่างครบถ้วน
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-xs space-y-2.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg">
                    2
                </div>
                <h4 class="font-bold text-slate-900 text-sm">นำขยะมาวางก่อนเวลา 08:30 น.</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    ในวันจัดเก็บ ให้นำขยะชิ้นใหญ่มาวางไว้บริเวณหน้าบ้านหรือจุดที่รถบรรทุกเข้าถึงได้สะดวก โดยไม่กีดขวางทางสัญจร
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-xs space-y-2.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg">
                    3
                </div>
                <h4 class="font-bold text-slate-900 text-sm">แยกประเภทขยะอันตราย</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    หากมีขยะอันตราย เช่น หลอดไฟ แบตเตอรี่ หรือสารเคมี กรุณาแยกบรรจุในถุงหรือกล่องต่างหากเพื่อความปลอดภัยของทีมงาน
                </p>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Countdown Timer Script -->
<script <?= \App\Core\CSP::nonceAttr() ?>>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('countdownContainer');
    if (!container) return;

    const targetDateStr = container.getAttribute('data-target');
    const targetDate = new Date(targetDateStr).getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance < 0) {
            document.getElementById('countDays').innerText = '0';
            document.getElementById('countHours').innerText = '0';
            document.getElementById('countMins').innerText = '0';
            document.getElementById('countSecs').innerText = '0';
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('countDays').innerText = days;
        document.getElementById('countHours').innerText = hours;
        document.getElementById('countMins').innerText = minutes;
        document.getElementById('countSecs').innerText = seconds;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>

<!-- Modal: Poster Infographic for Schedule -->
<div id="scheduleOrphanPosterModal" class="modal-backdrop-auto fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 hidden">
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
            <button type="button" data-modal-close="scheduleOrphanPosterModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-2 bg-slate-100 flex items-center justify-center max-h-[70vh]">
            <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/orphan_waste_guide.jpg" alt="ประกาศขยะกำพร้า เทศบาลนครนนทบุรี" class="w-full h-auto rounded-xl shadow-xs">
        </div>
        <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">สำนักการสาธารณสุขและสิ่งแวดล้อม</span>
            <button type="button" data-modal-close="scheduleOrphanPosterModal" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>
</div>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/citizen.php';
?>
