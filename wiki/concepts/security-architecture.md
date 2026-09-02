---
type: concept
title: "Security Architecture & Hardening Standards"
aliases:
  - "Security Architecture"
  - "ระบบรักษาความปลอดภัย"
  - "Hardening Guidelines"
created: "2026-09-02"
updated: "2026-09-02"
tags:
  - type/concept
  - domain/security
  - domain/architecture
---

# Security Architecture & Hardening Standards (สถาปัตยกรรมความปลอดภัยและมาตรฐานความมั่นคง)

## 💡 ภาพรวมและนิยาม (Overview & Definition)
สถาปัตยกรรมการรักษาความปลอดภัยของระบบเว็บแอปพลิเคชัน **ระบบแจ้งจัดเก็บขยะไร้บ้าน** ยึดหลักการป้องกันแบบหลายชั้น (**Defense-in-Depth**) เพื่อปกป้องข้อมูลทั้งในฝั่ง Front-End (Client-Side) และ Back-End (Server-Side) โดยรักษาความเข้ากันได้ 100% (**Zero-Breakage Guarantee**) และกำหนดรูปแบบการเขียนโค้ดให้เป็นมาตรฐานเดียวกันทั่วทั้งโครงการ (Unified Architecture Pattern)

---

## 🧩 แกนหลักด้านความปลอดภัย (Core Security Pillars)

### 1. Unified Validation & Sanitization Engine (`App\Core\Validator`)
- **Single Source of Truth:** รวมศูนย์การตรวจสอบความถูกต้องของข้อมูลทุก Controller ไว้ที่ `Validator::make($data, $rules)`
- **Sanitization อัตโนมัติ:** ตัดอักขระ Null Bytes (`chr(0)`), Trim ช่องว่าง, กรองคำสั่งอันตราย
- **กฎตรวจสอบเฉพาะทาง:**
  - `thai_phone`: ตรวจสอบเบอร์โทรศัพท์มือถือและพื้นฐานของประเทศไทย 10 หลัก
  - `coordinates`: ตรวจสอบขอบเขตพิกัดละติจูด (-90 ถึง 90) และลองจิจูด (-180 ถึง 180)
  - `in`: Whitelist ตรวจสอบสถานะงานที่อนุญาต ป้องกันค่าสถานะผิดปกติ

### 2. อัตราการจำกัดคำขอและป้องกัน Brute-Force (`App\Core\RateLimiter`)
- **การบล็อกการสแปมแจ้งข้อมูล:** จำกัดการส่งแบบฟอร์มขยะสาธารณะ (`/report`) สูงสุด 5 รายการต่อ 10 นาทีต่อ IP
- **การบล็อกการสืบค้นข้อมูลประชาชน (Anti-Enumeration):** จำกัดการค้นหาหน้า `/track` สูงสุด 30 ครั้งต่อ 1 นาที
- **การบล็อกการสุ่มรหัสผ่าน Admin:** ล็อกเอาต์และระงับ IP 15 นาทีหากใส่รหัสผิดเกิน 5 ครั้ง
- **HTTP 429 Too Many Requests:** ตอบกลับด้วยหน้า UI สุภาพแจ้งระยะเวลาที่ต้องรอ พร้อมรหัสสถานะ 429 สำหรับ AJAX

### 3. การคุ้มครองข้อมูลส่วนบุคคล (PDPA Compliance & Data Privacy)
- **การค้นหาด้วยเบอร์โทรศัพท์:** ป้องกันการสุ่มตัวเลขสั้นๆ (เช่น ป้อน `0` เพื่อดึงข้อมูลประชาชนทั้งหมด) โดยกำหนดให้ต้องระบุเบอร์โทรไม่น้อยกว่า 9–10 หลัก และทำ Normalize เครื่องหมายขีด/เว้นวรรคในฐานข้อมูล
- **Data Masking (`App\Core\PDPA`):** ซ่อนเบอร์โทรศัพท์ (`081-***-5678`), ซ่อนชื่อ-นามสกุล (`สมศักดิ์ ร...`), และซ่อนอีเมล (`s***i@example.com`) เมื่อแสดงผลต่อสาธารณะ
- **แผนที่สาธารณะ (`/api/map-points`):** ส่งเฉพาะข้อมูลพิกัด ประเภทขยะ และรูปภาพ โดยไม่เปิดเผยชื่อหรือเบอร์โทรของผู้แจ้ง

### 4. การรักษาความปลอดภัยฝั่ง Front-End & DOM XSS Defense
- **Universal CSRF Protection:** ติดตั้ง `<meta name="csrf-token">` ใน Layout หลัก และโหลด `app-security.js` เพื่อแทรก Header `X-CSRF-TOKEN` ในทุกคำขอ `fetch()` หรือ AJAX โดยอัตโนมัติ
- **Safe DOM / Leaflet Map Rendering:** หลีกเลี่ยงการต่อสตริง HTML สุ่มเสี่ยงใน Popups ของแผนที่ Leaflet โดยใช้ `AppSecurity.createSafePopup()` สร้าง DOM Element ผ่าน `textContent` ที่ปลอดภัยจาก XSS 100%
- **Declarative Event Delegation (No Inline `onclick`):** ยกเลิกการใช้ Inline Event Handler ทุกจุดในโปรเจกต์ (`onclick="..."`) แล้วเปลี่ยนมาใช้ Event Delegation ใน `app-security.js` ผ่าน Data Attributes เช่น `data-modal-open`, `data-modal-close`, `data-dismiss="alert"`, และ `data-sidebar-open/close` เพื่อสอดรับกับนโยบาย CSP ที่เข้มงวด

### 5. มาตรการระดับโครงสร้างพื้นฐาน (Server Hardening & Strict CSP)
- **Strict Nonce-based Content-Security-Policy (`App\Core\CSP`):**
  - กำหนดค่า CSP ตามมาตรฐานความปลอดภัยสูงสุดของ Mozilla Observatory / OWASP
  - **ลบ `'unsafe-inline'` และ `data:` ออกจาก `script-src` 100%**: ป้องกันการฉีดสคริปต์ (XSS) ทุกรูปแบบ
  - **Cryptographic Nonce Per-Request:** ใช้ `App\Core\CSP::nonce()` สุ่มรหัส 128-bit random base64 ผูกกับแท็ก `<script nonce="...">` ทุกแท็ก
  - **จำกัด `object-src 'none'`:** ปิดกั้นการแทรก Flash, Java Applets หรือ Object ภายนอกทั้งหมด
  - **จำกัด `base-uri 'self'`:** ป้องกันการโจมตี Base Tag Hijacking
  - **จำกัด `frame-ancestors 'self'`:** ป้องกัน Clickjacking
- **HTTP Security Headers:**
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: geolocation=(self), camera=(), microphone=()`
- **Upload Directory Hardening:** บล็อกการสั่ง Execute ไฟล์ Script (`.php`, `.phtml`, `.sh`, `.exe`, ฯลฯ) ใน `public/uploads/.htaccess`
- **Zero Information Leakage:** ซ่อน PHP Stack Traces และ Database Connection Exceptions ในโหมด Production พร้อมแสดงหน้าข้อผิดพลาดมาตรฐาน (`errors.403`, `errors.429`, `errors.500`)

---

## 🔗 ความสัมพันธ์ในคลังความรู้ (Key Relationships)
- **Associated Entities:** [[entities/essential-commands|Essential Commands & Operational CLI]], [[entities/obsidian|Obsidian]]
- **Associated Concepts:** [[concepts/llm-wiki|LLM Wiki]]
