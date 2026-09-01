---
name: docker-compose-dev
description: Architecture, Dockerfile patterns, and Docker Compose configurations for containerized PHP/Nginx/MySQL/Redis web applications. Use when creating container setups, volumes, network definitions, and deployment scripts.
---

# Docker Compose Development Skill

## 1. Multi-Service Container Architecture
- **`app` (PHP-FPM)**:
  - Base Image: `php:8.2-fpm-alpine` or `php:8.3-fpm-alpine`
  - Required Extensions: `pdo_mysql`, `mbstring`, `exif`, `pcntl`, `bcmath`, `gd`, `zip`
  - Composer installed from `composer:latest`
  - Non-root user permissions for storage and cache directories.
- **`web` (Nginx)**:
  - Base Image: `nginx:alpine`
  - Configuration: `docker/nginx/default.conf` forwarding `.php` requests to `app:9000`.
- **`mysql` (MySQL 8 / MariaDB)**:
  - Persistent named volume (e.g. `db_data:/var/lib/mysql`) to ensure data persists across container restarts.
  - Environment variables: `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD`.
- **`redis`**:
  - Optional high-speed caching & queue session driver.

## 2. Docker Compose Standards
- Always provide `.env.example` with container service names as hostnames (e.g., `DB_HOST=mysql`, `REDIS_HOST=redis`).
- Use healthchecks and `depends_on` conditions so the app waits for MySQL to be healthy before attempting migrations.
