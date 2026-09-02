<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= \App\Core\CSRF::token() ?>">
    <title><?= htmlspecialchars($title ?? 'ระบบแจ้งจัดเก็บขยะไร้บ้าน') ?></title>
    
    <!-- Google Fonts (Kanit & Prompt for Modern Thai Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Unified Client-side Security Helper (Pre-attaches CSP nonces) -->
    <script <?= \App\Core\CSP::nonceAttr() ?> src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/js/app-security.js"></script>

    <!-- Compiled Production Tailwind CSS (Zero Runtime, Zero CSP Violations) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/css/tailwind.css">
    
    <!-- Leaflet CSS & JS for OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha384-sHL9NAb7lN7rfvG5lfHpm643Xkcjzp4jFvuavGOndn6pjVqS6ny56CAt3nsEVT4H" crossorigin="anonymous"/>
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH" crossorigin="anonymous"></script>
    
    <!-- Lucide Icons -->
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" integrity="sha384-uTYyvsSSUZeaPhb5RbKlQa0zY/WpX/QHfvg2mczXyBQOpkWPEDy9lczyp+w7SKXu" crossorigin="anonymous"></script>

    <!-- html2canvas loaded with defer for non-blocking page load -->
    <script <?= \App\Core\CSP::nonceAttr() ?> defer src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha384-ZZ1pncU3bQe8y31yfZdMFdSpttDoPmOZg2wguVK9almUodir1PghgT0eY7Mrty8H" crossorigin="anonymous"></script>

    <!-- Application External Stylesheet (CSP Hardened - No inline styles) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/css/app-style.css">
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Citizen Navbar -->
    <header class="sticky top-0 z-[9999] glass-nav border-b border-slate-200/80 transition-all duration-300 shadow-xs">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Brand Logo & Title -->
                <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" class="flex items-center gap-3 group min-w-0">
                    <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/nonthaburi-logo.png" alt="เทศบาลนครนนทบุรี" class="w-11 h-11 sm:w-13 sm:h-13 object-contain drop-shadow-sm group-hover:scale-105 transition duration-200 flex-shrink-0">
                    <div class="truncate min-w-0">
                        <div class="text-base sm:text-lg font-bold text-slate-900 leading-tight group-hover:text-emerald-700 transition truncate">
                            ระบบแจ้งจัดเก็บขยะไร้บ้าน
                        </div>
                        <div class="hidden sm:block text-xs font-medium text-emerald-700 font-prompt truncate">
                            เทศบาลนครนนทบุรี
                        </div>
                    </div>
                </a>


                <?php
                $currentUri = \App\Core\Request::uri();
                $isHome = ($currentUri === '/' || $currentUri === '');
                $isSchedule = ($currentUri === '/schedule');
                $isReport = ($currentUri === '/report' || $currentUri === '/success');
                $isTrack = ($currentUri === '/track');
                ?>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-2">
                    <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" class="px-4 py-2 rounded-xl transition duration-150 flex items-center gap-2 <?= $isHome ? 'bg-emerald-50 text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60 font-medium' ?>">
                        <i data-lucide="home" class="w-4 h-4 <?= $isHome ? 'text-emerald-600' : '' ?>"></i>
                        <span>หน้าหลัก</span>
                    </a>
                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/schedule" class="px-4 py-2 rounded-xl transition duration-150 flex items-center gap-2 <?= $isSchedule ? 'bg-emerald-50 text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60 font-medium' ?>">
                        <i data-lucide="calendar-days" class="w-4 h-4 <?= $isSchedule ? 'text-emerald-600' : '' ?>"></i>
                        <span>รอบวันจัดเก็บ</span>
                    </a>
                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="px-4 py-2 rounded-xl transition duration-150 flex items-center gap-2 <?= $isReport ? 'bg-emerald-50 text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60 font-medium' ?>">
                        <i data-lucide="plus-circle" class="w-4 h-4 <?= $isReport ? 'text-emerald-600' : '' ?>"></i>
                        <span>แจ้งเก็บขยะ</span>
                    </a>
                    <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/track" class="px-4 py-2 rounded-xl transition duration-150 flex items-center gap-2 <?= $isTrack ? 'bg-emerald-50 text-emerald-800 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50/60 font-medium' ?>">
                        <i data-lucide="search" class="w-4 h-4 <?= $isTrack ? 'text-emerald-600' : '' ?>"></i>
                        <span>ติดตามสถานะ</span>
                    </a>
                </nav>

                <!-- Action Button & Admin Login for Desktop -->
                <div class="hidden md:flex items-center gap-3">
                    <?php if ($authUser): ?>
                        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/dashboard" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-medium transition shadow-sm">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 text-emerald-400"></i>
                            <span>เข้าสู่ Admin Portal</span>
                        </a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/login" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 hover:border-slate-400 text-slate-700 hover:bg-slate-100/80 rounded-xl font-medium transition duration-150 text-sm">
                            <i data-lucide="lock" class="w-4 h-4 text-slate-500"></i>
                            <span>เข้าสู่ระบบ Admin</span>
                        </a>
                        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition shadow-md shadow-emerald-600/25">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>แจ้งเก็บขยะทันที</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Toggle Button -->
                <div class="md:hidden flex items-center gap-2 flex-shrink-0">
                    <button id="mobileMenuBtn" class="p-2.5 text-slate-700 hover:text-slate-900 rounded-xl hover:bg-slate-100 border border-slate-200/80 transition" aria-label="Toggle menu">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div id="mobileMenu" class="hidden md:hidden pb-4 pt-2 border-t border-slate-100 space-y-1.5">
                <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm transition <?= $isHome ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-emerald-50/60 hover:text-emerald-800 font-medium' ?>">
                    <i data-lucide="home" class="w-4 h-4 <?= $isHome ? 'text-emerald-600' : 'text-slate-400' ?>"></i>
                    <span>หน้าหลัก</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/schedule" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm transition <?= $isSchedule ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-emerald-50/60 hover:text-emerald-800 font-medium' ?>">
                    <i data-lucide="calendar-days" class="w-4 h-4 <?= $isSchedule ? 'text-emerald-600' : 'text-slate-400' ?>"></i>
                    <span>รอบวันจัดเก็บ</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm transition <?= $isReport ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-emerald-50/60 hover:text-emerald-800 font-medium' ?>">
                    <i data-lucide="plus-circle" class="w-4 h-4 <?= $isReport ? 'text-emerald-600' : 'text-slate-400' ?>"></i>
                    <span>แจ้งเก็บขยะ</span>
                </a>
                <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/track" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm transition <?= $isTrack ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-slate-700 hover:bg-emerald-50/60 hover:text-emerald-800 font-medium' ?>">
                    <i data-lucide="search" class="w-4 h-4 <?= $isTrack ? 'text-emerald-600' : 'text-slate-400' ?>"></i>
                    <span>ติดตามสถานะ</span>
                </a>
                <div class="pt-2 border-t border-slate-100">
                    <?php if ($authUser): ?>
                        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/admin/dashboard" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 text-emerald-400"></i>
                            <span>เข้าสู่ Admin Portal</span>
                        </a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/login" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            <span>เข้าสู่ระบบ Admin</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </header>

    <!-- Flash Alert Messages -->
    <?php if (!empty($flash)): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-2xl border flex items-center justify-between shadow-sm <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : ($flash['type'] === 'danger' ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-amber-50 border-amber-200 text-amber-900') ?>">
                <div class="flex items-center gap-3">
                    <i data-lucide="<?= $flash['type'] === 'success' ? 'check-circle-2' : ($flash['type'] === 'danger' ? 'alert-circle' : 'info') ?>" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></span>
                </div>
                <button type="button" data-dismiss="alert" class="p-1 hover:opacity-75" aria-label="ปิดการแจ้งเตือน">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Dynamic Content Container -->
    <main class="flex-grow">
        <?php if (isset($viewContent)) echo $viewContent; ?>
    </main>

    <!-- Global Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-12 pb-8 border-t border-slate-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 pb-8 border-b border-slate-800">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/nonthaburi-logo.png" alt="เทศบาลนครนนทบุรี" class="w-10 h-10 object-contain bg-white rounded-full p-0.5">
                        <div>
                            <span class="font-bold text-white text-sm block">ระบบแจ้งจัดเก็บขยะไร้บ้าน</span>
                            <span class="text-[11px] text-emerald-400 font-normal">เทศบาลนครนนทบุรี</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        ระบบเพื่อประชาชนร่วมมือกับเทศบาลนครนนทบุรีในการแจ้งและกำจัดจุดทิ้งขยะไม่ถูกต้อง เพื่อสุขอนามัยและสิ่งแวดล้อมที่ดีของชุมชน
                    </p>
                </div>


                <div class="space-y-2">
                    <div class="text-sm font-bold text-white mb-3">ลิงก์ด่วน</div>
                    <ul class="text-xs space-y-2">
                        <li><a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" class="hover:text-emerald-400 transition">หน้าหลัก</a></li>
                        <li><a href="<?= htmlspecialchars($baseUrl ?: '') ?>/schedule" class="hover:text-emerald-400 transition">ปฏิทินและรอบวันจัดเก็บ</a></li>
                        <li><a href="<?= htmlspecialchars($baseUrl ?: '') ?>/report" class="hover:text-emerald-400 transition">แบบฟอร์มแจ้งเก็บขยะ</a></li>
                        <li><a href="<?= htmlspecialchars($baseUrl ?: '') ?>/track" class="hover:text-emerald-400 transition">ติดตามสถานะการจัดเก็บ</a></li>
                        <li><a href="<?= htmlspecialchars($baseUrl ?: '') ?>/login" class="hover:text-emerald-400 transition">สำหรับผู้ดูแลระบบ (Admin)</a></li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <div class="text-sm font-bold text-white mb-3">ติดต่อและแจ้งเหตุฉุกเฉิน</div>
                    <p class="text-xs text-slate-400">ศูนย์ประสานงานการจัดการขยะและสิ่งแวดล้อมชุมชน</p>
                    <div class="text-xs text-emerald-400 font-semibold flex items-center gap-1.5 mt-2">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                        <span>สายด่วน 1111 หรือ 02-xxx-xxxx</span>
                    </div>
                </div>
            </div>

            <div class="text-center text-xs text-slate-500">
                &copy; <?= date('Y') ?> ระบบแจ้งจัดเก็บขยะไร้บ้าน. สงวนลิขสิทธิ์ทุกประการ.
            </div>
        </div>
    </footer>

    <!-- Initialize Lucide Icons & Mobile Menu -->
    <script <?= \App\Core\CSP::nonceAttr() ?>>
        lucide.createIcons();

        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
