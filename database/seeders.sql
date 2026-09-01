-- Database Seeders: behn_abandoned_waste
-- Consolidated Admin-Managed Dataset for Homeless Waste Collection System (Nonthaburi Municipality)

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `activity_logs`;
TRUNCATE TABLE `notifications`;
TRUNCATE TABLE `status_histories`;
TRUNCATE TABLE `assignments`;
TRUNCATE TABLE `waste_report_images`;
TRUNCATE TABLE `waste_report_items`;
TRUNCATE TABLE `waste_reports`;
TRUNCATE TABLE `collection_schedules`;
TRUNCATE TABLE `waste_types`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Seed Users (Admin Only)
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'ผู้ดูแลระบบหลัก (Admin)', 'admin@waste.local', '081-111-2222', '$2y$10$RwHoXSrRtAed0NJqqW.Cw.lza0XZp9GKIJ0n6rckbTyi65dZG6LnK', 'admin', NOW(), NOW());

-- 2. Seed Waste Types (12 Orphan Waste Categories - Nonthaburi Municipality)
INSERT INTO `waste_types` (`id`, `name`, `description`, `icon`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ภาชนะเมลามีน โฟม และพลาสติกใส่อาหาร', 'ชามเมลามีน, กล่องโฟม, ขวดพลาสติก, แก้วพลาสติก, กล่องอาหาร, ช้อนพลาสติก (ล้างสะอาดก่อนส่ง)', 'utensils', 'assets/images/waste_types/type_1.jpg', 1, NOW(), NOW()),
(2, 'ถุงพลาสติกกรอบ ซองขนม และฟอยล์', 'ถุงพลาสติกกรอบ, ถุงบรรจุอาหาร, ซองขนม, ฟอยล์ทุกชนิด, ซองเครื่องปรุง (ล้างสะอาดและผึ่งแห้ง)', 'package', 'assets/images/waste_types/type_2.jpg', 1, NOW(), NOW()),
(3, 'ยางยานพาหนะ (ยางรถยนต์/มอเตอร์ไซค์/จักรยาน)', 'ยางรถยนต์, ยางมอเตอร์ไซค์, ยางในรถจักรยาน (ที่ตัดจุ๊บโลหะออกแล้ว)', 'disc', 'assets/images/waste_types/type_3.jpg', 1, NOW(), NOW()),
(4, 'ที่นอน โซฟา ฟองน้ำ และวิกผม', 'ท่อนโซฟา, ฟองน้ำโซฟา, ที่นอนเก่า, วิกผม, เรซิ่นงานฝีมือ', 'armchair', 'assets/images/waste_types/type_4.jpg', 1, NOW(), NOW()),
(5, 'ของใช้สุขอนามัยประจำวัน', 'ไม้ปั่นหู, ไม้จิ้มฟัน, ไหมขัดฟัน, แปรงสีฟัน, หลอดยาสีฟัน', 'sparkles', 'assets/images/waste_types/type_5.jpg', 1, NOW(), NOW()),
(6, 'เครื่องเขียนและบัตรพลาสติกแข็ง', 'ปากกาพลาสติก, ปากกาลบคำผิด, บัตร ATM, บัตรเครดิต, บัตรพนักงาน', 'credit-card', 'assets/images/waste_types/type_6.jpg', 1, NOW(), NOW()),
(7, 'สิ่งทอ เสื้อผ้า และซิลิโคน', 'ซิลิโคนเสริมอก, เสื้อผ้าเก่าชำรุด และเศษผ้าที่ไม่ใช้แล้ว', 'shirt', 'assets/images/waste_types/type_7.jpg', 1, NOW(), NOW()),
(8, 'สิ่งสักการะและเครื่องใช้ในบ้าน', 'ตุ๊กตานางรำที่ศาลต่างๆ, กระดาษเงินกระดาษทอง, ก้านธูป, พวงมาลัยปลอม, ผ้าสามสี', 'home', 'assets/images/waste_types/type_8.jpg', 1, NOW(), NOW()),
(9, 'อุปกรณ์กีฬาและของเล่น', 'ลูกขนไก่, ลูกฟุตบอล, ลูกเทนนิส, ลูกกอล์ฟ, ดินน้ำมัน, แป้งโด, หมากฝรั่ง', 'trophy', 'assets/images/waste_types/type_9.jpg', 1, NOW(), NOW()),
(10, 'ฟิล์มและรูปถ่ายเก่า', 'ฟิล์มถ่ายภาพ, รูปถ่ายเก่าๆ, ฟิล์มเอกซเรย์', 'film', 'assets/images/waste_types/type_10.jpg', 1, NOW(), NOW()),
(11, 'ของแห้ง ยาหมดอายุ และซองกันชื้น', 'ซองกันชื้น, ยาเม็ด/ผง, อาหารแห้ง, อาหารหรือของใช้หมดอายุ', 'pill', 'assets/images/waste_types/type_11.jpg', 1, NOW(), NOW()),
(12, 'ชุดตรวจและเวชภัณฑ์ผู้ไม่ป่วย', 'ชุดตรวจ ATK (ผู้ไม่ป่วย), ชุดตรวจครรภ์, หน้ากากอนามัย (ผู้ไม่ป่วย), เจลลดไข้, พลาสเตอร์ยา, ถุงมือยาง', 'shield-check', 'assets/images/waste_types/type_12.jpg', 1, NOW(), NOW());

-- 3. Seed Collection Schedules (Monthly Waste Collection Cycles)
INSERT INTO `collection_schedules` (`id`, `title`, `collection_date`, `start_time`, `end_time`, `area_zone`, `cutoff_date`, `description`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'รอบจัดเก็บขยะไร้บ้าน ประจำเดือนกรกฎาคม 2569', '2026-07-26', '09:00:00', '16:00:00', 'ทุกชุมชนและตำบลในเขตเทศบาลนครนนทบุรี', '2026-07-24 18:00:00', 'รอบจัดเก็บประจำเดือนกรกฎาคม ดำเนินการจัดเก็บเสร็จสมบูรณ์เรียบร้อยแล้ว', 'completed', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY)),
(2, 'รอบจัดเก็บขยะไร้บ้าน ประจำเดือนสิงหาคม 2569', '2026-08-30', '09:00:00', '16:00:00', 'ทุกชุมชนและตำบลในเขตเทศบาลนครนนทบุรี', '2026-08-28 18:00:00', 'รอบจัดเก็บประจำเดือนสิงหาคม ดำเนินการจัดเก็บเสร็จสมบูรณ์เรียบร้อยแล้ว นำส่งกำจัดถูกต้องตามหลักสุขาภิบาล', 'completed', 1, DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 'รอบจัดเก็บขยะไร้บ้านและขยะชิ้นใหญ่ ประจำเดือนกันยายน 2569', '2026-09-27', '09:00:00', '16:00:00', 'ทุกชุมชนและตำบลในเขตเทศบาลนครนนทบุรี', '2026-09-25 18:00:00', 'เปิดรับแจ้งล่วงหน้าสำหรับรอบจัดเก็บประจำเดือนกันยายน เจ้าหน้าที่จะเริ่มลงพื้นที่ตั้งแต่ 09:00 น. เป็นต้นไป กรุณานำขยะมาวางหน้าอาคารก่อนเวลา 08:30 น.', 'active', 1, DATE_SUB(NOW(), INTERVAL 5 DAY), NOW()),
(4, 'รอบจัดเก็บขยะไร้บ้าน ประจำเดือนตุลาคม 2569', '2026-10-25', '09:00:00', '16:00:00', 'ทุกชุมชนและตำบลในเขตเทศบาลนครนนทบุรี', '2026-10-23 18:00:00', 'รอบจัดเก็บประจำเดือนตุลาคม 2569 เตรียมเปิดรับแจ้งล่วงหน้า', 'upcoming', 1, NOW(), NOW()),
(5, 'รอบจัดเก็บขยะไร้บ้าน ประจำเดือนพฤศจิกายน 2569', '2026-11-29', '09:00:00', '16:00:00', 'ทุกชุมชนและตำบลในเขตเทศบาลนครนนทบุรี', '2026-11-27 18:00:00', 'รอบจัดเก็บประจำเดือนพฤศจิกายน 2569', 'upcoming', 1, NOW(), NOW()),
(6, 'รอบจัดเก็บขยะไร้บ้าน ประจำเดือนธันวาคม 2569', '2026-12-27', '09:00:00', '16:00:00', 'ทุกชุมชนและตำบลในเขตเทศบาลนครนนทบุรี', '2026-12-25 18:00:00', 'รอบส่งท้ายปี 2569 จัดเก็บพร้อมบิ๊กคลีนนิ่งทั่วเทศบาลนครนนทบุรี', 'upcoming', 1, NOW(), NOW());

