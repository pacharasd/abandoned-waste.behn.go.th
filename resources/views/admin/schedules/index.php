<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Top Header Card with Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-900">จัดการรอบวันจัดเก็บขยะประจำเดือน</h2>
            <p class="text-xs text-slate-400 mt-0.5">บริหารรอบการลงพื้นที่จัดเก็บขยะไร้บ้านและขยะชิ้นใหญ่ (เดือนละ 1 ครั้ง)</p>
        </div>

        <div class="flex items-center gap-2 sm:gap-2.5 flex-wrap">
            <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/schedules/quick-generate" method="POST" onsubmit="return confirm('ยืนยันสร้างรอบจัดเก็บขยะประจำเดือนถัดไปอัตโนมัติ?')">
                <?= \App\Core\CSRF::field() ?>
                <button type="submit" class="px-3 sm:px-4 py-2 sm:py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-2 border border-slate-200">
                    <i data-lucide="sparkles" class="w-4 h-4 text-emerald-600"></i>
                    <span>สร้างรอบเดือนถัดไปอัตโนมัติ</span>
                </button>
            </form>

            <button type="button" data-modal-open="addScheduleModal" class="px-3.5 sm:px-4 py-2 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-sm shadow-emerald-600/20">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>สร้างรอบจัดเก็บใหม่</span>
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">รอบจัดเก็บทั้งหมด</div>
            <div class="text-2xl font-bold text-slate-900 mt-1"><?= number_format($metrics['total'] ?? 0) ?> <span class="text-sm font-normal text-slate-500">รอบ</span></div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">รอบที่กำลังเปิดรับ/ใช้งาน</div>
            <div class="text-2xl font-bold text-emerald-700 mt-1"><?= number_format($metrics['active'] ?? 0) ?> <span class="text-sm font-normal text-emerald-600/80">รอบ</span></div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider">รายการขยะในรอบจัดเก็บ</div>
            <div class="text-2xl font-bold text-blue-700 mt-1"><?= number_format($metrics['total_reports'] ?? 0) ?> <span class="text-sm font-normal text-blue-600/80">รายการ</span></div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-teal-600 uppercase tracking-wider">น้ำหนักขยะในรอบรวม</div>
            <div class="text-2xl font-bold text-teal-700 mt-1"><?= number_format($metrics['total_weight'] ?? 0, 1) ?> <span class="text-sm font-normal text-teal-600/80">กก.</span></div>
        </div>
    </div>

    <!-- Schedules Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm min-w-[800px]">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 whitespace-nowrap">ชื่อรอบการจัดเก็บ</th>
                        <th class="px-6 py-3.5 whitespace-nowrap">วันและเวลาที่จัดเก็บ</th>
                        <th class="px-6 py-3.5 whitespace-nowrap">วันปิดรับแจ้ง</th>
                        <th class="px-6 py-3.5 text-center whitespace-nowrap">จำนวนขยะที่แจ้ง</th>
                        <th class="px-6 py-3.5 text-right whitespace-nowrap">น้ำหนักรวม (กก.)</th>
                        <th class="px-6 py-3.5 text-center whitespace-nowrap">สถานะ</th>
                        <th class="px-6 py-3.5 text-right whitespace-nowrap">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">ยังไม่มีข้อมูลรอบการจัดเก็บในระบบ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $s): ?>
                            <?php
                            $sDate = strtotime($s['collection_date']);
                            $statusInfo = [
                                'active' => ['class' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'label' => '🟢 เปิดรับแจ้ง (Active)'],
                                'upcoming' => ['class' => 'bg-blue-50 text-blue-800 border-blue-200', 'label' => '🔵 รอบถัดไป (Upcoming)'],
                                'collecting' => ['class' => 'bg-amber-50 text-amber-800 border-amber-200', 'label' => '🟡 กำลังดำเนินการ'],
                                'completed' => ['class' => 'bg-slate-100 text-slate-600 border-slate-200', 'label' => '⚪ จัดเก็บเสร็จสิ้น'],
                                'cancelled' => ['class' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => '🔴 ยกเลิก']
                            ][$s['status']] ?? ['class' => 'bg-slate-100 text-slate-600 border-slate-200', 'label' => $s['status']];
                            ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4">
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/schedules/<?= $s['id'] ?>" class="font-bold text-slate-900 hover:text-emerald-600 transition block">
                                        <?= htmlspecialchars($s['title']) ?>
                                    </a>
                                    <span class="text-xs text-slate-400 block truncate max-w-xs" title="<?= htmlspecialchars($s['area_zone']) ?>">
                                        📍 <?= htmlspecialchars($s['area_zone']) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800"><?= date('d/m/Y', $sDate) ?></div>
                                    <div class="text-xs text-slate-400"><?= date('H:i', strtotime($s['start_time'])) ?> - <?= date('H:i', strtotime($s['end_time'])) ?> น.</div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($s['cutoff_date'])): ?>
                                        <div class="text-xs text-slate-700 font-medium"><?= date('d/m/Y', strtotime($s['cutoff_date'])) ?></div>
                                        <div class="text-[11px] text-slate-400">เวลา <?= date('H:i น.', strtotime($s['cutoff_date'])) ?></div>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="font-bold text-slate-900"><?= number_format($s['reports_count'] ?? 0) ?></span>
                                    <span class="text-xs text-slate-400">รายการ</span>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap font-mono font-semibold text-emerald-700">
                                    <?= number_format($s['total_weight'] ?? 0, 1) ?>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border <?= $statusInfo['class'] ?>">
                                        <?= $statusInfo['label'] ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap space-x-1">
                                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/schedules/<?= $s['id'] ?>" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition inline-flex items-center" title="ดูรายการขยะในรอบนี้">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                    <button type="button" class="btn-edit-schedule p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition inline-flex items-center" data-schedule='<?= json_encode($s, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>' title="แก้ไข">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/schedules/<?= $s['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('ยืนยันการลบรอบจัดเก็บ \'<?= htmlspecialchars($s['title'], ENT_QUOTES) ?>\'?')">
                                        <?= \App\Core\CSRF::field() ?>
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition inline-flex items-center" title="ลบ">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
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

