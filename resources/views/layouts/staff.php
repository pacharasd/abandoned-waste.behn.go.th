<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title><?= htmlspecialchars($title ?? 'Staff Portal | ระบบเจ้าหน้าที่จัดเก็บขยะ') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Unified Client-side Security Helper (Pre-attaches CSP nonces) -->
    <script <?= \App\Core\CSP::nonceAttr() ?> src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/js/app-security.js"></script>

    <!-- Compiled Production Tailwind CSS (Zero Runtime, Zero CSP Violations) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/css/tailwind.css">
    
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha384-sHL9NAb7lN7rfvG5lfHpm643Xkcjzp4jFvuavGOndn6pjVqS6ny56CAt3nsEVT4H" crossorigin="anonymous"/>
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH" crossorigin="anonymous"></script>

    <!-- Lucide Icons -->
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" integrity="sha384-uTYyvsSSUZeaPhb5RbKlQa0zY/WpX/QHfvg2mczXyBQOpkWPEDy9lczyp+w7SKXu" crossorigin="anonymous"></script>

    <!-- Application External Stylesheet (CSP Hardened - No inline styles) -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/css/app-style.css">
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col antialiased">

    <!-- Staff Mobile Top Bar -->
    <header class="sticky top-0 z-40 bg-emerald-800 text-white shadow-md">
        <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/staff/dashboard" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center font-bold text-white shadow-sm">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="font-bold text-base leading-tight">ระบบเจ้าหน้าที่ภาคสนาม</div>
                    <div class="text-[11px] text-emerald-200"><?= htmlspecialchars($authUser['name'] ?? 'เจ้าหน้าที่') ?></div>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/logout" method="POST">
                    <?= \App\Core\CSRF::field() ?>
                    <button type="submit" title="ออกจากระบบ" class="px-3 py-1.5 bg-emerald-900/60 hover:bg-emerald-900 text-xs font-semibold rounded-lg text-emerald-100 transition flex items-center gap-1">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>ออก</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (!empty($flash)): ?>
        <div class="max-w-3xl mx-auto px-4 mt-4 w-full">
            <div class="p-3.5 rounded-xl border flex items-center justify-between shadow-sm <?= $flash['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' ?>">
                <div class="flex items-center gap-2 text-xs font-medium">
                    <i data-lucide="<?= $flash['type'] === 'success' ? 'check-circle-2' : 'alert-circle' ?>" class="w-4 h-4 flex-shrink-0"></i>
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                </div>
                <button type="button" data-dismiss="alert" class="p-1 hover:opacity-75" aria-label="ปิดการแจ้งเตือน">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-3xl w-full mx-auto p-4 sm:p-6 mb-12">
        <?php if (isset($viewContent)) echo $viewContent; ?>
    </main>

    <script <?= \App\Core\CSP::nonceAttr() ?>>
        lucide.createIcons();
    </script>
</body>
</html>