-- 4. Seed Waste Reports (15 realistic reports across Nonthaburi, managed directly by Admin)
INSERT INTO `waste_reports` (`id`, `report_number`, `reporter_name`, `reporter_phone`, `address`, `latitude`, `longitude`, `waste_type_id`, `collection_schedule_id`, `estimated_weight`, `actual_weight`, `description`, `status`, `assigned_staff_id`, `submitted_at`, `completed_at`, `created_at`, `updated_at`) VALUES
-- 1. รอรับเรื่อง
(1, 'WB-2026-000001', 'คุณกนกวรรณ จิตอาสา', '089-123-4567', 'ถ.รัตนาธิเบศร์ หน้าศูนย์ราชการจังหวัดนนทบุรี ใกล้ทางขึ้น MRT ศูนย์ราชการนนทบุรี ต.บางกระสอ อ.เมืองนนทบุรี', 13.8617000, 100.5133000, 4, 3, 65.00, NULL, 'มีฟูกที่นอนเก่าและเก้าอี้พังถูกนำมาทิ้งไว้ข้างทางเท้า ขวางทางเดินคนตาบอด', 'รอรับเรื่อง', NULL, DATE_SUB(NOW(), INTERVAL 1 HOUR), NULL, DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_SUB(NOW(), INTERVAL 1 HOUR)),

