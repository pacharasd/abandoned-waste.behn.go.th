<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'เข้าสู่ระบบผู้ดูแลระบบ | ระบบแจ้งจัดเก็บขยะไร้บ้าน') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-emerald-950 via-slate-900 to-slate-950 text-slate-100 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" class="inline-block mb-4 hover:scale-105 transition">
            <img src="<?= htmlspecialchars($baseUrl ?: '') ?>/assets/images/nonthaburi-logo.png" alt="เทศบาลนครนนทบุรี" class="w-20 h-20 mx-auto object-contain bg-white rounded-full p-1 shadow-2xl shadow-emerald-500/20">
        </a>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">เข้าสู่ระบบ Admin Portal</h2>
        <p class="text-xs sm:text-sm text-emerald-400 font-medium mt-1">เทศบาลนครนนทบุรี • ระบบจัดการขยะไร้บ้าน</p>
    </div>


    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-slate-900/90 backdrop-blur-xl py-8 px-6 shadow-2xl border border-slate-800 rounded-3xl sm:px-10">

            <!-- Flash Alert -->
            <?php if (!empty($flash)): ?>
                <div class="mb-6 p-4 rounded-xl border flex items-center gap-3 text-xs font-medium <?= $flash['type'] === 'danger' ? 'bg-rose-950/50 border-rose-800 text-rose-300' : 'bg-emerald-950/50 border-emerald-800 text-emerald-300' ?>">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= htmlspecialchars($baseUrl ?: '') ?>/login" method="POST" class="space-y-5">
                <?= \App\Core\CSRF::field() ?>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">อีเมลผู้ดูแลระบบ (Email)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input type="email" name="email" id="emailInput" value="admin@waste.local" required placeholder="admin@waste.local"
                               class="w-full pl-10 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">รหัสผ่าน (Password)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" name="password" id="passwordInput" value="admin1234" required placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition duration-200 shadow-lg shadow-emerald-600/25 flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>เข้าสู่ระบบ Admin</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-800">
                <button type="button" onclick="fillAdminDemo()" class="w-full p-3 bg-slate-800 hover:bg-slate-700/80 border border-slate-700 rounded-xl text-xs transition flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        <span class="font-bold text-slate-200">เข้าใช้งานด้วยบัญชี Admin อัตโนมัติ</span>
                    </div>
                    <span class="text-[11px] text-emerald-400 font-mono">admin1234</span>
                </button>
            </div>

            <div class="mt-6 text-center">
                <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" class="text-xs text-slate-400 hover:text-emerald-400 transition flex items-center justify-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>กลับสู่หน้าหลักประชาชน</span>
                </a>
            </div>

        </div>
    </div>

    <script>
        lucide.createIcons();
        function fillAdminDemo() {
            document.getElementById('emailInput').value = 'admin@waste.local';
            document.getElementById('passwordInput').value = 'admin1234';
        }
    </script>
</body>
</html>
