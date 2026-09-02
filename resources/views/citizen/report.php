<?php ob_start(); ?>

<div class="bg-gradient-to-b from-emerald-900/10 via-slate-50 to-slate-50 py-10 lg:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Title -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold uppercase tracking-wider mb-2">
                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                <span>แบบฟอร์มแจ้งเรื่องสำหรับประชาชน</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-bold text-slate-900 leading-tight">แจ้งขอให้จัดเก็บขยะตกค้าง</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-2 max-w-lg mx-auto">
                กรอกรายละเอียด ปักหมุดบนแผนที่ และแนบรูปภาพเพื่อให้เจ้าหน้าที่เข้าดำเนินการได้อย่างรวดเร็วและตรงจุด
            </p>
        </div>

        <!-- Form Card Container -->
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
            <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/report" method="POST" enctype="multipart/form-data" id="wasteReportForm" class="p-5 sm:p-10 space-y-8 sm:space-y-10">
                <?= \App\Core\CSRF::field() ?>

                <!-- Anti-Bot Honeypot Trap (Hidden from real users) -->
                <div class="form-honeypot" aria-hidden="true">
                    <label for="sys_bot_trap_field">Do not fill this field</label>
                    <input type="text" name="sys_bot_trap_field" id="sys_bot_trap_field" tabindex="-1" autocomplete="off">
                </div>

                <?php if (!empty($nextSchedule)): ?>
                    <!-- Linked Monthly Collection Round Notice -->
                    <div class="p-4 bg-emerald-50/80 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-emerald-900">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0">
                                <i data-lucide="calendar-check" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="text-emerald-700 font-bold block">รอบจัดเก็บประจำเดือนที่เปิดรับ:</span>
                                <strong class="text-slate-800"><?= htmlspecialchars($nextSchedule['title']) ?></strong>
                                <span class="text-slate-500">(จัดเก็บวันที่ <?= date('d/m/Y', strtotime($nextSchedule['collection_date'])) ?> เวลา <?= date('H:i', strtotime($nextSchedule['start_time'])) ?>-<?= date('H:i', strtotime($nextSchedule['end_time'])) ?> น.)</span>
                            </div>
                        </div>
                        <input type="hidden" name="collection_schedule_id" value="<?= (int)$nextSchedule['id'] ?>">
                        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/schedule" target="_blank" class="px-3 py-1.5 bg-white border border-emerald-300 text-emerald-700 rounded-xl font-bold hover:bg-emerald-100 transition whitespace-nowrap shadow-xs">
                            ดูตารางจัดเก็บรอบอื่น
                        </a>
                    </div>
                <?php endif; ?>


                <!-- SECTION 1: ข้อมูลผู้แจ้ง -->
                <div>
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            1
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg">ข้อมูลผู้แจ้งเรื่อง</h3>
                            <p class="text-xs text-slate-500">สำหรับติดต่อสอบถามหรือแจ้งความคืบหน้ากรณีจำเป็น</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-2">
                                ชื่อ - นามสกุล <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="reporter_name" required placeholder="เช่น นายสมศักดิ์ รักสะอาด"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-2">
                                เบอร์โทรศัพท์สำหรับติดต่อ <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="reporter_phone" required placeholder="เช่น 081-234-5678"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: พิกัดและสถานที่บนแผนที่ (ความแม่นยำสูง) -->
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                2
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base sm:text-lg">ตำแหน่งและสถานที่จัดเก็บ</h3>
                                <p class="text-xs text-slate-500">ค้นหาชื่อสถานที่/ซอย, กดปุ่ม GPS, หรือลากหมุดบนแผนที่</p>
                            </div>
                        </div>

                        <button type="button" id="getCurrentLocationBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-xl text-xs font-bold transition border border-emerald-300 shadow-xs">
                            <i data-lucide="crosshair" class="w-4 h-4 text-emerald-600"></i>
                            <span>ใช้พิกัดปัจจุบัน (GPS)</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Smart Place & Street Search Box -->
                        <div class="relative">
                            <div class="relative flex items-center">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </div>
                                <input type="text" id="placeSearchInput" 
                                       placeholder="🔍 พิมพ์ค้นหา ซอย / ถนน / ชุมชน / สถานที่ เช่น ซอยติวานนท์ 24, ตลาดนนท์, วัดบัวขวัญ"
                                       autocomplete="off"
                                       class="w-full pl-10 pr-24 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition shadow-sm">
                                <button type="button" id="clearSearchBtn" class="hidden absolute inset-y-0 right-16 px-2 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                    ✕
                                </button>
                                <button type="button" id="doSearchBtn" class="absolute inset-y-1 right-1 px-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1 shadow-sm transition">
                                    <span>ค้นหา</span>
                                </button>
                            </div>

                            <!-- Search Results Dropdown -->
                            <div id="searchResultsDropdown" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-slate-200 z-50 overflow-hidden divide-y divide-slate-100 max-h-60 overflow-y-auto">
                            </div>
                        </div>

                        <!-- Quick Landmark Chips for Nonthaburi -->
                        <div>
                            <div class="text-[11px] font-semibold text-slate-500 mb-1.5 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3 text-emerald-600"></i>
                                <span>ปุ่มลัดย่านสำคัญในเขตเทศบาลนครนนทบุรี:</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" class="quick-zone-chip px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-medium transition" data-lat="13.8436" data-lng="100.4912" data-name="ท่าน้ำนนทบุรี / ตลาดนนท์">
                                    🚤 ท่าน้ำนนท์
                                </button>
                                <button type="button" class="quick-zone-chip px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-medium transition" data-lat="13.8602" data-lng="100.5135" data-name="ศูนย์ราชการจังหวัดนนทบุรี">
                                    🏛️ ศูนย์ราชการนนท์
                                </button>
                                <button type="button" class="quick-zone-chip px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-medium transition" data-lat="13.8588" data-lng="100.5218" data-name="แยกแคราย ถนนติวานนท์">
                                    🚦 แยกแคราย / ติวานนท์
                                </button>
                                <button type="button" class="quick-zone-chip px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-medium transition" data-lat="13.8596" data-lng="100.5429" data-name="เดอะมอลล์ งามวงศ์วาน / แยกพงษ์เพชร">
                                    🛍️ เดอะมอลล์งามวงศ์วาน
                                </button>
                                <button type="button" class="quick-zone-chip px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-medium transition" data-lat="13.8698" data-lng="100.4855" data-name="ถนนรัตนาธิเบศร์ / สะพานพระนั่งเกล้า">
                                    🛣️ ถ.รัตนาธิเบศร์
                                </button>
                                <button type="button" class="quick-zone-chip px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-[11px] font-medium transition" data-lat="13.8617" data-lng="100.5147" data-name="อุทยานมกุฏรมยสราญ">
                                    🌳 อุทยานมกุฏฯ
                                </button>
                            </div>
                        </div>

                        <!-- GPS Accuracy Status Box -->
                        <div id="gpsAccuracyBox" class="hidden p-3 rounded-xl text-xs font-medium border transition-all duration-200">
                        </div>

                        <!-- Leaflet Interactive Pin Picker -->
                        <div class="relative">
                            <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-inner relative z-0 h-[380px]" id="pickerMap"></div>
                            <div class="absolute bottom-2 left-2 right-2 sm:right-auto z-[500] pointer-events-none">
                                <div class="bg-slate-900/80 backdrop-blur-sm text-white px-3 py-1.5 rounded-xl text-[11px] font-medium flex items-center gap-1.5 shadow-md">
                                    <i data-lucide="hand" class="w-3.5 h-3.5 text-emerald-400"></i>
                                    <span>คลิกหรือลากหมุดเพื่อระบุตำแหน่งที่แน่นอน</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Latitude (ละติจูด)</label>
                                <input type="text" id="latitude" name="latitude" value="13.8628000" readonly
                                       class="w-full px-3.5 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-mono text-slate-700">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Longitude (ลองจิจูด)</label>
                                <input type="text" id="longitude" name="longitude" value="100.5145000" readonly
                                       class="w-full px-3.5 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-mono text-slate-700">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-slate-800">
                                    รายละเอียดสถานที่ / จุดสังเกต <span class="text-rose-500">*</span>
                                </label>
                                <span id="geoStatus" class="hidden text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 font-medium">
                                    ✨ ดึงที่อยู่อัตโนมัติเรียบร้อย
                                </span>
                            </div>
                            <textarea name="address" id="addressInput" required rows="2" placeholder="เช่น ริมถนนติวานนท์ หน้าปากซอย 14 ใกล้สะพานลอยคนข้าม ฝั่งตรงข้ามปั๊มน้ำมัน"
                                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"></textarea>
                        </div>

                    </div>
                </div>


                <!-- SECTION 3: ประเภทขยะและรายละเอียดเพิ่มเติม (เลือกได้หลายประเภท) -->
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                3
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base sm:text-lg">ประเภทขยะกำพร้าและประมาณการน้ำหนัก</h3>
                                <p class="text-xs text-slate-500">เลือกประเภทขยะที่ต้องการให้จัดเก็บตามข้อกำหนดของเทศบาลนครนนทบุรี (เลือกได้มากกว่า 1 ประเภท)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Official Orphan Waste Guide Box (ขยะกำพร้า เทศบาลนครนนทบุรี) -->
                    <div class="bg-gradient-to-br from-teal-50 via-emerald-50 to-sky-50 rounded-2xl p-5 border border-teal-200/80 space-y-4 mb-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm">
                                    <i data-lucide="info" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-800 border border-teal-300">
                                            ขยะกำพร้า (ขยะที่รีไซเคิลไม่ได้ ซาเล้งไม่รับ)
                                        </span>
                                        <span class="text-xs font-semibold text-slate-500">เทศบาลนครนนทบุรี</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-sm sm:text-base mt-0.5">
                                        ข้อกำหนดการรับและคัดแยกขยะกำพร้าก่อนแจ้งจัดเก็บ
                                    </h4>
                                </div>
                            </div>

                            <button type="button" data-modal-open="orphanWasteModal" class="px-3.5 py-2 bg-white hover:bg-teal-100/60 text-teal-800 text-xs font-bold rounded-xl border border-teal-300 transition flex items-center gap-1.5 flex-shrink-0 shadow-xs">
                                <i data-lucide="image" class="w-4 h-4 text-teal-600"></i>
                                <span>ดูโปสเตอร์ประกาศฉบับเต็ม</span>
                            </button>
                        </div>

                        <!-- 3 Quick Rules & Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
                            <!-- Rule 1: Accepted -->
                            <div class="bg-white/85 backdrop-blur-xs p-3.5 rounded-xl border border-emerald-200/80 text-xs space-y-1.5">
                                <div class="font-bold text-emerald-800 flex items-center gap-1.5">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                                    <span>✅ ประเภทที่รับ (12 หมวดหมู่ด้านล่าง)</span>
                                </div>
                                <p class="text-slate-600 text-[11px] leading-relaxed">
                                    กล่องโฟม/เมลามีน, ซองขนม/ฟอยล์, ยางรถ/จักรยาน (ตัดจุ๊บแล้ว), ท่อนโซฟา/ฟองน้ำ/วิกผม, แปรงสีฟัน/ไม้ปั่นหู, บัตรแข็ง/ปากกา, สิ่งทอ/ซิลิโคน, สิ่งสักการะ/ตุ๊กตาศาล, อุปกรณ์กีฬา/ลูกบอล/แป้งโด, ฟิล์ม/รูปถ่าย, ซองกันชื้น/ยาหมดอายุ, ATK/หน้ากาก (ผู้ไม่ป่วย)
                                </p>
                            </div>

                            <!-- Rule 2: Not Accepted -->
                            <div class="bg-white/85 backdrop-blur-xs p-3.5 rounded-xl border border-rose-200 text-xs space-y-1.5">
                                <div class="font-bold text-rose-800 flex items-center gap-1.5">
                                    <i data-lucide="x-circle" class="w-4 h-4 text-rose-600"></i>
                                    <span>❌ ประเภทที่ไม่รับเด็ดขาด (12 รายการ)</span>
                                </div>
                                <p class="text-slate-600 text-[11px] leading-relaxed">
                                    สายไฟ, ท่อประปา, สายยาง, หนังเทียม, ขวดแก้ว, กระเบื้อง, ขยะติดเชื้อ, สังกะสี, หลอดไฟ, อุปกรณ์อิเล็กทรอนิกส์, ตลับหมึกพิมพ์, เศษเหล็กลวด
                                </p>
                            </div>

                            <!-- Rule 3: 3 Steps Preparation -->
                            <div class="bg-white/85 backdrop-blur-xs p-3.5 rounded-xl border border-teal-200 text-xs space-y-1.5">
                                <div class="font-bold text-teal-800 flex items-center gap-1.5">
                                    <i data-lucide="sparkles" class="w-4 h-4 text-teal-600"></i>
                                    <span>วิธีเตรียมขยะ 3 ขั้นตอน</span>
                                </div>
                                <div class="flex items-center justify-between text-center pt-1 text-[11px]">
                                    <div class="flex-1">
                                        <span class="block text-base">🚰</span>
                                        <strong class="text-teal-900 block mt-0.5">1. ล้าง</strong>
                                        <span class="text-[10px] text-slate-500">พอสะอาด</span>
                                    </div>
                                    <div class="text-slate-300">→</div>
                                    <div class="flex-1">
                                        <span class="block text-base">🌀</span>
                                        <strong class="text-teal-900 block mt-0.5">2. ผึ่ง</strong>
                                        <span class="text-[10px] text-slate-500">ให้แห้งสนิท</span>
                                    </div>
                                    <div class="text-slate-300">→</div>
                                    <div class="flex-1">
                                        <span class="block text-base">📦</span>
                                        <strong class="text-teal-900 block mt-0.5">3. เก็บ</strong>
                                        <span class="text-[10px] text-slate-500">รวบรวมลงถุง</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Multiple Waste Type Selection Cards with Weight Input -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-semibold text-slate-800">
                                    เลือกประเภทขยะที่ต้องการส่งมอบ <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-xs text-emerald-700 font-medium">
                                    (แตะเพื่อเลือก และระบุน้ำหนักประมาณการของแต่ละประเภท)
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                                <?php foreach ($wasteTypes as $index => $type): ?>
                                    <div class="waste-type-card relative flex flex-col justify-between p-4 rounded-2xl border-2 transition-all duration-200 cursor-pointer <?= $index === 0 ? 'bg-emerald-50/60 border-emerald-500 shadow-sm' : 'bg-slate-50 border-slate-200 hover:border-emerald-300' ?>" data-type-id="<?= $type['id'] ?>">
                                        
                                        <!-- Header with Checkbox -->
                                        <div>
                                            <div class="flex items-start justify-between gap-2 mb-2">
                                                <div class="flex items-center gap-2.5">
                                                    <input type="checkbox" name="waste_types[]" value="<?= $type['id'] ?>" id="type_cb_<?= $type['id'] ?>"
                                                           <?= $index === 0 ? 'checked' : '' ?>
                                                           class="waste-type-checkbox w-4 h-4 text-emerald-600 rounded-md border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                                    <label for="type_cb_<?= $type['id'] ?>" class="font-bold text-sm text-slate-900 cursor-pointer select-none">
                                                        <?= htmlspecialchars($type['name']) ?>
                                                    </label>
                                                </div>
                                                <i data-lucide="<?= htmlspecialchars($type['icon'] ?: 'trash-2') ?>" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                                            </div>

                                            <!-- Waste Type Image Preview -->
                                            <?php if (!empty($type['image'])): ?>
                                                <div class="w-full h-32 my-2 rounded-xl bg-slate-100 border border-slate-200/80 overflow-hidden shadow-xs flex items-center justify-center">
                                                    <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/<?= htmlspecialchars($type['image']) ?>" 
                                                         alt="<?= htmlspecialchars($type['name']) ?>" 
                                                         class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                                </div>
                                            <?php endif; ?>

                                            <p class="text-[11px] text-slate-500 leading-relaxed mb-3">
                                                <?= htmlspecialchars($type['description']) ?>
                                            </p>
                                        </div>

                                        <!-- Weight input for this specific type -->
                                        <div class="pt-2 border-t border-slate-200/60 <?= $index === 0 ? '' : 'opacity-60' ?> weight-container" id="weight_container_<?= $type['id'] ?>">
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                                น้ำหนักประมาณการ:
                                            </label>
                                            <div class="relative">
                                                <input type="number" step="0.1" min="0" 
                                                       name="estimated_weights[<?= $type['id'] ?>]" 
                                                       id="weight_input_<?= $type['id'] ?>"
                                                       value="<?= $index === 0 ? '10.0' : '' ?>"
                                                       placeholder="เช่น 10.0"
                                                       class="weight-calc-input w-full pl-3 pr-10 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-[11px] text-slate-400 font-medium">กก.</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>

                        <!-- Live Summary Banner (Clean White Theme) -->
                        <div class="my-5 p-4 sm:p-5 rounded-2xl bg-white text-slate-800 shadow-sm border border-slate-200">
                            <div class="grid grid-cols-2 gap-3 divide-x divide-slate-100 items-center">
                                
                                <!-- Col 1: Selected Count -->
                                <div class="flex items-center gap-2.5 sm:gap-3 pr-2">
                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0 shadow-2xs">
                                        <i data-lucide="layers" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-[11px] sm:text-xs text-slate-500 font-medium">ประเภทที่เลือก</div>
                                        <div class="text-sm sm:text-base font-bold text-slate-900 flex items-baseline gap-1">
                                            <span id="selectedTypesCount" class="text-base sm:text-xl font-extrabold text-emerald-600 font-mono">1</span>
                                            <span class="text-[11px] sm:text-xs font-normal text-slate-500">ประเภท</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Col 2: Total Weight -->
                                <div class="flex items-center gap-2.5 sm:gap-3 pl-3 sm:pl-4">
                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 flex-shrink-0 shadow-2xs">
                                        <i data-lucide="scale" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-[11px] sm:text-xs text-slate-500 font-medium">น้ำหนักรวมประมาณ</div>
                                        <div class="text-sm sm:text-base font-bold text-slate-900 font-mono flex items-baseline gap-1">
                                            <span id="totalEstimatedWeight" class="text-base sm:text-xl font-extrabold text-teal-600 font-mono">10.0</span>
                                            <span class="text-[11px] sm:text-xs font-normal text-slate-500">กก.</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Photo Upload Section -->
                        <div class="pt-2">
                            <label class="block text-sm font-semibold text-slate-800 mb-2">
                                รูปภาพจุดทิ้งขยะ (Before Photo)
                            </label>
                            <input type="file" name="image" id="imageInput" accept="image/*"
                                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        </div>


                        <!-- Image Preview Container -->
                        <div id="imagePreviewWrapper" class="hidden">
                            <div class="text-xs font-semibold text-slate-600 mb-2">ตัวอย่างรูปภาพที่เลือก:</div>
                            <img id="imagePreview" src="#" alt="Preview" class="max-h-48 rounded-xl border border-slate-200 shadow-sm object-cover">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-800 mb-2">
                                รายละเอียดเพิ่มเติม หรือ ข้อสังเกตอื่น ๆ
                            </label>
                            <textarea name="description" rows="3" placeholder="เช่น ส่งกลิ่นเน่าเหม็นรุนแรง มีน้ำขัง หรือ มีวัสดุมีคมปะปนอยู่"
                                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"></textarea>
                        </div>
                    </div>
                </div>


                <!-- Submit Button Area -->
                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-500 text-center sm:text-left">
                        🔒 ข้อมูลของท่านจะถูกส่งตรงเข้าสู่ระบบบริหารจัดการเพื่อมอบหมายงานให้เจ้าหน้าที่
                    </p>
                    <button type="submit" id="submitBtn" class="w-full sm:w-auto px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition duration-200 flex items-center justify-center gap-2 shadow-xl shadow-emerald-600/25 hover:scale-[1.02]">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        <span>ส่งเรื่องแจ้งจัดเก็บขยะ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Leaflet Map Script for Location Picker with Auto Reverse Geocoding -->