<!-- Modal: Add New Schedule -->
<div id="addScheduleModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="calendar-plus" class="w-5 h-5 text-emerald-600"></i>
                <span>สร้างรอบจัดเก็บขยะใหม่</span>
            </h3>
            <button type="button" data-modal-close="addScheduleModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/schedules" method="POST" class="space-y-4">
            <?= \App\Core\CSRF::field() ?>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700">ชื่อรอบการจัดเก็บ <span class="text-rose-500">*</span></label>
                    <button type="button" id="btnAutoTitle" class="text-[11px] text-emerald-700 hover:text-emerald-800 font-semibold inline-flex items-center gap-1 hover:underline">
                        <span>✨ ตั้งชื่อตามเดือนอัตโนมัติ</span>
                    </button>
                </div>
                <input type="text" name="title" id="add_title" required placeholder="เช่น รอบจัดเก็บขยะชิ้นใหญ่ ประจำเดือนกันยายน 2569"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">วันที่จัดเก็บ <span class="text-rose-500">*</span></label>
                    <input type="date" name="collection_date" id="add_collection_date" required value="<?= date('Y-m-d', strtotime('+7 days')) ?>"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                    <div id="add_collection_preview" class="text-[11px] text-emerald-700 mt-1 font-medium"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">สถานะเริ่มต้น</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                        <option value="upcoming">🔵 รอบถัดไป (Upcoming)</option>
                        <option value="active" selected>🟢 เปิดรับเรื่อง (Active)</option>
                        <option value="collecting">🟡 กำลังจัดเก็บ (Collecting)</option>
                        <option value="completed">⚪ เสร็จสิ้น (Completed)</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">เวลาเริ่ม</label>
                        <input type="time" name="start_time" id="add_start_time" value="09:00"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">เวลาสิ้นสุด</label>
                        <input type="time" name="end_time" id="add_end_time" value="16:00"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                    </div>
                </div>
                <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                    <span class="text-[11px] text-slate-400">ช่วงเวลาด่วน:</span>
                    <button type="button" class="btn-time-preset text-[11px] px-2 py-0.5 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-600 rounded-md transition" data-start-target="add_start_time" data-end-target="add_end_time" data-start="09:00" data-end="16:00">09:00 - 16:00 น. (ปกติ)</button>
                    <button type="button" class="btn-time-preset text-[11px] px-2 py-0.5 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-600 rounded-md transition" data-start-target="add_start_time" data-end-target="add_end_time" data-start="08:30" data-end="12:00">ครึ่งวันเช้า</button>
                    <button type="button" class="btn-time-preset text-[11px] px-2 py-0.5 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-600 rounded-md transition" data-start-target="add_start_time" data-end-target="add_end_time" data-start="13:00" data-end="16:30">ครึ่งวันบ่าย</button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">วัน-เวลาปิดรับแจ้งล่วงหน้า (Cutoff Date)</label>
                <input type="datetime-local" name="cutoff_date" id="add_cutoff_date" value="<?= date('Y-m-d\T18:00', strtotime('+5 days')) ?>"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                
                <div id="add_cutoff_preview" class="text-xs font-medium text-emerald-800 bg-emerald-50/80 border border-emerald-200/80 px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                    <span class="preview-text"></span>
                </div>

                <div class="flex items-center gap-1.5 flex-wrap pt-1">
                    <span class="text-[11px] text-slate-400">ทางลัดกำหนดปิดรับ:</span>
                    <button type="button" class="btn-cutoff-preset text-[11px] px-2.5 py-1 bg-emerald-100/70 hover:bg-emerald-200 text-emerald-900 rounded-lg font-semibold transition" data-target="add_cutoff_date" data-base="add_collection_date" data-days="2" data-preview="add_cutoff_preview">⚡ ปิดก่อน 2 วัน (18:00 น.) [แนะนำ]</button>
                    <button type="button" class="btn-cutoff-preset text-[11px] px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" data-target="add_cutoff_date" data-base="add_collection_date" data-days="1" data-preview="add_cutoff_preview">⚡ ปิดก่อน 1 วัน</button>
                    <button type="button" class="btn-cutoff-preset text-[11px] px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" data-target="add_cutoff_date" data-base="add_collection_date" data-days="3" data-preview="add_cutoff_preview">⚡ ปิดก่อน 3 วัน</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">พื้นที่ / โซนให้บริการ</label>
                <input type="text" name="area_zone" value="ครอบคลุมทุกตำบล/ชุมชนในเขตเทศบาลนครนนทบุรี"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">รายละเอียด / คำแนะนำประชาชน</label>
                <textarea name="description" rows="3" placeholder="ระบุคำแนะนำ เช่น การนำขยะมาวางหน้าบ้านก่อนเวลา 08:30 น."
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" data-modal-close="addScheduleModal" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition">
                    ยกเลิก
                </button>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition shadow-sm shadow-emerald-600/20">
                    บันทึกรอบจัดเก็บ
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Schedule -->
<div id="editScheduleModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="edit-3" class="w-5 h-5 text-emerald-600"></i>
                <span>แก้ไขรอบจัดเก็บขยะ</span>
            </h3>
            <button type="button" data-modal-close="editScheduleModal" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editScheduleForm" method="POST" class="space-y-4">
            <?= \App\Core\CSRF::field() ?>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">ชื่อรอบการจัดเก็บ <span class="text-rose-500">*</span></label>
                <input type="text" name="title" id="edit_title" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">วันที่จัดเก็บ <span class="text-rose-500">*</span></label>
                    <input type="date" name="collection_date" id="edit_collection_date" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                    <div id="edit_collection_preview" class="text-[11px] text-emerald-700 mt-1 font-medium"></div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">สถานะ</label>
                    <select name="status" id="edit_status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                        <option value="upcoming">🔵 รอบถัดไป (Upcoming)</option>
                        <option value="active">🟢 เปิดรับเรื่อง (Active)</option>
                        <option value="collecting">🟡 กำลังจัดเก็บ (Collecting)</option>
                        <option value="completed">⚪ เสร็จสิ้น (Completed)</option>
                        <option value="cancelled">🔴 ยกเลิก (Cancelled)</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">เวลาเริ่ม</label>
                        <input type="time" name="start_time" id="edit_start_time"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">เวลาสิ้นสุด</label>
                        <input type="time" name="end_time" id="edit_end_time"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                    </div>
                </div>
                <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                    <span class="text-[11px] text-slate-400">ช่วงเวลาด่วน:</span>
                    <button type="button" class="btn-time-preset text-[11px] px-2 py-0.5 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-600 rounded-md transition" data-start-target="edit_start_time" data-end-target="edit_end_time" data-start="09:00" data-end="16:00">09:00 - 16:00 น. (ปกติ)</button>
                    <button type="button" class="btn-time-preset text-[11px] px-2 py-0.5 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-600 rounded-md transition" data-start-target="edit_start_time" data-end-target="edit_end_time" data-start="08:30" data-end="12:00">ครึ่งวันเช้า</button>
                    <button type="button" class="btn-time-preset text-[11px] px-2 py-0.5 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 text-slate-600 rounded-md transition" data-start-target="edit_start_time" data-end-target="edit_end_time" data-start="13:00" data-end="16:30">ครึ่งวันบ่าย</button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">วัน-เวลาปิดรับแจ้งล่วงหน้า (Cutoff Date)</label>
                <input type="datetime-local" name="cutoff_date" id="edit_cutoff_date"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition cursor-pointer">
                
                <div id="edit_cutoff_preview" class="text-xs font-medium text-emerald-800 bg-emerald-50/80 border border-emerald-200/80 px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                    <span class="preview-text"></span>
                </div>

                <div class="flex items-center gap-1.5 flex-wrap pt-1">
                    <span class="text-[11px] text-slate-400">ทางลัดกำหนดปิดรับ:</span>
                    <button type="button" class="btn-cutoff-preset text-[11px] px-2.5 py-1 bg-emerald-100/70 hover:bg-emerald-200 text-emerald-900 rounded-lg font-semibold transition" data-target="edit_cutoff_date" data-base="edit_collection_date" data-days="2" data-preview="edit_cutoff_preview">⚡ ปิดก่อน 2 วัน (18:00 น.) [แนะนำ]</button>
                    <button type="button" class="btn-cutoff-preset text-[11px] px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" data-target="edit_cutoff_date" data-base="edit_collection_date" data-days="1" data-preview="edit_cutoff_preview">⚡ ปิดก่อน 1 วัน</button>
                    <button type="button" class="btn-cutoff-preset text-[11px] px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" data-target="edit_cutoff_date" data-base="edit_collection_date" data-days="3" data-preview="edit_cutoff_preview">⚡ ปิดก่อน 3 วัน</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">พื้นที่ / โซนให้บริการ</label>
                <input type="text" name="area_zone" id="edit_area_zone"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">รายละเอียด / คำแนะนำประชาชน</label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" data-modal-close="editScheduleModal" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition">
                    ยกเลิก
                </button>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition shadow-sm shadow-emerald-600/20">
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

