<?php ob_start(); ?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-emerald-900 via-emerald-800 to-teal-900 text-white overflow-hidden py-16 lg:py-24">
    <!-- Background subtle shapes -->
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-emerald-400 blur-3xl"></div>
        <div class="absolute top-1/2 -right-24 w-96 h-96 rounded-full bg-teal-300 blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Hero Content -->
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-xs sm:text-sm font-medium backdrop-blur-md">
                    <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/nonthaburi-logo.png" alt="เทศบาลนครนนทบุรี" class="w-5 h-5 object-contain bg-white rounded-full p-0.5">
                    <span>เทศบาลนครนนทบุรี</span>
                </div>


                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-tight">
                    ขยะไร้บ้าน <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 via-teal-200 to-white">
                        เทศบาลนครนนทบุรี
                    </span>
                </h1>

                <p class="text-lg text-emerald-100/90 max-w-xl font-light leading-relaxed">
                    ช่วยกันดูแลความสะอาดในเมือง ปักหมุดจุดที่มีขยะตกค้าง ขยะชิ้นใหญ่ หรือขยะเน่าเสีย เจ้าหน้าที่จะได้รับมอบหมายงานและเข้าจัดเก็บอย่างเป็นระบบ พร้อมติดตามสถานะได้แบบเรียลไทม์
                </p>

                <!-- Action CTA Group -->
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-2xl transition duration-200 shadow-xl shadow-emerald-950/30 hover:scale-[1.02] border border-emerald-300/30">
                        <i data-lucide="plus-circle" class="w-6 h-6 text-white"></i>
                        <span>แจ้งจุดจัดเก็บขยะ</span>
                    </a>
                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/track" class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-medium rounded-2xl transition duration-200 backdrop-blur-md">
                        <i data-lucide="search" class="w-5 h-5 text-white"></i>
                        <span>ติดตามสถานะงาน</span>
                    </a>
                </div>


                <!-- Quick highlights -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-emerald-700/50">
                    <div>
                        <div class="text-2xl lg:text-3xl font-bold text-white"><?= number_format($metrics['total'] ?? 0) ?></div>
                        <div class="text-xs text-white">รายการแจ้งทั้งหมด</div>
                    </div>
                    <div>
                        <div class="text-2xl lg:text-3xl font-bold text-white"><?= number_format($metrics['completed'] ?? 0) ?></div>
                        <div class="text-xs text-white">จัดเก็บสำเร็จแล้ว</div>
                    </div>
                    <div>
                        <div class="text-2xl lg:text-3xl font-bold text-white"><?= number_format($metrics['actual_weight_total'] ?? 0, 1) ?> <span class="text-sm font-normal text-white">กก.</span></div>
                        <div class="text-xs text-white">น้ำหนักจัดเก็บจริง</div>
                    </div>
                </div>
            </div>

            <!-- Right Hero Card: Quick Search & Tracking -->
            <div class="lg:col-span-5">
                <div class="bg-white/95 backdrop-blur-md p-6 sm:p-8 rounded-3xl shadow-2xl border border-white/20 text-slate-800">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">ตรวจสอบสถานะรายการแจ้ง</h3>
                            <p class="text-xs text-slate-500">กรอกเลขที่รายการ เช่น WB-2026-000001 หรือเบอร์โทร</p>
                        </div>
                    </div>

                    <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/track" method="GET" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">ค้นหาด้วยเลขที่รายการ หรือ เบอร์โทรศัพท์</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="search" class="w-5 h-5"></i>
                                </div>
                                <input type="text" name="search" required placeholder="เช่น WB-2026-000001 หรือ 0812345678" 
                                       class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-sm">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-lg shadow-slate-900/10">
                            <i data-lucide="arrow-right" class="w-4 h-4 text-emerald-400"></i>
                            <span>ตรวจสอบสถานะ</span>
                        </button>
                    </form>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>ยังไม่ได้แจ้งขยะ?</span>
                        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="text-emerald-700 font-semibold hover:underline flex items-center gap-1">
                            <span>กรอกแบบฟอร์มแจ้งเรื่อง</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Monthly Collection Schedule Announcement Section -->
