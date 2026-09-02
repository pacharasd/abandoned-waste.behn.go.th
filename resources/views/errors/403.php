<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 ปฏิเสธการเข้าถึง | ระบบแจ้งจัดเก็บขยะไร้บ้าน</title>
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center bg-white p-8 rounded-2xl shadow-xl border border-slate-100">
        <div class="w-20 h-20 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold">
            403
        </div>
        <h1 class="text-2xl font-bold text-slate-800 mb-2">ไม่มีสิทธิ์เข้าถึงหน้านี้</h1>
        <p class="text-slate-500 mb-6">คุณไม่มีสิทธิ์ในการเข้าถึงทรัพยากรส่วนนี้ หากคิดว่านี่เป็นข้อผิดพลาด กรุณาติดต่อผู้ดูแลระบบ</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition duration-200 shadow-md shadow-emerald-600/20">
                กลับสู่หน้าหลัก
            </a>
            <a href="<?= htmlspecialchars($baseUrl ?: '') ?>/login" class="inline-flex items-center justify-center px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition duration-200">
                เข้าสู่ระบบ
            </a>
        </div>
    </div>
</body>
</html>
