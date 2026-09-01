---
name: tailwind-ui-ux
description: Guidelines and design system patterns for modern, high-aesthetic government and environmental web applications with Tailwind CSS, Thai typography, and mobile-first layouts.
---

# Tailwind CSS & UI/UX Design System Skill

## 1. Aesthetic Identity (Thai Eco-Government)
- **Palette**:
  - Primary / Brand: Deep Emerald & Forest Green (`emerald-600`, `emerald-700`, `emerald-800`, `teal-600`)
  - Accent / Warnings: Amber & Orange (`amber-500`, `orange-500`) for pending issues.
  - Surface: Crisp White (`#FFFFFF`), Slate Light (`slate-50`, `slate-100`), Card borders (`slate-200`).
  - Text: High-contrast slate (`slate-800`, `slate-900`, muted `slate-500`).
- **Typography**:
  - Primary Thai Fonts: `Kanit`, `Prompt`, `Sarabun`, or `Inter` paired with clean line-heights.
- **Glassmorphism & Elevation**:
  - Use subtle shadows (`shadow-sm`, `shadow-md`, `shadow-emerald-500/10`) and backdrop blur on sticky navigation bars.

## 2. Mobile-First Layouts & Accessibility
- **Field Staff Mobile View**:
  - Large tap targets (min 44x44px buttons).
  - Clear high-contrast status badges.
  - Single-column cards for task details with sticky bottom action buttons (e.g. "อัปเดตสถานะ", "นำทางด้วย GPS").
- **Admin Dashboard Layout**:
  - Collapsible responsive sidebar.
  - Top header with search bar, user profile, and Notification Bell with unread counter badge.
  - Grid metrics cards (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`).
