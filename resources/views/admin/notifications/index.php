<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div>
            <h2 class="text-xl font-bold text-slate-900">การแจ้งเตือนภายในระบบ</h2>
            <p class="text-xs text-slate-400 mt-0.5">การแจ้งเตือนเมื่อมีรายการแจ้งใหม่ หรือเมื่อเจ้าหน้าที่ปฏิบัติงานเสร็จสิ้น</p>
        </div>

        <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
            <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/notifications/read-all" method="POST">
                <?= \App\Core\CSRF::field() ?>
                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center gap-1.5">
                    <i data-lucide="check-check" class="w-4 h-4 text-emerald-600"></i>
                    <span>ทำเครื่องหมายว่าอ่านทั้งหมดแล้ว</span>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Notifications List Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden divide-y divide-slate-100">
        <?php if (empty($notifications)): ?>
            <div class="p-12 text-center text-slate-400 space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                    <i data-lucide="bell-off" class="w-6 h-6"></i>
                </div>
                <div class="text-sm font-medium">ไม่มีการแจ้งเตือนในขณะนี้</div>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <div class="p-5 flex items-start gap-4 transition <?= empty($n['is_read']) ? 'bg-emerald-50/40' : 'hover:bg-slate-50' ?>">
                    <!-- Icon Type -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 <?= $n['type'] === 'new_report' ? 'bg-amber-100 text-amber-700' : ($n['type'] === 'job_completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') ?>">
                        <i data-lucide="<?= $n['type'] === 'new_report' ? 'plus-circle' : ($n['type'] === 'job_completed' ? 'check-circle' : 'bell') ?>" class="w-5 h-5"></i>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-sm font-bold text-slate-900 truncate">
                                <?= htmlspecialchars($n['title']) ?>
                            </h4>
                            <span class="text-[11px] text-slate-400 flex-shrink-0">
                                <?= date('d/m/Y H:i', strtotime($n['created_at'])) ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            <?= htmlspecialchars($n['message']) ?>
                        </p>
                    </div>

                    <!-- Action -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <?php if ($n['related_id']): ?>
                            <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/notifications/<?= $n['id'] ?>/read" method="POST">
                                <?= \App\Core\CSRF::field() ?>
                                <button type="submit" class="px-3 py-1.5 bg-white hover:bg-emerald-50 text-emerald-700 border border-slate-200 hover:border-emerald-300 rounded-lg text-xs font-semibold transition flex items-center gap-1 shadow-sm">
                                    <span>เปิดดู</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

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
