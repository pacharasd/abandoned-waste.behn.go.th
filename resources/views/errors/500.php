<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 ข้อผิดพลาดของระบบ | ระบบแจ้งจัดเก็บขยะไร้บ้าน</title>
    <script <?= \App\Core\CSP::nonceAttr() ?> src="https://cdn.tailwindcss.com/3.4.16" integrity="sha384-mS5Uq7sE90lgbBDN8xgf34ibEgbZo4gB3tfLY40ZRle+M188BQw8onzNHg6GUZaA" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center bg-white p-8 rounded-2xl shadow-xl border border-slate-100">
        <div class="w-20 h-20 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl font-bold">
            500
        </div>
        <h1 class="text-2xl font-bold text-slate-800 mb-2">เกิดข้อผิดพลาดของระบบ</h1>
        <p class="text-slate-500 mb-6">ขออภัยในความไม่สะดวก ระบบกำลังประสบปัญหาชั่วคราวและได้รับการบันทึกไปยังเจ้าหน้าที่เรียบร้อยแล้ว</p>
        <a href="<?= htmlspecialchars($baseUrl ?: '/') ?>" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition duration-200 shadow-md shadow-emerald-600/20">
            กลับสู่หน้าหลัก
        </a>
    </div>
</body>
</html>
