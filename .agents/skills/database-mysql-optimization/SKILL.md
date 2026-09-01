---
name: database-mysql-optimization
description: Database architecture, relational integrity, foreign key constraints, indexing strategies, and query optimization patterns for MySQL 8 and MariaDB. Use when designing schemas, migrations, seeders, and complex queries.
---

# MySQL & Relational Database Optimization Skill

## 1. Schema & Foreign Key Integrity
- **Relational Constraints**: Always define explicit `FOREIGN KEY (...) REFERENCES ... ON DELETE CASCADE / SET NULL`.
- **Indexed Search Fields**: Add indexes to fields used in `WHERE`, `ORDER BY`, or `JOIN`:
  - `report_number` (UNIQUE index)
  - `status`, `waste_type_id`, `assigned_staff_id`, `created_at`
- **Precise Data Types**:
  - GPS Coordinates: `DECIMAL(10, 7)` for high-precision Latitude and Longitude.
  - Weights: `DECIMAL(8, 2)` (e.g. up to 999,999.99 kg).
  - Status / Roles: `ENUM` or controlled strings.

## 2. Query Performance & Aggregations
- Use SQL Aggregations (`COUNT(*)`, `SUM(estimated_weight)`, `SUM(actual_weight)`) for dashboard metrics rather than loading all models into PHP memory.
- Use composite indexes on `(status, created_at)` for fast filtered timeline queries.
