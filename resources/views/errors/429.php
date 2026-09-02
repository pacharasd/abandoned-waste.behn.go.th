<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 ส่งคำขอถี่เกินไป | ระบบแจ้งจัดเก็บขยะไร้บ้าน</title>
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center bg-white p-8 rounded-2xl shadow-xl border border-slate-100">
        <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold">
            429
        </div>
        <h1 class="text-2xl font-bold text-slate-800 mb-2">ส่งคำขอถี่เกินกำหนด</h1>
        <p class="text-slate-500 mb-4">ระบบตรวจพบการส่งคำขอจำนวนมากในระยะเวลาสั้น เพื่อความปลอดภัยและความเสถียรของระบบ</p>
        <div class="p-3.5 bg-amber-50 rounded-xl text-amber-800 text-sm font-medium mb-6">
            ⏳ กรุณารอ<?= htmlspecialchars($waitText ?? 'สักครู่') ?> ก่อนทำรายการใหม่อีกครั้ง
        </div>
        <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition duration-200 shadow-md shadow-emerald-600/20">
            กลับสู่หน้าหลัก
        </a>
    </div>
</body>
</html>
