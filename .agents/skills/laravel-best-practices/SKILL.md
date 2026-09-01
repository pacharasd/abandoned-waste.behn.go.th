---
name: laravel-best-practices
description: Comprehensive guidelines, patterns, and best practices for building robust, secure, and maintainable Laravel web applications. Use when writing controllers, models, migrations, seeders, requests, policies, and blade views.
---

# Laravel Best Practices & Architecture Skill

## 1. Controller & Request Architecture
- **Skinny Controllers, Rich Models/Services**: Keep controllers minimal. Delegate business logic to Models or dedicated Service classes.
- **Form Requests for Validation**: Always validate user input using dedicated Form Requests or structured validation rules. Never trust raw user inputs.
- **RESTful Resource Routing**: Follow standard naming conventions (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`).

## 2. Eloquent ORM & Relationships
- **Explicit Foreign Keys**: Always define explicit `belongsTo`, `hasMany`, and `belongsToMany` relationships.
- **Eager Loading (`with`)**: Prevent N+1 query problems by eager loading related models (e.g. `WasteReport::with(['wasteType', 'assignedStaff', 'images'])`).
- **Database Transactions**: Wrap multi-table operations (such as creating a report + saving images + logging status history) in `DB::transaction(function() { ... })`.

## 3. Security & Access Control
- **CSRF Protection**: Ensure all POST/PUT/DELETE forms include `@csrf`.
- **Authorization Policies**: Use Laravel Gates and Policies to guard actions based on user roles (`admin` vs `staff`).
- **Mass Assignment Protection**: Use `$fillable` on all Eloquent models.

## 4. Blade Templating & Components
- **Layouts**: Use master layouts (`layouts.app`, `layouts.admin`, `layouts.staff`) with `@yield('content')` or `<x-layout>`.
- **Reusable Components**: Break repeated UI elements (Status Badges, Stat Cards, Alerts, Modals) into reusable Blade components.