-- 2. รอรับเรื่อง
(2, 'WB-2026-000002', 'นายประสิทธิ์ สวนใหญ่', '081-445-6677', 'ท่าน้ำนนทบุรี ถ.ประชาราษฎร์ หน้าหอนาฬิกา ต.สวนใหญ่ อ.เมืองนนทบุรี', 13.8423000, 100.4912000, 1, 3, 28.00, NULL, 'มีกล่องโฟมและแก้วพลาสติกจากร้านค้ากองสะสมริมเขื่อนแม่น้ำเจ้าพระยา', 'รอรับเรื่อง', NULL, DATE_SUB(NOW(), INTERVAL 3 HOUR), NULL, DATE_SUB(NOW(), INTERVAL 3 HOUR), DATE_SUB(NOW(), INTERVAL 3 HOUR)),

-- 3. รอรับเรื่อง
(3, 'WB-2026-000003', 'นายธีรภัทร ชุมชนสุขใจ', '081-987-6543', 'ถ.ติวานนท์ ใกล้แยกแคราย หน้าปากซอยติวานนท์ 18 ต.ตลาดขวัญ อ.เมืองนนทบุรี', 13.8589000, 100.5221000, 3, 3, 85.00, NULL, 'มียางรถยนต์เก่าและชิ้นส่วนยางมอเตอร์ไซค์กองอยู่ข้างเสาไฟฟ้า เกรงว่าจะเป็นแหล่งเพาะพันธุ์ยุงลาย', 'รอรับเรื่อง', NULL, DATE_SUB(NOW(), INTERVAL 5 HOUR), NULL, DATE_SUB(NOW(), INTERVAL 5 HOUR), DATE_SUB(NOW(), INTERVAL 3 HOUR)),