<?php if (!empty($nextSchedule)): ?>
    <?php
    $schedTime = strtotime($nextSchedule['collection_date']);
    $thaiMonths = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    $thaiDays = [
        'Sunday' => 'วันอาทิตย์', 'Monday' => 'วันจันทร์', 'Tuesday' => 'วันอังคาร',
        'Wednesday' => 'วันพุธ', 'Thursday' => 'วันพฤหัสบดี', 'Friday' => 'วันศุกร์', 'Saturday' => 'วันเสาร์'
    ];
    $dName = $thaiDays[date('l', $schedTime)] ?? '';
    $mNum = (int)date('n', $schedTime);
    $dateLabel = "{$dName}ที่ " . date('j', $schedTime) . " " . ($thaiMonths[$mNum] ?? '') . " " . (date('Y', $schedTime) + 543);
    $daysLeft = (int)ceil(($schedTime - strtotime('today')) / 86400);
    ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-emerald-100 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-2.5 bg-gradient-to-b from-emerald-500 to-teal-600"></div>

            <div class="flex items-start sm:items-center gap-4 pl-2">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 shadow-inner">
                    <i data-lucide="calendar-days" class="w-6 h-6 sm:w-7 sm:h-7"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            🗓️ รอบจัดเก็บประจำเดือน (เดือนละ 1 ครั้ง)
                        </span>
                        <?php if ($daysLeft > 0): ?>
                            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-200">
                                เหลืออีก <?= $daysLeft ?> วัน
                            </span>
                        <?php elseif ($daysLeft === 0): ?>
                            <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200 animate-pulse">
                                กำลังจัดเก็บวันนี้!
                            </span>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-lg sm:text-xl text-slate-900 leading-snug">
                        <?= htmlspecialchars($nextSchedule['title']) ?>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        นัดหมายลงพื้นที่: <strong class="text-slate-800"><?= $dateLabel ?></strong> (<?= date('H:i', strtotime($nextSchedule['start_time'])) ?> - <?= date('H:i', strtotime($nextSchedule['end_time'])) ?> น.)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto flex-shrink-0">
                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/schedule" class="flex-1 md:flex-none px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    <span>ดูปฏิทินรอบจัดเก็บ</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="flex-1 md:flex-none px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5 shadow-md shadow-emerald-600/20">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>แจ้งเรื่องรอบนี้</span>
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Interactive Map Overview Section -->
<section class="py-16 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1">Live Map Overview</div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">แผนที่แสดงจุดแจ้งขยะในพื้นที่</h2>
                <p class="text-slate-500 text-sm mt-1">คลิกที่หมุดบนแผนที่เพื่อดูรายละเอียดของแต่ละจุดแจ้งจัดเก็บ</p>
            </div>
            <!-- Legend Indicators -->
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-800 rounded-full font-medium">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> รอรับเรื่อง / ตรวจสอบ
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 border border-blue-200 text-blue-800 rounded-full font-medium">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> กำลังดำเนินการ
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-full font-medium">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> จัดเก็บเรียบร้อยแล้ว
                </span>
            </div>
        </div>

        <!-- Leaflet Map Container -->
        <div class="rounded-3xl overflow-hidden shadow-xl border border-slate-200 bg-slate-100 relative z-0 h-[450px]" id="publicOverviewMap"></div>

    </div>
</section>

