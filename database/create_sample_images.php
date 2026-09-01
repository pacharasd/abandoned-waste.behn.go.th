<?php
/**
 * Generate Sample Before/After Images using PHP GD
 */

$uploadDir = __DIR__ . '/../public/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$samples = [
    'sample_before_1.jpg' => ['title' => 'จุดทิ้งที่นอนเก่าและเก้าอี้ชำรุด', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [180, 83, 9]],
    'sample_before_2.jpg' => ['title' => 'กองเศษอาหารเน่าเสียใต้สะพาน', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [185, 28, 28]],
    'sample_before_3.jpg' => ['title' => 'กองขยะถุงพลาสติกและกล่องกระดาษ', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [161, 98, 7]],
    'sample_before_4.jpg' => ['title' => 'กองขวดพลาสติกและกระป๋องริมรั้ว', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [29, 78, 216]],
    'sample_before_5.jpg' => ['title' => 'หลอดไฟแตกและแบตเตอรี่เก่า', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [190, 24, 93]],
    'sample_after_5.jpg'  => ['title' => 'พื้นที่จัดเก็บและทำความสะอาดแล้ว', 'subtitle' => '✅ รูปหลังจัดเก็บ (After)', 'bg' => [4, 120, 87]],
    'sample_before_6.jpg' => ['title' => 'กองเศษกิ่งไม้และขยะอุดตันท่อ', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [71, 85, 105]],
    'sample_after_6.jpg'  => ['title' => 'ลอกท่อและกวาดล้างถนนเรียบร้อย', 'subtitle' => '✅ รูปหลังจัดเก็บ (After)', 'bg' => [13, 148, 136]],
    'sample_before_7.jpg' => ['title' => 'กองยางรถยนต์เก่าและชิ้นส่วนมอเตอร์ไซค์', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [30, 41, 59]],
    'sample_before_8.jpg' => ['title' => 'ซากชุดตรวจ ATK และหน้ากากอนามัยเก่า', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [159, 18, 57]],
    'sample_before_9.jpg' => ['title' => 'กองเสื้อผ้าเก่าและเศษผ้าชำรุด', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [67, 56, 202]],
    'sample_before_10.jpg' => ['title' => 'ตุ๊กตานางรำและเครื่องสักการะชำรุดริมรั้ววัด', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [120, 53, 15]],
    'sample_before_11.jpg' => ['title' => 'โซฟาหนังเก่าและฟองน้ำชำรุด', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [133, 77, 14]],
    'sample_after_11.jpg'  => ['title' => 'จัดเก็บโซฟาและเคลียร์พื้นที่หน้าซอยเรียบร้อย', 'subtitle' => '✅ รูปหลังจัดเก็บ (After)', 'bg' => [16, 185, 129]],
    'sample_before_12.jpg' => ['title' => 'กล่องโฟมและแก้วพลาสติกหน้าตลาดสด', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [202, 138, 4]],
    'sample_after_12.jpg'  => ['title' => 'กวาดล้างและฆ่าเชื้อจุดทิ้งขยะหน้าตลาดสด', 'subtitle' => '✅ รูปหลังจัดเก็บ (After)', 'bg' => [5, 150, 105]],
    'sample_before_13.jpg' => ['title' => 'ซองขนมกรอบและถุงพลาสติกสะสมริมคลอง', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [217, 119, 6]],
    'sample_after_13.jpg'  => ['title' => 'ตักเก็บขยะริมคลองและทำความสะอาดตลิ่ง', 'subtitle' => '✅ รูปหลังจัดเก็บ (After)', 'bg' => [4, 120, 87]],
    'sample_before_14.jpg' => ['title' => 'เศษลูกฟุตบอล อุปกรณ์กีฬาแตกหัก', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [79, 70, 229]],
    'sample_after_14.jpg'  => ['title' => 'จัดเก็บอุปกรณ์กีฬาชำรุดลานชุมชนเรียบร้อย', 'subtitle' => '✅ รูปหลังจัดเก็บ (After)', 'bg' => [16, 185, 129]],
    'sample_before_15.jpg' => ['title' => 'ยาหมดอายุและซองกันชื้นกองริมกำแพง', 'subtitle' => '📷 รูปก่อนจัดเก็บ (Before)', 'bg' => [225, 29, 72]],
];

foreach ($samples as $filename => $info) {
    $width = 600;
    $height = 400;
    $img = imagecreatetruecolor($width, $height);

    $bgColor = imagecolorallocate($img, $info['bg'][0], $info['bg'][1], $info['bg'][2]);
    $white = imagecolorallocate($img, 255, 255, 255);
    $dark = imagecolorallocate($img, 15, 23, 42);

    imagefill($img, 0, 0, $bgColor);

    // Draw header box
    imagefilledrectangle($img, 25, 25, $width - 25, $height - 25, imagecolorallocate($img, 255, 255, 255));
    imagefilledrectangle($img, 35, 35, $width - 35, 95, $bgColor);

    // Draw text
    imagestring($img, 5, 50, 55, "WASTE REPORT PHOTO - " . strtoupper(str_replace('.jpg', '', $filename)), $white);
    imagestring($img, 5, 50, 140, "Status: " . $info['subtitle'], $dark);
    imagestring($img, 4, 50, 180, "Detail: " . $info['title'], $dark);
    imagestring($img, 3, 50, 240, "Area: Nonthaburi Municipality (Bangkok Metropolitan)", $dark);
    imagestring($img, 3, 50, 270, "Date: " . date('Y-m-d H:i:s'), $dark);

    imagejpeg($img, $uploadDir . $filename, 90);
    imagedestroy($img);
    echo "Created image: {$filename}\n";
}
