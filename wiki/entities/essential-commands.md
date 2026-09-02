---
type: entity
title: "Essential Commands & Operational CLI"
category: "tool"
aliases:
  - "Essential Commands"
  - "CLI Commands"
  - "คำสั่งที่ใช้บ่อย"
  - "Project Commands"
created: "2026-09-02"
updated: "2026-09-02"
tags:
  - type/entity
  - category/tool
  - domain/devops
  - domain/workflow
---

# Essential Commands & Operational CLI (คำสั่งสำคัญและจำเป็นใช้บ่อย)

> **Category:** `tool`  
> **Primary Role:** คู่มืออ้างอิงคำสั่งปฏิบัติการและบำรุงรักษาระบบ (Operational Cheat Sheet & Command Registry) ทั้งสำหรับ Developer และ LLM Agent

---

## 📋 ภาพรวม (Profile & Description)
หน้านี้รวบรวมคำสั่งสำคัญ (Essential CLI Commands) ที่ใช้เป็นประจำในการพัฒนา บำรุงรักษา ทดสอบระบบเว็บแอปพลิเคชัน **ระบบแจ้งจัดเก็บขยะไร้บ้าน** และการบริหารจัดการคลังความรู้ [[llm-wiki]] ให้ทำงานได้อย่างต่อเนื่อง แม่นยำ และปลอดภัย

---

## 🗄️ 1. ฐานข้อมูลและการเตรียมระบบ (Database & Migrations)

| คำสั่ง (Command) | หน้าที่ / รายละเอียด |
| :--- | :--- |
| `php database/migrate.php` | **รัน Migration & Seeders:** สร้างฐานข้อมูล `behn_abandoned_waste` (MySQL) พร้อมตารางหลักทั้ง 10 ตาราง และใส่ข้อมูลตั้งต้นสำหรับทดสอบ (User Admin, เจ้าหน้าที่เก็บขยะ 5 ทีม, ประเภทขยะ 12 หมวด, คำร้องทดสอบ) |
| `php database/create_sample_images.php` | **สร้างรูปภาพตัวอย่าง (PHP):** สร้างไฟล์ภาพ Before/After จำลองสำหรับรายการแจ้งเก็บขยะเพื่อใช้ในการทดสอบระบบ |
| `node database/create_sample_images.js` | **สร้างรูปภาพตัวอย่าง (Node.js):** รันสร้างไฟล์ภาพจำลองในโฟลเดอร์รูปภาพ |

---

## 🧪 2. การทดสอบและตรวจสอบความถูกต้อง (Verification & Testing)

| คำสั่ง (Command) | หน้าที่ / รายละเอียด |
| :--- | :--- |
| `php tests/verify_system.php` | **รัน Automated Verification Suite:** ทดสอบความสมบูรณ์ของระบบแบบครบวงจร (Model, Controller, Routing, การจำกัดสิทธิ์ Admin/Staff, การเปลี่ยนสถานะงาน, ระบบแจ้งเตือน Notification, และมาตรการ PDPA) |

---

## 🌐 3. การรันเซิร์ฟเวอร์เพื่อพัฒนา (Local Development Server)

### วิธีที่ ก: PHP Built-in Server (สะดวกรวดเร็ว)
```bash
php -S localhost:8000 -t public
```
- เข้าใช้งานได้ทันทีที่: `http://localhost:8000`

### วิธีที่ ข: รันผ่าน XAMPP (Apache + MySQL)
1. เปิด **XAMPP Control Panel** แล้ว Start โมดูล **Apache** และ **MySQL**
2. URL หลักของระบบ:
   - 👥 **หน้าประชาชน (แจ้งขยะ/ติดตามงาน):** `http://localhost/abandoned_waste/public/`
   - 🔑 **หน้าเข้าสู่ระบบ (Admin & Staff):** `http://localhost/abandoned_waste/public/login`
   - 🔍 **หน้าติดตามสถานะงาน:** `http://localhost/abandoned_waste/public/track`
   - *(กรณีโฟลเดอร์เป็นภาษาไทย: `http://localhost/ขยะไร้บ้าน/public/`)*

---

## 🐳 4. การจัดการผ่าน Docker & Docker Compose