<!-- Workflow Steps Section -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">ขั้นตอนการดำเนินงานที่โปร่งใส</h2>
            <p class="text-slate-500 text-sm mt-2">จากจุดที่แจ้งเรื่อง สู่การจัดเก็บที่เสร็จสมบูรณ์และวัดผลได้จริง</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Step 1 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg mb-4">
                    1
                </div>
                <h3 class="font-bold text-slate-900 mb-2">1. แจ้งเรื่องและปักหมุด</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    ประชาชนกรอกรายละเอียดสถานที่ ปักหมุดบนแผนที่ และแนบรูปถ่ายขยะตกค้าง
                </p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-lg mb-4">
                    2
                </div>
                <h3 class="font-bold text-slate-900 mb-2">2. เจ้าหน้าที่รับเรื่อง</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    ระบบแจ้งเตือนเจ้าหน้าที่ทันทีเพื่อตรวจสอบพิกัดและความถูกต้องของจุดแจ้งขยะ
                </p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-lg mb-4">
                    3
                </div>
                <h3 class="font-bold text-slate-900 mb-2">3. ลงพื้นที่จัดเก็บ</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    เดินทางตามพิกัด GPS เข้าจัดเก็บ และตรวจวัดน้ำหนักขยะจริง
                </p>
            </div>

            <!-- Step 4 -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg mb-4">
                    4
                </div>
                <h3 class="font-bold text-slate-900 mb-2">4. ยืนยันผลและปิดงาน</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    อัปโหลดรูปภาพหลังจัดเก็บ (After) ปิดงานในระบบ ประชาชนติดตามผลงานได้ทันที
                </p>
            </div>
        </div>
</section>