<script <?= \App\Core\CSP::nonceAttr() ?>>
document.addEventListener('DOMContentLoaded', function() {
    let defaultLat = 13.8628;
    let defaultLng = 100.5145;

    const map = L.map('pickerMap', {
        preferCanvas: true,
        zoomAnimation: true,
        fadeAnimation: true
    }).setView([defaultLat, defaultLng], 15);

    setTimeout(() => map.invalidateSize(), 150);

    // Form submit loading spinner & debounce
    const reportForm = document.getElementById('wasteReportForm');
    if (reportForm) {
        reportForm.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.innerHTML = `<svg class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>กำลังส่งข้อมูล...</span>`;
            }
        });
    }

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Draggable Marker with Tooltip
    let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
    marker.bindTooltip("📍 ลากหมุดเพื่อระบุจุดทิ้งขยะที่แน่นอน", { permanent: false, direction: 'top', offset: [0, -10] });

    let accuracyCircle = null;

    function updateInputs(lat, lng) {
        document.getElementById('latitude').value = parseFloat(lat).toFixed(7);
        document.getElementById('longitude').value = parseFloat(lng).toFixed(7);
    }

    // Set initial coordinates
    updateInputs(defaultLat, defaultLng);

    // Smart Thai Address Formatter from OpenStreetMap Nominatim with Postal Code Support
    function formatThaiAddress(a, displayName) {
        if (!a) return displayName || '';

        // 1. Road / Soi / Street
        let road = a.road || a.pedestrian || a.footway || a.street || '';

        // 2. Place / Landmark / Facility
        let landmark = a.building || a.amenity || a.office || a.neighbourhood || a.quarter || '';
        if (landmark === road) landmark = '';

        // 3. Province (จังหวัด)
        let province = a.province || a.state || '';
        if (!province && a.city && !a.city.includes('เทศบาล') && !a.city.includes('เมือง')) {
            province = a.city;
        }

        let isBKK = province.includes('กรุงเทพ');

        // Clean province prefix
        let cleanProvince = province.replace(/^จังหวัด\s*/, '').replace(/^จ\.\s*/, '').trim();
        let formattedProvince = '';
        if (cleanProvince === 'กรุงเทพมหานคร' || cleanProvince === 'Bangkok') {
            formattedProvince = 'กรุงเทพมหานคร';
            isBKK = true;
        } else if (cleanProvince) {
            formattedProvince = 'จ.' + cleanProvince;
        }

        // 4. Sub-district (ตำบล / แขวง)
        let rawSubdistrict = a.subdistrict || a.suburb || a.village || '';
        let cleanSubdistrict = '';
        if (rawSubdistrict) {
            cleanSubdistrict = rawSubdistrict.replace(/^(ตำบล|แขวง|ต\.|ข\.)\s*/, '').trim();
        }

        // 5. District (อำเภอ / เขต)
        let rawDistrict = a.district || a.city_district || a.county || '';
        let cleanDistrict = '';
        if (rawDistrict) {
            let tempDist = rawDistrict.replace(/^(อำเภอ|เขต|อ\.|ข\.|ตำบล|แขวง|ต\.)\s*/, '').trim();
            // Avoid duplicate if district name equals subdistrict name
            if (tempDist !== cleanSubdistrict) {
                cleanDistrict = tempDist;
            }
        }
        // Fallback for district if missing or in city
        if (!cleanDistrict && a.city) {
            let cityClean = a.city.replace(/^(เทศบาลนคร|เทศบาลเมือง|เทศบาลตำบล|อำเภอ|เขต|อ\.|ข\.)\s*/, '').trim();
            if (cityClean.includes('เมือง') || cityClean === cleanProvince) {
                cleanDistrict = 'เมือง' + (cleanProvince ? cleanProvince : '');
            } else if (cityClean && cityClean !== cleanSubdistrict && cityClean !== cleanProvince) {
                cleanDistrict = cityClean;
            }
        }

        let formattedSubdistrict = cleanSubdistrict ? (isBKK ? 'แขวง' : 'ต.') + cleanSubdistrict : '';
        let formattedDistrict = cleanDistrict ? (isBKK ? 'เขต' : 'อ.') + cleanDistrict : '';

        // 6. Postal Code (รหัสไปรษณีย์)
        let postcode = a.postcode || a.postal_code || '';
        if (!postcode && (cleanProvince.includes('นนทบุรี') || formattedProvince.includes('นนทบุรี'))) {
            // Default postcode for Nonthaburi municipality
            postcode = '11000';
        }

        // 7. Assemble neatly without duplicate segments
        let parts = [];
        if (road) parts.push(road);
        if (landmark && landmark !== road && !parts.includes(landmark)) parts.push(landmark);
        if (formattedSubdistrict && !parts.includes(formattedSubdistrict)) parts.push(formattedSubdistrict);
        if (formattedDistrict && !parts.includes(formattedDistrict)) parts.push(formattedDistrict);
        if (formattedProvince && !parts.includes(formattedProvince)) parts.push(formattedProvince);
        if (postcode && !parts.includes(postcode)) parts.push(postcode);

        return parts.length > 0 ? parts.join(' ') : displayName;
    }

    // Auto Reverse Geocode using OpenStreetMap Nominatim
    async function reverseGeocode(lat, lng) {
        const addressInput = document.getElementById('addressInput');
        const geoStatus = document.getElementById('geoStatus');
        if (!addressInput) return;

        if (geoStatus) {
            geoStatus.classList.remove('hidden');
            geoStatus.textContent = '⏳ กำลังดึงที่อยู่จากพิกัด...';
        }

        try {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=th`;
            const res = await fetch(url, { headers: { 'User-Agent': 'NonthaburiWasteApp/2.0' } });
            
            if (res.ok) {
                const data = await res.json();
                if (data && data.address) {
                    const formattedAddress = formatThaiAddress(data.address, data.display_name);
                    addressInput.value = formattedAddress;

                    if (geoStatus) {
                        geoStatus.textContent = '✨ ดึงที่อยู่อัตโนมัติเรียบร้อย';
                        setTimeout(() => { geoStatus.classList.add('hidden'); }, 3500);
                    }

                    // Highlight animation on textarea
                    addressInput.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/50');
                    setTimeout(() => {
                        addressInput.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/50');
                    }, 2000);
                }
            }
        } catch (err) {
            console.warn('Reverse geocoding error:', err);
            if (geoStatus) geoStatus.classList.add('hidden');
        }
    }

    marker.on('dragend', function (e) {
        const pos = marker.getLatLng();
        updateInputs(pos.lat, pos.lng);
        reverseGeocode(pos.lat, pos.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    // 1. Geolocation API with Accuracy Circle & Warning
    document.getElementById('getCurrentLocationBtn')?.addEventListener('click', function() {
        const btn = this;
        const gpsAccuracyBox = document.getElementById('gpsAccuracyBox');

        if (navigator.geolocation) {
            btn.innerHTML = '<span class="animate-spin mr-1">⌛</span> กำลังระบุพิกัด...';
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    const accuracy = pos.coords.accuracy || 10;

                    // Fly to location at high zoom level (18)
                    map.flyTo([lat, lng], 18, { duration: 1.2 });
                    marker.setLatLng([lat, lng]);
                    updateInputs(lat, lng);

                    // Draw / Update Accuracy Circle
                    if (accuracyCircle) {
                        map.removeLayer(accuracyCircle);
                    }
                    accuracyCircle = L.circle([lat, lng], {
                        radius: accuracy,
                        color: '#059669',
                        fillColor: '#10b981',
                        fillOpacity: 0.12,
                        weight: 1.5
                    }).addTo(map);

                    // Display Smart Accuracy Status
                    if (gpsAccuracyBox) {
                        gpsAccuracyBox.classList.remove('hidden');
                        if (accuracy <= 35) {
                            gpsAccuracyBox.className = 'p-3 rounded-xl text-xs font-medium border bg-emerald-50 text-emerald-900 border-emerald-300 flex items-center justify-between gap-2';
                            gpsAccuracyBox.innerHTML = `
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold text-sm">🎯</span>
                                    <span><strong>พิกัด GPS แม่นยำสูง</strong> (รัศมีคลาดเคลื่อน ±${Math.round(accuracy)} เมตร)</span>
                                </div>
                                <span class="text-[11px] text-emerald-700 font-semibold bg-emerald-100/80 px-2 py-0.5 rounded-md">ตรงจุด</span>
                            `;
                        } else {
                            gpsAccuracyBox.className = 'p-3 rounded-xl text-xs font-medium border bg-amber-50 text-amber-900 border-amber-300 flex items-center justify-between gap-2';
                            gpsAccuracyBox.innerHTML = `
                                <div class="flex items-center gap-2">
                                    <span class="text-amber-600 font-bold text-sm">💡</span>
                                    <span><strong>สัญญาณ GPS คลาดเคลื่อน (±${Math.round(accuracy)} ม.)</strong> สามารถคลิกหรือลากหมุดไปยังจุดทิ้งขยะที่แน่นอนได้</span>
                                </div>
                                <span class="text-[11px] text-amber-800 font-semibold bg-amber-100/80 px-2 py-0.5 rounded-md">ลากหมุดปรับแต่งได้</span>
                            `;
                        }
                    }

                    btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i><span>พบพิกัดแล้ว!</span>';
                    lucide.createIcons();
                    
                    // Auto-fill address asynchronously
                    reverseGeocode(lat, lng);
                },
                (err) => {
                    alert('ไม่สามารถระบุพิกัด GPS ได้ กรุณาพิมพ์ค้นหาชื่อสถานที่ หรือคลิกเลือกจุดบนแผนที่โดยตรง');
                    btn.innerHTML = '<i data-lucide="crosshair" class="w-4 h-4"></i><span>ใช้พิกัดปัจจุบัน (GPS)</span>';
                    lucide.createIcons();
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
            );
        } else {
            alert('เบราว์เซอร์ของคุณไม่รองรับการระบุตำแหน่ง GPS');
        }
    });

    // 2. Place & Street Search Box (Nominatim Geocoding)
    const placeSearchInput = document.getElementById('placeSearchInput');
    const doSearchBtn = document.getElementById('doSearchBtn');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const searchResultsDropdown = document.getElementById('searchResultsDropdown');

    let searchTimeout = null;

    async function executePlaceSearch(query) {
        if (!query || query.trim().length < 2) {
            searchResultsDropdown.classList.add('hidden');
            return;
        }

        searchResultsDropdown.innerHTML = '<div class="p-4 text-xs text-slate-500 text-center"><span class="animate-spin mr-1.5">⌛</span> กำลังค้นหาสถานที่ในนนทบุรี...</div>';
        searchResultsDropdown.classList.remove('hidden');

        try {
            // Biased to Nonthaburi / Greater Bangkok area
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&viewbox=100.35,13.98,100.65,13.75&bounded=0&countrycodes=th&limit=6&addressdetails=1&accept-language=th`;
            const res = await fetch(url, { headers: { 'User-Agent': 'NonthaburiWasteApp/2.0' } });
            
            if (res.ok) {
                const results = await res.json();
                if (results && results.length > 0) {
                    let html = '';
                    results.forEach((item, idx) => {
                        const title = item.name || item.display_name.split(',')[0];
                        const subtitle = item.display_name;
                        html += `
                            <div class="search-result-item p-3 hover:bg-emerald-50 cursor-pointer transition flex items-start gap-2.5" data-lat="${item.lat}" data-lng="${item.lon}" data-name="${title}">
                                <span class="text-emerald-600 mt-0.5 flex-shrink-0">📍</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-slate-900 truncate">${title}</div>
                                    <div class="text-[11px] text-slate-500 line-clamp-1">${subtitle}</div>
                                </div>
                            </div>
                        `;
                    });
                    searchResultsDropdown.innerHTML = html;

                    // Bind click on items
                    searchResultsDropdown.querySelectorAll('.search-result-item').forEach(el => {
                        el.addEventListener('click', async function() {
                            const lat = parseFloat(this.dataset.lat);
                            const lng = parseFloat(this.dataset.lng);
                            const name = this.dataset.name;

                            map.flyTo([lat, lng], 18, { duration: 1.2 });
                            marker.setLatLng([lat, lng]);
                            updateInputs(lat, lng);
                            
                            placeSearchInput.value = name;
                            searchResultsDropdown.classList.add('hidden');
                            clearSearchBtn.classList.remove('hidden');

                            await reverseGeocode(lat, lng);
                        });
                    });
                } else {
                    searchResultsDropdown.innerHTML = '<div class="p-4 text-xs text-slate-500 text-center">ไม่พบสถานที่ที่ระบุ กรุณาลองพิมพ์ชื่อซอยหรือถนนใกล้เคียง</div>';
                }
            }
        } catch (err) {
            console.warn('Geocoding error:', err);
            searchResultsDropdown.innerHTML = '<div class="p-4 text-xs text-rose-500 text-center">เกิดข้อผิดพลาดในการค้นหา</div>';
        }
    }

    placeSearchInput?.addEventListener('input', function() {
        const val = this.value;
        if (val.length > 0) {
            clearSearchBtn.classList.remove('hidden');
        } else {
            clearSearchBtn.classList.add('hidden');
            searchResultsDropdown.classList.add('hidden');
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            executePlaceSearch(val);
        }, 450);
    });

    placeSearchInput?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            executePlaceSearch(this.value);
        }
    });

    doSearchBtn?.addEventListener('click', function() {
        executePlaceSearch(placeSearchInput.value);
    });

    clearSearchBtn?.addEventListener('click', function() {
        placeSearchInput.value = '';
        this.classList.add('hidden');
        searchResultsDropdown.classList.add('hidden');
        placeSearchInput.focus();
    });

    // Close search dropdown on click outside
    document.addEventListener('click', function(e) {
        if (!placeSearchInput.contains(e.target) && !searchResultsDropdown.contains(e.target)) {
            searchResultsDropdown.classList.add('hidden');
        }
    });

    // 3. Quick Landmark Chips Click Listener
    document.querySelectorAll('.quick-zone-chip').forEach(chip => {
        chip.addEventListener('click', async function() {
            const lat = parseFloat(this.dataset.lat);
            const lng = parseFloat(this.dataset.lng);
            const name = this.dataset.name;

            map.flyTo([lat, lng], 18, { duration: 1.2 });
            marker.setLatLng([lat, lng]);
            updateInputs(lat, lng);

            if (placeSearchInput) {
                placeSearchInput.value = name;
                clearSearchBtn.classList.remove('hidden');
            }

            await reverseGeocode(lat, lng);
        });
    });


    // Multi-type selection and weight calculation
    function updateWasteSummary() {
        let selectedCount = 0;
        let totalWeight = 0.0;

        document.querySelectorAll('.waste-type-card').forEach(card => {
            const cb = card.querySelector('.waste-type-checkbox');
            const weightInput = card.querySelector('.weight-calc-input');
            const weightContainer = card.querySelector('.weight-container');

            if (cb && cb.checked) {
                selectedCount++;
                card.classList.add('bg-emerald-50/60', 'border-emerald-500', 'shadow-sm');
                card.classList.remove('bg-slate-50', 'border-slate-200');
                if (weightContainer) weightContainer.classList.remove('opacity-60');
                if (weightInput) {
                    weightInput.disabled = false;
                    const val = parseFloat(weightInput.value) || 0;
                    totalWeight += val;
                }
            } else {
                card.classList.remove('bg-emerald-50/60', 'border-emerald-500', 'shadow-sm');
                card.classList.add('bg-slate-50', 'border-slate-200');
                if (weightContainer) weightContainer.classList.add('opacity-60');
            }
        });

        const countElem = document.getElementById('selectedTypesCount');
        const weightElem = document.getElementById('totalEstimatedWeight');
        if (countElem) countElem.textContent = selectedCount;
        if (weightElem) weightElem.textContent = totalWeight.toFixed(1);
    }

    // Attach event listeners
    document.querySelectorAll('.waste-type-checkbox').forEach(cb => {
        cb.addEventListener('change', updateWasteSummary);
    });

    document.querySelectorAll('.weight-calc-input').forEach(input => {
        input.addEventListener('input', updateWasteSummary);
        input.addEventListener('click', (e) => e.stopPropagation());
    });

    document.querySelectorAll('.waste-type-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'LABEL') return;
            const cb = this.querySelector('.waste-type-checkbox');
            if (cb) {
                cb.checked = !cb.checked;
                updateWasteSummary();
            }
        });
    });

    // Initial calculation
    updateWasteSummary();

    // Form submission validation
    document.getElementById('wasteReportForm')?.addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.waste-type-checkbox:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('กรุณาเลือกประเภทขยะอย่างน้อย 1 ประเภท');
            return false;
        }
    });

    // Live Image Preview
    const imageInput = document.getElementById('imageInput');
    const imagePreviewWrapper = document.getElementById('imagePreviewWrapper');
    const imagePreview = document.getElementById('imagePreview');

    imageInput?.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreviewWrapper.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            imagePreviewWrapper.classList.add('hidden');
        }
    });
});
</script>

<!-- Modal: Official Poster Infographic View -->
<div id="orphanWasteModal" class="modal-backdrop-auto fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 hidden">
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
            <button type="button" data-modal-close="orphanWasteModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-2 bg-slate-100 flex items-center justify-center max-h-[70vh]">
            <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/orphan_waste_guide.jpg" alt="ประกาศขยะกำพร้า เทศบาลนครนนทบุรี" class="w-full h-auto rounded-xl shadow-xs">
        </div>
        <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">สำนักการสาธารณสุขและสิ่งแวดล้อม</span>
            <button type="button" data-modal-close="orphanWasteModal" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>
</div>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/citizen.php';
?>