ระบบมีไฟล์คอนฟิกสำหรับสภาพแวดล้อม Multi-container (PHP-FPM, Nginx, MySQL 8, Redis):

```bash
# 1. สร้างและสตาร์ต Container ในโหมด Background
docker compose up -d --build

# 2. ตรวจสอบสถานะของ Container ทั้งหมด
docker compose ps

# 3. ดู Logs การทำงานแบบ Real-time
docker compose logs -f

# 4. สั่ง Migrate ฐานข้อมูลภายใน Container
docker compose exec app php database/migrate.php

# 5. สั่งรันชุดทดสอบความถูกต้องภายใน Container
docker compose exec app php tests/verify_system.php

# 6. หยุดการทำงานและปิด Container
docker compose down
```
- **Web URL (Docker):** `http://localhost:8080`

---

## 🧠 5. การจัดการคลังความรู้ LLM Wiki (Wiki Maintenance Commands)

คำสั่งตรวจสอบสุขภาพและการสืบค้นในระบบ [[llm-wiki]]:

### CLI Tools (Node.js / Python)
```bash
# ตรวจสอบตัวชี้วัดสถิติจำนวนหน้าและลิงก์ของคลังความรู้
npm run status
# หรือ: node tools/wiki_tools.js status
# หรือ: python tools/wiki_tools.py status

# ตรวจสอบสุขภาพกราฟความรู้ (Broken Links, Orphans, Index completeness)
npm run lint
# หรือ: node tools/wiki_tools.js lint
# หรือ: python tools/wiki_tools.py lint

# ค้นหาข้อความหรือคีย์เวิร์ดใน Wiki
node tools/wiki_tools.js search "<คำค้นหา>"
# หรือ: python tools/wiki_tools.py search "<คำค้นหา>"
```

### Agent Operations (คำสั่งสำหรับผู้ช่วย Agent)
- `/ingest` หรือ `"Ingest source X"`: นำเข้าและย่อยเอกสารดิบจาก `raw/` บันทึกเป็น Source digest, แตก Concepts/Entities, อัปเดต `wiki/index.md` และลงบันทึกใน `wiki/log.md`
- `/query` หรือ `"Synthesize on topic Y"`: ค้นคว้าและสังเคราะห์คำตอบข้ามเอกสาร พร้อมเชื่อมโยง `[[WikiLinks]]` และบันทึกลง `wiki/syntheses/` หรือ `wiki/queries/`
- `/lint` หรือ `"Check wiki health"`: รันการตรวจสอบความสมบูรณ์และแนะนำแนวทางขยายกราฟความรู้

---

## 🌿 6. คำสั่งควบคุมเวอร์ชัน (Git Version Control)

```bash
# ตรวจสอบสถานะไฟล์ที่แก้ไข
git status

# สเตจไฟล์ทั้งหมดที่พร้อมบันทึก
git add .

# บันทึกประวัติการเปลี่ยนแปลง
git commit -m "feat/fix: คำอธิบายการแก้ไข"

# ซิงก์ข้อมูลกับรีโมท
git pull
git push
```

---

## 🔑 บัญชีทดสอบระบบที่สร้างขึ้นอัตโนมัติ (Demo Credentials)

| Role | อีเมล (Email) | รหัสผ่าน (Password) | สิทธิ์การใช้งาน |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@waste.local` | `admin1234` | บริหารจัดการทุกโมดูล, มอบหมายงาน, ดู Dashboard & Reports |
| **Staff 1** | `somchai@waste.local` | `staff1234` | เจ้าหน้าที่ภาคสนาม (ทีม A) |
| **Staff 2** | `wichai@waste.local` | `staff1234` | เจ้าหน้าที่ภาคสนาม (ทีม B) |
| **Staff 3** | `manas@waste.local` | `staff1234` | เจ้าหน้าที่ภาคสนาม (ทีม C) |
| **Staff 4** | `anupong@waste.local` | `staff1234` | เจ้าหน้าที่ภาคสนาม (ทีม D) |
| **Staff 5** | `siriporn@waste.local` | `staff1234` | เจ้าหน้าที่ภาคสนาม (ทีม E) |

---

## 🔗 Key Relationships
- **Associated Concepts:** [[llm-wiki]], [[wikilinks]]
- **Associated Entities:** [[obsidian]]
