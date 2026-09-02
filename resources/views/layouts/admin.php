<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= \App\Core\CSRF::token() ?>">
    <title><?= htmlspecialchars($title ?? 'Admin Portal | ระบบแจ้งจัดเก็บขยะไร้บ้าน') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://cdn.tailwindcss.com"></script>
    <script <?= \App\Core\CSP::nonceAttr() ?>>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    },
                    fontFamily: {
                        sans: ['Kanit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Chart.js -->
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Lucide Icons -->
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://unpkg.com/lucide@latest"></script>

    <!-- Unified Client-side Security Helper -->
    <script <?= \App\Core\CSP::nonceAttr() ?> src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/js/app-security.js"></script>
    
    <style>
        html { scroll-behavior: smooth; }
        body { 
            font-family: 'Kanit', sans-serif; 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        button, a { -webkit-tap-highlight-color: transparent; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 flex h-screen overflow-hidden antialiased">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="adminSidebarBackdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 lg:hidden hidden transition-opacity duration-300"></div>

    <!-- Sidebar Navigation (Desktop Static + Mobile Off-Canvas Drawer) -->
    <aside id="adminSidebar" class="w-64 bg-slate-900 text-slate-300 flex flex-col flex-shrink-0 z-50 fixed inset-y-0 left-0 -translate-x-full lg:translate-x-0 lg:static lg:inset-auto transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none h-full">
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-5 bg-slate-950 border-b border-slate-800 flex-shrink-0">
            <div class="flex items-center gap-3">
                <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/nonthaburi-logo.png" alt="เทศบาลนครนนทบุรี" class="w-8 h-8 object-contain bg-white rounded-full p-0.5">
                <div>
                    <span class="font-bold text-white tracking-wide text-sm block leading-tight">Admin Portal</span>
                    <span class="text-[10px] text-emerald-400 font-normal">เทศบาลนครนนทบุรี</span>
                </div>
            </div>
            <!-- Mobile Close Drawer Button -->
            <button type="button" data-sidebar-close class="lg:hidden p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition" aria-label="ปิดเมนู">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>


        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/dashboard" class="admin-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/dashboard') !== false || $_SERVER['REQUEST_URI'] === '/admin' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span>ภาพรวม Dashboard</span>
            </a>

            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports" class="admin-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/reports') !== false && strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/reports/export') === false ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                <i data-lucide="inbox" class="w-4 h-4"></i>
                <span>รายการแจ้งขยะ</span>
            </a>

            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/schedules" class="admin-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/schedules') !== false ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                <i data-lucide="calendar-days" class="w-4 h-4"></i>
                <span>รอบวันจัดเก็บประจำเดือน</span>
            </a>

            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/waste-types" class="admin-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/waste-types') !== false ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                <i data-lucide="tags" class="w-4 h-4"></i>
                <span>ประเภทขยะ</span>
            </a>

            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/analytics" class="admin-nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/analytics') !== false ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                <span>รายงานและสถิติ</span>
            </a>

            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/notifications" class="admin-nav-link flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm transition <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/notifications') !== false ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' ?>">
                <div class="flex items-center gap-3">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span>การแจ้งเตือน</span>
                </div>
                <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                    <span class="px-2 py-0.5 bg-rose-500 text-white text-[11px] font-bold rounded-full">
                        <?= $unreadCount ?>
                    </span>
                <?php endif; ?>
            </a>
        </nav>

        <!-- Current User Profile & Public Site Link -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/60 space-y-3 flex-shrink-0">
            <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" target="_blank" class="flex items-center gap-2 text-xs text-emerald-400 hover:underline">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>เปิดดูหน้าเว็บประชาชน</span>
            </a>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                        A
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-white truncate"><?= htmlspecialchars($authUser['name'] ?? 'Admin') ?></div>
                        <div class="text-[10px] text-slate-400">ผู้ดูแลระบบ</div>
                    </div>
                </div>
                <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/logout" method="POST">
                    <?= \App\Core\CSRF::field() ?>
                    <button type="submit" title="ออกจากระบบ" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- Top App Bar -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between flex-shrink-0 z-10">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <!-- Hamburger Menu Button for Mobile/Tablet -->
                <button type="button" data-sidebar-open class="lg:hidden p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition flex-shrink-0" aria-label="เปิดเมนู">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <h1 class="text-sm sm:text-base md:text-lg font-bold text-slate-900 truncate max-w-[200px] sm:max-w-xs md:max-w-md lg:max-w-none">
                    <?= htmlspecialchars($title ?? 'Admin Portal') ?>
                </h1>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                <!-- Notification Bell with Unread Badge -->
                <div class="relative">
                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/notifications" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition relative flex items-center justify-center">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                            <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white animate-pulse">
                                <?= $unreadCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Export CSV Fast Button -->
                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/reports/export/csv" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition border border-slate-200">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Export CSV</span>
                </a>
            </div>
        </header>

        <!-- Flash Alert in Admin -->
        <?php if (!empty($flash)): ?>
            <div class="px-4 sm:px-6 pt-4">
                <div class="p-3.5 rounded-xl border flex items-center justify-between shadow-sm <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : ($flash['type'] === 'danger' ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-amber-50 border-amber-200 text-amber-900') ?>">
                    <div class="flex items-center gap-2.5 text-xs font-medium">
                        <i data-lucide="<?= $flash['type'] === 'success' ? 'check-circle-2' : ($flash['type'] === 'danger' ? 'alert-circle' : 'info') ?>" class="w-4 h-4 flex-shrink-0"></i>
                        <span><?= htmlspecialchars($flash['message']) ?></span>
                    </div>
                    <button type="button" data-dismiss="alert" class="p-1 hover:opacity-75" aria-label="ปิดการแจ้งเตือน">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Scrollable Page Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            <?php if (isset($viewContent)) echo $viewContent; ?>
        </main>
    </div>

    <script <?= \App\Core\CSP::nonceAttr() ?>>
        lucide.createIcons();

        function toggleAdminSidebar(open) {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            if (!sidebar || !backdrop) return;

            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Attach listeners for sidebar triggers
        document.querySelectorAll('[data-sidebar-open]').forEach(el => el.addEventListener('click', () => toggleAdminSidebar(true)));
        document.querySelectorAll('[data-sidebar-close]').forEach(el => el.addEventListener('click', () => toggleAdminSidebar(false)));
        document.getElementById('adminSidebarBackdrop')?.addEventListener('click', () => toggleAdminSidebar(false));
        document.querySelectorAll('.admin-nav-link').forEach(el => el.addEventListener('click', () => {
            if (window.innerWidth < 1024) toggleAdminSidebar(false);
        }));

        // Close drawer with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                toggleAdminSidebar(false);
            }
        });

        // Close drawer on resize to desktop width
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                toggleAdminSidebar(false);
            }
        });
    </script>
</body>
</html>