-- 4. กำลังตรวจสอบ
(4, 'WB-2026-000004', 'นางวิไลพร วัดบัวขวัญ', '086-778-9900', 'ซอยงามวงศ์วาน 23 ซอยวัดบัวขวัญพระอารามหลวง ต.บางเขน อ.เมืองนนทบุรี', 13.8698000, 100.5365000, 8, 3, 40.00, NULL, 'ตุ๊กตานางรำเก่า ศาลพระภูมิเก่า พวงมาลัยแห้งและก้านธูปกองอยู่ใต้ต้นโพธิ์ริมกำแพงวัด', 'กำลังตรวจสอบ', NULL, DATE_SUB(NOW(), INTERVAL 7 HOUR), NULL, DATE_SUB(NOW(), INTERVAL 7 HOUR), DATE_SUB(NOW(), INTERVAL 4 HOUR)),

-- 5. กำลังตรวจสอบ
(5, 'WB-2026-000005', 'นางสมศรี มีสุข', '086-555-4321', 'ตลาดสดเทศบาลนครนนทบุรี ซอยพิบูลสงคราม 22 ต.สวนใหญ่ อ.เมืองนนทบุรี', 13.8395000, 100.4950000, 1, 3, 120.00, NULL, 'กล่องโฟม ชามเมลามีน และถุงพลาสติกบรรจุอาหารสะสมปริมาณมากช่วงสุดสัปดาห์', 'กำลังตรวจสอบ', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 8 HOUR)),

-- 6. กำลังตรวจสอบ
(6, 'WB-2026-000006', 'นายสุรชัย ท่าทราย', '089-667-1122', 'ถ.พิบูลสงคราม ใต้สะพานพระราม 5 ต.สวนใหญ่ อ.เมืองนนทบุรี', 13.8298000, 100.5042000, 7, 3, 55.00, NULL, 'กองเสื้อผ้าเก่า กางเกงยีนส์ชำรุด และเศษผ้าห่มนำมาทิ้งไว้ข้างตอม่อสะพาน', 'กำลังตรวจสอบ', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 6 HOUR)),

-- 7. กำลังดำเนินการ
(7, 'WB-2026-000007', 'นายเกรียงไกร นครอินทร์', '083-441-2233', 'ถ.นครอินทร์ ใกล้วงเวียนพระราม 5 ต.บางขุนกอง อ.บางกรวย นนทบุรี', 13.8245000, 100.4720000, 3, 3, 90.00, NULL, 'ยางรถยนต์เก่า 8 เส้น ยางมอเตอร์ไซค์ 5 เส้น กองอยู่ริมไหล่ทางถนนนครอินทร์', 'กำลังดำเนินการ', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 5 HOUR)),

-- 8. กำลังดำเนินการ
(8, 'WB-2026-000008', 'พยาบาลจิตอาสา นนทบุรี', '082-114-5566', 'ซอยประชาราษฎร์ 26 ใกล้โรงพยาบาลส่งเสริมสุขภาพตำบลบางซื่อ ต.สวนใหญ่', 13.8312000, 100.5110000, 12, 3, 15.00, NULL, 'มีกล่องทิ้งซากชุดตรวจ ATK และหน้ากากอนามัยที่ใช้แล้วจากจุดตรวจชุมชน มัดใส่ถุงแดงอย่างดี ต้องการให้จัดเก็บตามหลักอนามัย', 'กำลังดำเนินการ', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 3 HOUR)),

-- 9. กำลังดำเนินการ
(9, 'WB-2026-000009', 'นายอนุชา พิทักษ์เมือง', '085-444-3322', 'ซอยเรวดี 45 ลานกีฬาและสนามเด็กเล่นชุมชน ต.ตลาดขวัญ อ.เมืองนนทบุรี', 13.8543000, 100.5089000, 9, 3, 45.00, NULL, 'ลูกฟุตบอล ลูกบาสแตก และของเล่นพลาสติกหักพัง ชำรุดใช้งานไม่ได้ กองอยู่ข้างอัฒจันทร์', 'กำลังดำเนินการ', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 HOUR)),