<!-- Nonthaburi Orphan Waste Showcase Section -->
<section class="py-16 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-xs font-bold uppercase tracking-wider mb-2">
                    <span>เทศบาลนครนนทบุรี • สำนักการสาธารณสุขและสิ่งแวดล้อม</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    ขยะกำพร้า (ขยะที่รีไซเคิลไม่ได้ ซาเล้งไม่รับ)
                </h2>
                <p class="text-slate-500 text-xs sm:text-sm mt-1 max-w-2xl">
                    ร่วมเป็นส่วนหนึ่งในการเปลี่ยนขยะกำพร้าสู่พลังงานทดแทน (RDF) เพื่อลดปริมาณขยะฝังกลบอย่างยั่งยืน
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" onclick="document.getElementById('homeOrphanPosterModal').classList.remove('hidden')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-2 border border-slate-200">
                    <i data-lucide="image" class="w-4 h-4 text-teal-600"></i>
                    <span>ดูโปสเตอร์ฉบับเต็ม</span>
                </button>
                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-sm shadow-teal-600/20">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>แจ้งขยะกำพร้า</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left: 3 Steps Preparation -->
            <div class="lg:col-span-4 bg-gradient-to-br from-teal-800 to-emerald-900 text-white p-6 sm:p-8 rounded-3xl space-y-6 shadow-xl relative overflow-hidden">
                <div class="space-y-1">
                    <span class="text-xs text-teal-200 uppercase font-semibold tracking-wider">วิธีเตรียมก่อนส่ง</span>
                    <h3 class="text-xl font-bold text-white">3 ขั้นตอนง่ายๆ ในการจัดเก็บ</h3>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-400 text-teal-950 flex items-center justify-center font-bold text-base flex-shrink-0">
                            1
                        </div>
                        <div>
                            <strong class="text-sm font-bold text-white block">ล้าง (พอสะอาด)</strong>
                            <p class="text-teal-100/80 text-[11px] mt-0.5 leading-relaxed">
                                ชะล้างคราบอาหาร ซอส หรือน้ำมันตกค้างในกล่องโฟม ชามเมลามีน หรือซองเครื่องปรุง
                            </p>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-300 text-teal-950 flex items-center justify-center font-bold text-base flex-shrink-0">
                            2
                        </div>
                        <div>
                            <strong class="text-sm font-bold text-white block">ผึ่ง (ให้แห้งสนิท)</strong>
                            <p class="text-teal-100/80 text-[11px] mt-0.5 leading-relaxed">
                                ผึ่งลมหรือตากแดดให้แห้งสนิท เพื่อไม่ให้เกิดเชื้อรา กลิ่นอับ หรือน้ำเน่าเสีย
                            </p>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-400 text-emerald-950 flex items-center justify-center font-bold text-base flex-shrink-0">
                            3
                        </div>
                        <div>
                            <strong class="text-sm font-bold text-white block">เก็บ (รวบรวม)</strong>
                            <p class="text-teal-100/80 text-[11px] mt-0.5 leading-relaxed">
                                บรรจุใส่ถุงหรือกล่องอย่างเป็นระเบียบ ทำลายของมีคมก่อนรวม และแจ้งผ่านระบบ
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Accepted vs Not Accepted Split -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Accepted Grid -->
                <div class="bg-emerald-50/70 p-6 rounded-3xl border border-emerald-200/80 space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                            ✓
                        </div>
                        <h4 class="font-bold text-slate-900 text-base">ประเภทขยะกำพร้าที่รับ (12 หมวดหมู่)</h4>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs">
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">🍽️ โฟม/เมลามีน/พลาสติก</strong>
                            <span class="text-[11px] text-slate-500">กล่องโฟม ชาม ช้อน แก้ว ขวด</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">🍿 ซองขนม/ฟอยล์</strong>
                            <span class="text-[11px] text-slate-500">ถุงกรอบ ซองฟอยล์ ซองปรุงรส</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">🚗 ยางยานพาหนะ</strong>
                            <span class="text-[11px] text-slate-500">ยางรถยนต์ มอเตอร์ไซค์ จักรยาน</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">🛋️ ท่อนโซฟา/ฟองน้ำ/วิก</strong>
                            <span class="text-[11px] text-slate-500">ที่นอนเก่า ฟองน้ำ วิกผม เรซิ่น</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">🪥 ของใช้สุขอนามัย</strong>
                            <span class="text-[11px] text-slate-500">แปรงสีฟัน หลอดยา ไม้ปั่นหู</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">💳 บัตรแข็ง/ปากกา</strong>
                            <span class="text-[11px] text-slate-500">บัตร ATM บัตรพนักงาน ลบคำผิด</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">👕 สิ่งทอ/เสื้อผ้า/ซิลิโคน</strong>
                            <span class="text-[11px] text-slate-500">เสื้อผ้าเก่า ซิลิโคนเสริมอก</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">🎎 สิ่งสักการะ/ศาล</strong>
                            <span class="text-[11px] text-slate-500">ตุ๊กตานางรำ มาลัยปลอม ผ้าสามสี</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">⚽ กีฬา/แป้งโด/ของเล่น</strong>
                            <span class="text-[11px] text-slate-500">ลูกบอล ขนไก่ ดินน้ำมัน หมากฝรั่ง</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">🎞️ ฟิล์ม/รูปถ่ายเก่า</strong>
                            <span class="text-[11px] text-slate-500">ฟิล์มภาพถ่าย ฟิล์มเอกซเรย์</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">💊 ของแห้ง/ยาหมดอายุ</strong>
                            <span class="text-[11px] text-slate-500">ซองกันชื้น ยาเม็ด ของหมดอายุ</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-emerald-100 shadow-xs">
                            <strong class="text-slate-800 block mb-0.5">🩹 ชุดตรวจ (ผู้ไม่ป่วย)</strong>
                            <span class="text-[11px] text-slate-500">ATK หน้ากากอนามัย ถุงมือยาง</span>
                        </div>
                    </div>
                </div>

                <!-- Not Accepted Notice -->
                <div class="bg-rose-50/80 p-5 rounded-3xl border border-rose-200 space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-xs">
                            ✕
                        </div>
                        <h4 class="font-bold text-rose-900 text-sm">ประเภทที่ไม่รับเด็ดขาด (12 รายการ)</h4>
                    </div>
                    <div class="flex flex-wrap gap-2 text-[11px] text-rose-800 font-medium pt-1">
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">สายไฟ</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">ท่อประปา</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">สายยาง</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">หนังเทียม</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">ขวดแก้ว</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">กระเบื้อง</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">ขยะติดเชื้อ</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">สังกะสี</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">หลอดไฟ</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">อุปกรณ์อิเล็กทรอนิกส์</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">ตลับหมึกเครื่องพิมพ์</span>
                        <span class="px-2.5 py-1 bg-white rounded-lg border border-rose-200">เศษเหล็กลวด</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: Poster Infographic for Home -->