<script <?= \App\Core\CSP::nonceAttr() ?>>
const thaiMonthNames = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
const thaiDayNames = ['วันอาทิตย์', 'วันจันทร์', 'วันอังคาร', 'วันพุธ', 'วันพฤหัสบดี', 'วันศุกร์', 'วันเสาร์'];

function formatThaiDateDisplay(dateStr, withTime = false) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return '';
    const day = d.getDate();
    const dayName = thaiDayNames[d.getDay()];
    const month = thaiMonthNames[d.getMonth() + 1];
    const year = d.getFullYear() + 543;
    if (withTime) {
        const hours = String(d.getHours()).padStart(2, '0');
        const mins = String(d.getMinutes()).padStart(2, '0');
        return `${dayName}ที่ ${day} ${month} ${year} เวลา ${hours}:${mins} น.`;
    }
    return `${dayName}ที่ ${day} ${month} ${year}`;
}

function updateCutoffPreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    const val = input.value;
    const textEl = preview.querySelector('.preview-text') || preview;
    if (val) {
        textEl.textContent = 'กำหนดปิดรับ: ' + formatThaiDateDisplay(val, true);
        preview.classList.remove('hidden');
    } else {
        textEl.textContent = 'ยังไม่ได้กำหนดวันปิดรับแจ้งล่วงหน้า';
    }
}

function updateCollectionPreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    const val = input.value;
    if (val) {
        preview.textContent = 'ตรงกับ: ' + formatThaiDateDisplay(val, false);
    } else {
        preview.textContent = '';
    }
}

function calcAutoCutoff(collectionDateVal, daysBefore = 2, timeStr = '18:00') {
    if (!collectionDateVal) return '';
    const parts = collectionDateVal.split('-');
    if (parts.length !== 3) return '';
    const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    d.setDate(d.getDate() - parseInt(daysBefore, 10));
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}T${timeStr}`;
}

function generateAutoTitle(dateVal, targetId) {
    if (!dateVal) return;
    const parts = dateVal.split('-');
    if (parts.length !== 3) return;
    const monthNum = parseInt(parts[1], 10);
    const yearNum = parseInt(parts[0], 10) + 543;
    const monthName = thaiMonthNames[monthNum];
    const target = document.getElementById(targetId);
    if (target) {
        target.value = `รอบจัดเก็บขยะชิ้นใหญ่ ประจำเดือน${monthName} ${yearNum}`;
    }
}

// Click anywhere on date/time inputs to trigger native picker popup
document.querySelectorAll('input[type="date"], input[type="time"], input[type="datetime-local"]').forEach(input => {
    input.addEventListener('click', function() {
        if (typeof this.showPicker === 'function') {
            try { this.showPicker(); } catch (e) {}
        }
    });
});

// Auto-sync in Add modal
const addCollDate = document.getElementById('add_collection_date');
if (addCollDate) {
    addCollDate.addEventListener('change', function() {
        updateCollectionPreview('add_collection_date', 'add_collection_preview');
        const cutoff = document.getElementById('add_cutoff_date');
        if (cutoff) {
            cutoff.value = calcAutoCutoff(this.value, 2, '18:00');
            updateCutoffPreview('add_cutoff_date', 'add_cutoff_preview');
        }
        const titleInput = document.getElementById('add_title');
        if (titleInput && !titleInput.value.trim()) {
            generateAutoTitle(this.value, 'add_title');
        }
    });
    // Run initial previews
    updateCollectionPreview('add_collection_date', 'add_collection_preview');
    updateCutoffPreview('add_cutoff_date', 'add_cutoff_preview');
}

// Auto-sync in Edit modal
const editCollDate = document.getElementById('edit_collection_date');
if (editCollDate) {
    editCollDate.addEventListener('change', function() {
        updateCollectionPreview('edit_collection_date', 'edit_collection_preview');
        const cutoff = document.getElementById('edit_cutoff_date');
        if (cutoff && !cutoff.value) {
            cutoff.value = calcAutoCutoff(this.value, 2, '18:00');
            updateCutoffPreview('edit_cutoff_date', 'edit_cutoff_preview');
        }
    });
}

// Cutoff input change triggers preview update
document.getElementById('add_cutoff_date')?.addEventListener('input', function() {
    updateCutoffPreview('add_cutoff_date', 'add_cutoff_preview');
});
document.getElementById('edit_cutoff_date')?.addEventListener('input', function() {
    updateCutoffPreview('edit_cutoff_date', 'edit_cutoff_preview');
});

// Presets for cutoff date
document.querySelectorAll('.btn-cutoff-preset').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.dataset.target;
        const baseId = this.dataset.base;
        const days = this.dataset.days || 2;
        const previewId = this.dataset.preview;
        const baseVal = document.getElementById(baseId)?.value;
        if (!baseVal) return;
        const newCutoff = calcAutoCutoff(baseVal, days, '18:00');
        const targetEl = document.getElementById(targetId);
        if (targetEl) {
            targetEl.value = newCutoff;
            updateCutoffPreview(targetId, previewId);
        }
    });
});

// Presets for time
document.querySelectorAll('.btn-time-preset').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const startTarget = document.getElementById(this.dataset.startTarget);
        const endTarget = document.getElementById(this.dataset.endTarget);
        if (startTarget && this.dataset.start) startTarget.value = this.dataset.start;
        if (endTarget && this.dataset.end) endTarget.value = this.dataset.end;
    });
});

// Auto title button
document.getElementById('btnAutoTitle')?.addEventListener('click', function(e) {
    e.preventDefault();
    const val = document.getElementById('add_collection_date')?.value;
    generateAutoTitle(val, 'add_title');
});

function openEditModal(data) {
    const baseUrl = '<?= htmlspecialchars($baseUrl ?: "") ?>';
    document.getElementById('editScheduleForm').action = baseUrl + '/admin/schedules/' + data.id + '/update';
    
    document.getElementById('edit_title').value = data.title || '';
    document.getElementById('edit_collection_date').value = data.collection_date || '';
    document.getElementById('edit_start_time').value = (data.start_time || '09:00:00').substring(0, 5);
    document.getElementById('edit_end_time').value = (data.end_time || '16:00:00').substring(0, 5);
    document.getElementById('edit_area_zone').value = data.area_zone || '';
    document.getElementById('edit_description').value = data.description || '';
    document.getElementById('edit_status').value = data.status || 'upcoming';
    
    if (data.cutoff_date) {
        const isoStr = data.cutoff_date.replace(' ', 'T').substring(0, 16);
        document.getElementById('edit_cutoff_date').value = isoStr;
    } else {
        document.getElementById('edit_cutoff_date').value = calcAutoCutoff(data.collection_date, 2, '18:00');
    }

    updateCollectionPreview('edit_collection_date', 'edit_collection_preview');
    updateCutoffPreview('edit_cutoff_date', 'edit_cutoff_preview');

    document.getElementById('editScheduleModal').classList.remove('hidden');
}

document.querySelectorAll('.btn-edit-schedule').forEach(btn => {
    btn.addEventListener('click', function() {
        const data = JSON.parse(this.getAttribute('data-schedule'));
        openEditModal(data);
    });
});
</script>

<?php
$viewContent = ob_get_clean();
require BASE_PATH . '/resources/views/layouts/admin.php';
?>