-- 10. กำลังดำเนินการ
(10, 'WB-2026-000010', 'นางสาวปรียา รักสะอาด', '087-777-8899', 'ชุมชนวัดเขมาภิรตารามราชวรวิหาร ถ.พิบูลสงคราม ต.สวนใหญ่ อ.เมืองนนทบุรี', 13.8188000, 100.5135000, 11, 3, 20.00, NULL, 'ยาเม็ดหมดอายุและซองกันชื้นจากบ้านเรือนและร้านยาเก่า นำมารวบรวมไว้ให้เทศบาลจัดเก็บไปทำลาย', 'กำลังดำเนินการ', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 HOUR)),

-- 11. จัดเก็บเรียบร้อยแล้ว
(11, 'WB-2026-000011', 'นายธนาวัฒน์ งามวงศ์วาน', '081-332-9988', 'แยกพงษ์เพชร ถ.งามวงศ์วาน หน้าอาคารพาณิชย์เก่า ต.บางเขน อ.เมืองนนทบุรี', 13.8574000, 100.5428000, 4, 2, 75.00, 72.50, 'โซฟาหนังเทียมเก่าขาดและเศษฟองน้ำเบาะรถ ถูกนำมาทิ้งกองไว้', 'จัดเก็บเรียบร้อยแล้ว', NULL, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- 12. จัดเก็บเรียบร้อยแล้ว
(12, 'WB-2026-000012', 'นางพิมพา กระทรวงสาธารณสุข', '084-556-7788', 'ซอยติวานนท์ 4 ใกล้ทางเข้า MRT กระทรวงสาธารณสุข ต.ตลาดขวัญ อ.เมืองนนทบุรี', 13.8480000, 100.5205000, 1, 2, 50.00, 53.00, 'กล่องโฟมใส่อาหารและแก้วพลาสติกใช้แล้วจำนวนมากจากงานออกร้าน', 'จัดเก็บเรียบร้อยแล้ว', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY)),

-- 13. จัดเก็บเรียบร้อยแล้ว
(13, 'WB-2026-000013', 'นายสมคิด ชุมชนริมคลอง', '086-123-9900', 'ริมคลองบางกร่าง ซอยบางกร่าง 45 ต.บางกร่าง อ.เมืองนนทบุรี', 13.8471000, 100.4673000, 2, 2, 35.00, 38.20, 'ซองขนมกรอบ ซองฟอยล์ และถุงพลาสติกสะสมริมตลิ่งคลอง', 'จัดเก็บเรียบร้อยแล้ว', NULL, DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- 14. จัดเก็บเรียบร้อยแล้ว
(14, 'WB-2026-000014', 'นายดำรง บางศรีเมือง', '089-887-2211', 'สวนสาธารณะเฉลิมพระเกียรติ ต.บางศรีเมือง อ.เมืองนนทบุรี', 13.8402000, 100.4815000, 9, 1, 40.00, 42.00, 'เศษลูกฟุตบอลเก่า ลูกเทนนิส และชิ้นส่วนเครื่องเล่นเด็กที่แตกหัก', 'จัดเก็บเรียบร้อยแล้ว', NULL, DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 34 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 34 DAY)),

-- 15. ยกเลิก
(15, 'WB-2026-000015', 'นายอดิศร แจ้งวัฒนะ', '082-990-1122', 'ถ.แจ้งวัฒนะ หน้าไซต์งานก่อสร้าง ต.คลองเกลือ อ.ปากเกร็ด นนทบุรี', 13.8992000, 100.5489000, 1, 3, 300.00, NULL, 'เศษอิฐ หัก ปูนซีเมนต์ และกระเบื้องปูพื้นจากการทุบบ้าน', 'ยกเลิก', NULL, DATE_SUB(NOW(), INTERVAL 8 HOUR), NULL, DATE_SUB(NOW(), INTERVAL 8 HOUR), DATE_SUB(NOW(), INTERVAL 6 HOUR));

