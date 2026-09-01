/**
 * Generate Sample Images for uploads
 */
const fs = require('fs');
const path = require('path');

const uploadDir = path.join(__dirname, '..', 'public', 'uploads');
if (!fs.existsSync(uploadDir)) {
    fs.mkdirSync(uploadDir, { recursive: true });
}

const samples = [
    { name: 'sample_before_1.jpg', title: 'จุดทิ้งที่นอนเก่าและเก้าอี้ชำรุด', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#b45309' },
    { name: 'sample_before_2.jpg', title: 'กองเศษอาหารเน่าเสียใต้สะพาน', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#b91c1c' },
    { name: 'sample_before_3.jpg', title: 'กองขยะถุงพลาสติกและกล่องกระดาษ', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#a16207' },
    { name: 'sample_before_4.jpg', title: 'กองขวดพลาสติกและกระป๋องริมรั้ว', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#1d4ed8' },
    { name: 'sample_before_5.jpg', title: 'หลอดไฟแตกและแบตเตอรี่เก่า', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#be185d' },
    { name: 'sample_after_5.jpg',  title: 'พื้นที่จัดเก็บและทำความสะอาดแล้ว', status: '✅ หลังจัดเก็บ (After)', bg: '#047857' },
    { name: 'sample_before_6.jpg', title: 'กองเศษกิ่งไม้และขยะอุดตันท่อ', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#475569' },
    { name: 'sample_after_6.jpg',  title: 'ลอกท่อและกวาดล้างถนนเรียบร้อย', status: '✅ หลังจัดเก็บ (After)', bg: '#0f766e' },
    { name: 'sample_before_7.jpg', title: 'กองยางรถยนต์เก่าและชิ้นส่วนมอเตอร์ไซค์', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#1e293b' },
    { name: 'sample_before_8.jpg', title: 'ซากชุดตรวจ ATK และหน้ากากอนามัยเก่า', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#9f1239' },
    { name: 'sample_before_9.jpg', title: 'กองเสื้อผ้าเก่าและเศษผ้าชำรุด', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#4338ca' },
    { name: 'sample_before_10.jpg', title: 'ตุ๊กตานางรำและเครื่องสักการะชำรุด', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#78350f' },
    { name: 'sample_before_11.jpg', title: 'โซฟาหนังเก่าและฟองน้ำชำรุด', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#854d0e' },
    { name: 'sample_after_11.jpg',  title: 'จัดเก็บโซฟาและทำความสะอาดพื้นที่เรียบร้อย', status: '✅ หลังจัดเก็บ (After)', bg: '#10b981' },
    { name: 'sample_before_12.jpg', title: 'กล่องโฟมและแก้วพลาสติกหน้าตลาดสด', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#ca8a04' },
    { name: 'sample_after_12.jpg',  title: 'กวาดล้างและฆ่าเชื้อจุดทิ้งขยะหน้าตลาดสด', status: '✅ หลังจัดเก็บ (After)', bg: '#059669' },
    { name: 'sample_before_13.jpg', title: 'ซองขนมกรอบและถุงพลาสติกสะสมริมคลอง', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#d97706' },
    { name: 'sample_after_13.jpg',  title: 'ตักเก็บขยะริมคลองและทำความสะอาดตลิ่ง', status: '✅ หลังจัดเก็บ (After)', bg: '#047857' },
    { name: 'sample_before_14.jpg', title: 'เศษลูกฟุตบอล อุปกรณ์กีฬาแตกหัก', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#4f46e5' },
    { name: 'sample_after_14.jpg',  title: 'จัดเก็บอุปกรณ์กีฬาชำรุดลานชุมชนเรียบร้อย', status: '✅ หลังจัดเก็บ (After)', bg: '#10b981' },
    { name: 'sample_before_15.jpg', title: 'ยาหมดอายุและซองกันชื้นกองริมกำแพง', status: '📷 ก่อนจัดเก็บ (Before)', bg: '#e11d48' },
];

samples.forEach(s => {
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400">
        <rect width="600" height="400" fill="${s.bg}"/>
        <rect x="20" y="20" width="560" height="360" rx="16" fill="#ffffff" opacity="0.96"/>
        <rect x="40" y="40" width="520" height="60" rx="12" fill="${s.bg}"/>
        <text x="60" y="78" font-family="'Kanit', 'Prompt', sans-serif" font-size="18" font-weight="bold" fill="#ffffff">${s.name.toUpperCase()}</text>
        <text x="60" y="150" font-family="'Kanit', 'Prompt', sans-serif" font-size="20" font-weight="bold" fill="#0f172a">${s.status}</text>
        <text x="60" y="195" font-family="'Kanit', 'Prompt', sans-serif" font-size="16" fill="#334155">${s.title}</text>
        <text x="60" y="250" font-family="'Kanit', 'Prompt', sans-serif" font-size="14" fill="#64748b">พื้นที่: เทศบาลนครนนทบุรี / กรุงเทพฯ และปริมณฑล</text>
        <text x="60" y="280" font-family="'Kanit', 'Prompt', sans-serif" font-size="14" fill="#64748b">บันทึกข้อมูล: ระบบจัดการขยะไร้บ้าน</text>
        <circle cx="500" cy="310" r="30" fill="${s.bg}" opacity="0.2"/>
        <text x="488" y="318" font-family="sans-serif" font-size="24">📸</text>
    </svg>`;

    fs.writeFileSync(path.join(uploadDir, s.name), svg);
    console.log('Created sample image:', s.name);
});
