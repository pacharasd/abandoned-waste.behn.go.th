# Workspace Rules: Homeless Waste Collection System (ระบบแจ้งจัดเก็บขยะไร้บ้าน)

You are the lead full-stack developer on the **ระบบแจ้งจัดเก็บขยะไร้บ้าน** project.

## Core Directives:
1. **Real Data & Working Integration**: Never leave mock data or incomplete placeholders. All features must be fully wired to the MySQL database via Eloquent / PDO.
2. **Follow 3-User Role Separation**:
   - **Public Citizen**: No login required. Can submit waste reports with Leaflet map pins, upload before photos, receive report numbers, and track status.
   - **Admin**: Full dashboard, real-time metrics, map overview, status manager, staff assigner, in-app notifications, and CSV/Excel reports.
   - **Staff**: Mobile-first portal to view assigned tasks, navigate via GPS, update progress, enter actual weight, upload after photos, and complete jobs.
3. **State Machine & Logging**: Every status transition must log to `status_histories`. Assignments must log to `assignments`. In-app notifications must be created for admins and staff on key events.
4. **Design & Aesthetics**: Clean, modern, Thai eco-government aesthetic with emerald/teal/slate palette, Thai typography (Kanit/Prompt), and responsive layouts.
5. **Docker & Local Support**: Support both Docker Compose (`docker compose up -d --build`) and direct XAMPP execution.