-- 5. Seed Waste Report Items (Breakdown per Report)
INSERT INTO `waste_report_items` (`id`, `waste_report_id`, `waste_type_id`, `estimated_weight`, `actual_weight`, `created_at`, `updated_at`) VALUES
-- Report 1
(1, 1, 4, 50.00, NULL, NOW(), NOW()),
(2, 1, 6, 15.00, NULL, NOW(), NOW()),

-- Report 2
(3, 2, 1, 20.00, NULL, NOW(), NOW()),
(4, 2, 2, 8.00, NULL, NOW(), NOW()),

-- Report 3
(5, 3, 3, 85.00, NULL, NOW(), NOW()),

-- Report 4
(6, 4, 8, 40.00, NULL, NOW(), NOW()),

-- Report 5
(7, 5, 1, 80.00, NULL, NOW(), NOW()),
(8, 5, 2, 40.00, NULL, NOW(), NOW()),

-- Report 6
(9, 6, 7, 55.00, NULL, NOW(), NOW()),

-- Report 7
(10, 7, 3, 90.00, NULL, NOW(), NOW()),

-- Report 8
(11, 8, 12, 10.00, NULL, NOW(), NOW()),
(12, 8, 5, 5.00, NULL, NOW(), NOW()),

-- Report 9
(13, 9, 9, 45.00, NULL, NOW(), NOW()),

-- Report 10
(14, 10, 11, 20.00, NULL, NOW(), NOW()),

-- Report 11 (จัดเก็บแล้ว)
(15, 11, 4, 75.00, 72.50, NOW(), NOW()),

-- Report 12 (จัดเก็บแล้ว)
(16, 12, 1, 50.00, 53.00, NOW(), NOW()),

-- Report 13 (จัดเก็บแล้ว)
(17, 13, 2, 35.00, 38.20, NOW(), NOW()),

-- Report 14 (จัดเก็บแล้ว)
(18, 14, 9, 40.00, 42.00, NOW(), NOW()),

-- Report 15 (ยกเลิก)
(19, 15, 1, 300.00, NULL, NOW(), NOW());

-- 6. Seed Report Images (Before & After)
INSERT INTO `waste_report_images` (`id`, `waste_report_id`, `image_path`, `image_type`, `created_at`, `updated_at`) VALUES
(1, 1, 'uploads/sample_before_1.jpg', 'before', NOW(), NOW()),
(2, 2, 'uploads/sample_before_2.jpg', 'before', NOW(), NOW()),
(3, 3, 'uploads/sample_before_3.jpg', 'before', NOW(), NOW()),
(4, 4, 'uploads/sample_before_4.jpg', 'before', NOW(), NOW()),
(5, 5, 'uploads/sample_before_5.jpg', 'before', NOW(), NOW()),
(6, 6, 'uploads/sample_before_6.jpg', 'before', NOW(), NOW()),
(7, 7, 'uploads/sample_before_7.jpg', 'before', NOW(), NOW()),
(8, 8, 'uploads/sample_before_8.jpg', 'before', NOW(), NOW()),
(9, 9, 'uploads/sample_before_9.jpg', 'before', NOW(), NOW()),
(10, 10, 'uploads/sample_before_10.jpg', 'before', NOW(), NOW()),
(11, 11, 'uploads/sample_before_11.jpg', 'before', NOW(), NOW()),
(12, 11, 'uploads/sample_after_11.jpg', 'after', NOW(), NOW()),
(13, 12, 'uploads/sample_before_12.jpg', 'before', NOW(), NOW()),
(14, 12, 'uploads/sample_after_12.jpg', 'after', NOW(), NOW()),
(15, 13, 'uploads/sample_before_13.jpg', 'before', NOW(), NOW()),
(16, 13, 'uploads/sample_after_13.jpg', 'after', NOW(), NOW()),
(17, 14, 'uploads/sample_before_14.jpg', 'before', NOW(), NOW()),
(18, 14, 'uploads/sample_after_14.jpg', 'after', NOW(), NOW()),
(19, 15, 'uploads/sample_before_15.jpg', 'before', NOW(), NOW());