<div id="homeOrphanPosterModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 hidden" onclick="if(event.target === this) this.classList.add('hidden')">
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
            <button type="button" onclick="document.getElementById('homeOrphanPosterModal').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-2 bg-slate-100 flex items-center justify-center max-h-[70vh]">
            <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/orphan_waste_guide.jpg" alt="ประกาศขยะกำพร้า เทศบาลนครนนทบุรี" class="w-full h-auto rounded-xl shadow-xs">
        </div>
        <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">สำนักการสาธารณสุขและสิ่งแวดล้อม</span>
            <button type="button" onclick="document.getElementById('homeOrphanPosterModal').classList.add('hidden')" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>
</div>

<!-- Leaflet Map Script for Public Homepage -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('publicOverviewMap', {
        preferCanvas: true,
        zoomAnimation: true,
        fadeAnimation: true
    }).setView([13.8621, 100.5134], 12);

    setTimeout(() => map.invalidateSize(), 150);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Fetch map points from backend API
    fetch('<?= htmlspecialchars($baseUrl ?: '') ?>/api/map-points')
        .then(response => response.json())
        .then(points => {
            if (!points || points.length === 0) return;

            const markers = [];

            points.forEach(pt => {
                let markerColor = '#f59e0b'; // pending/yellow
                if (pt.status === 'จัดเก็บเรียบร้อยแล้ว') {
                    markerColor = '#10b981'; // green
                } else if (['รับงานแล้ว', 'กำลังเดินทาง', 'กำลังดำเนินการ'].includes(pt.status)) {
                    markerColor = '#3b82f6'; // blue
                } else if (pt.status === 'ยกเลิก') {
                    markerColor = '#64748b'; // gray
                }

                // Custom DivIcon marker
                const customIcon = L.divIcon({
                    className: 'custom-pin',
                    html: `<div style="background-color: ${markerColor}; width: 26px; height: 26px; border-radius: 50%; border: 3px solid white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center;">
                             <div style="background-color: white; width: 6px; height: 6px; border-radius: 50%;"></div>
                           </div>`,
                    iconSize: [26, 26],
                    iconAnchor: [13, 13]
                });

                const marker = L.marker([pt.lat, pt.lng], { icon: customIcon }).addTo(map);
                
                const popupContent = `
                    <div style="font-family: 'Kanit', sans-serif; min-width: 200px; padding: 4px;">
                        <div style="font-weight: bold; font-size: 14px; color: #0f172a; margin-bottom: 4px;">
                            ${pt.report_number}
                        </div>
                        <div style="font-size: 12px; color: #059669; font-weight: 500; margin-bottom: 4px;">
                            🏷️ ${pt.waste_type}
                        </div>
                        <div style="font-size: 11px; color: #64748b; margin-bottom: 6px;">
                            📍 ${pt.address}
                        </div>
                        <div style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: bold; background: #ecfdf5; color: #065f46; margin-bottom: 8px;">
                            สถานะ: ${pt.status}
                        </div>
                        <div style="border-top: 1px solid #e2e8f0; padding-top: 6px;">
                            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/track?search=${pt.report_number}" style="display:block; text-align:center; padding: 4px 8px; background: #059669; color: white; border-radius: 6px; font-size: 11px; text-decoration: none; font-weight: 500;">
                                ดูความคืบหน้า
                            </a>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markers.push(marker);
            });

            if (markers.length > 0) {
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.15));
            }
        })
        .catch(err => console.error('Error loading map points:', err));
});
</script>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/citizen.php';
?>