-- 7. Seed Status Histories (Admin Actions Timeline)
INSERT INTO `status_histories` (`id`, `waste_report_id`, `old_status`, `new_status`, `changed_by`, `note`, `created_at`) VALUES
-- Report 1
(1, 1, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
-- Report 2
(2, 2, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
-- Report 3
(3, 3, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
-- Report 4
(4, 4, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 7 HOUR)),
(5, 4, 'รอรับเรื่อง', 'กำลังตรวจสอบ', 1, 'Admin ตรวจสอบพิกัดและประเภทสิ่งสักการะ', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
-- Report 5
(6, 5, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(7, 5, 'รอรับเรื่อง', 'กำลังตรวจสอบ', 1, 'Admin รับเรื่องและตรวจสอบปริมาณกล่องโฟมหน้าตลาดสด', DATE_SUB(NOW(), INTERVAL 8 HOUR)),
-- Report 6
(8, 6, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(9, 6, 'รอรับเรื่อง', 'กำลังตรวจสอบ', 1, 'Admin ตรวจสอบจุดทิ้งขยะใต้สะพานพระราม 5', DATE_SUB(NOW(), INTERVAL 6 HOUR)),
-- Report 7
(10, 7, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(11, 7, 'รอรับเรื่อง', 'กำลังดำเนินการ', 1, 'Admin อนุมัติและจัดรถบรรทุกเข้าจัดเก็บยางรถยนต์', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
-- Report 8
(12, 8, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(13, 8, 'รอรับเรื่อง', 'กำลังดำเนินการ', 1, 'Admin นำทีมเข้าจัดเก็บเวชภัณฑ์และชุดตรวจ ATK', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
-- Report 9
(14, 9, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(15, 9, 'รอรับเรื่อง', 'กำลังดำเนินการ', 1, 'Admin นำทีมเข้าเก็บกวาดอุปกรณ์กีฬาชำรุดลานกีฬาเรวดี', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
-- Report 10
(16, 10, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(17, 10, 'รอรับเรื่อง', 'กำลังดำเนินการ', 1, 'Admin กำลังดำเนินการบรรจุหีบห่อยาหมดอายุ', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
-- Report 11 (จัดเก็บแล้ว)
(18, 11, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(19, 11, 'รอรับเรื่อง', 'กำลังดำเนินการ', 1, 'Admin เข้าจัดเก็บโซฟาเก่าแยกพงษ์เพชร', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(20, 11, 'กำลังดำเนินการ', 'จัดเก็บเรียบร้อยแล้ว', 1, 'Admin บันทึกปิดงานและชั่งน้ำหนักจริง 72.50 กก.', DATE_SUB(NOW(), INTERVAL 3 DAY)),
-- Report 12 (จัดเก็บแล้ว)
(21, 12, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(22, 12, 'รอรับเรื่อง', 'จัดเก็บเรียบร้อยแล้ว', 1, 'Admin จัดเก็บกล่องโฟมและกวาดล้างถนนเรียบร้อย น้ำหนัก 53.00 กก.', DATE_SUB(NOW(), INTERVAL 4 DAY)),
-- Report 13 (จัดเก็บแล้ว)
(23, 13, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(24, 13, 'รอรับเรื่อง', 'จัดเก็บเรียบร้อยแล้ว', 1, 'Admin ตักเก็บขยะริมคลองบางกร่างเสร็จสิ้น น้ำหนัก 38.20 กก.', DATE_SUB(NOW(), INTERVAL 5 DAY)),
-- Report 14 (จัดเก็บแล้ว)
(25, 14, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 35 DAY)),
(26, 14, 'รอรับเรื่อง', 'จัดเก็บเรียบร้อยแล้ว', 1, 'Admin จัดเก็บอุปกรณ์กีฬาชำรุดสวนบางศรีเมืองเสร็จสิ้น น้ำหนัก 42.00 กก.', DATE_SUB(NOW(), INTERVAL 34 DAY)),
-- Report 15 (ยกเลิก)
(27, 15, NULL, 'รอรับเรื่อง', NULL, 'ประชาชนส่งเรื่องแจ้งผ่านเว็บไซต์', DATE_SUB(NOW(), INTERVAL 8 HOUR)),
(28, 15, 'รอรับเรื่อง', 'ยกเลิก', 1, 'ขอยกเลิกคำร้อง เนื่องจากเป็นเศษวัสดุก่อสร้าง (อิฐหินปูน) ไม่อยู่ในขอบข่ายขยะไร้บ้าน', DATE_SUB(NOW(), INTERVAL 6 HOUR));

-- 8. Seed Notifications (Admin Notifications)
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `related_type`, `related_id`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'new_report', 'มีรายการแจ้งขยะใหม่ [WB-2026-000001]', 'มีประชาชนแจ้งขอให้จัดเก็บขยะชิ้นใหญ่ (ที่นอนเก่า) บริเวณ หน้าศูนย์ราชการนนทบุรี', 'waste_report', 1, 0, NULL, DATE_SUB(NOW(), INTERVAL 1 HOUR), NOW()),
(2, 1, 'new_report', 'มีรายการแจ้งขยะใหม่ [WB-2026-000002]', 'มีประชาชนแจ้งขอให้จัดเก็บกล่องโฟมและพลาสติก บริเวณ ท่าน้ำนนทบุรี', 'waste_report', 2, 0, NULL, DATE_SUB(NOW(), INTERVAL 3 HOUR), NOW()),
(3, 1, 'new_report', 'มีรายการแจ้งขยะใหม่ [WB-2026-000003]', 'มีประชาชนแจ้งขอให้จัดเก็บยางรถยนต์ บริเวณ ถ.ติวานนท์', 'waste_report', 3, 0, NULL, DATE_SUB(NOW(), INTERVAL 5 HOUR), NOW()),
(4, 1, 'job_completed', 'จัดเก็บขยะเสร็จสมบูรณ์ [WB-2026-000011]', 'จัดเก็บโซฟาเก่าที่ แยกพงษ์เพชร เรียบร้อยแล้ว (น้ำหนักจริง 72.50 กก.)', 'waste_report', 11, 1, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), NOW()),
(5, 1, 'job_completed', 'จัดเก็บขยะเสร็จสมบูรณ์ [WB-2026-000012]', 'จัดเก็บกล่องโฟมที่ ตลาดขวัญ เรียบร้อยแล้ว (น้ำหนักจริง 53.00 กก.)', 'waste_report', 12, 1, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), NOW());

-- 9. Seed Activity Logs
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'login', 'Admin เข้าสู่ระบบสำเร็จ', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(2, 1, 'update_status', 'Admin ปรับสถานะ WB-2026-000004 เป็น กำลังตรวจสอบ', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(3, 1, 'update_status', 'Admin ปรับสถานะ WB-2026-000007 เป็น กำลังดำเนินการ', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(4, 1, 'complete_job', 'Admin บันทึกจัดเก็บ WB-2026-000011 สำเร็จพร้อมน้ำหนักจริง 72.50 กก.', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 1, 'complete_job', 'Admin บันทึกจัดเก็บ WB-2026-000012 สำเร็จพร้อมน้ำหนักจริง 53.00 กก.', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(6, 1, 'cancel_report', 'Admin ยกเลิกรายการ WB-2026-000015 (เศษอิฐปูนก่อสร้าง ไม่อยู่ในขอบข่ายขยะไร้บ้าน)', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 6 HOUR));
