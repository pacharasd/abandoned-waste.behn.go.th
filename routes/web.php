<?php
use App\Core\Router;

// ==========================================
// 1. PUBLIC CITIZEN ROUTES
// ==========================================
Router::get('/', 'CitizenController@home');
Router::get('/schedule', 'CitizenController@schedule');
Router::get('/report', 'CitizenController@reportForm');
Router::post('/report', 'CitizenController@submitReport');
Router::get('/success', 'CitizenController@success');
Router::get('/track', 'CitizenController@track');
Router::get('/api/map-points', 'CitizenController@getMapPointsApi');

// ==========================================
// 2. AUTHENTICATION ROUTES
// ==========================================
Router::get('/login', 'AuthController@showLogin');
Router::post('/login', 'AuthController@login');
Router::post('/logout', 'AuthController@logout');
Router::get('/logout', 'AuthController@logout');

// ==========================================
// 3. ADMIN PORTAL ROUTES (auth:admin)
// ==========================================
Router::get('/admin', 'AdminDashboardController@index', ['auth:admin']);
Router::get('/admin/dashboard', 'AdminDashboardController@index', ['auth:admin']);
Router::get('/admin/api/stats', 'AdminDashboardController@statsApi', ['auth:admin']);

// Admin Schedules Management (Monthly Cycles)
Router::get('/admin/schedules', 'AdminScheduleController@index', ['auth:admin']);
Router::post('/admin/schedules', 'AdminScheduleController@store', ['auth:admin']);
Router::post('/admin/schedules/quick-generate', 'AdminScheduleController@quickGenerate', ['auth:admin']);
Router::get('/admin/schedules/{id}', 'AdminScheduleController@show', ['auth:admin']);
Router::post('/admin/schedules/{id}/update', 'AdminScheduleController@update', ['auth:admin']);
Router::post('/admin/schedules/{id}/delete', 'AdminScheduleController@delete', ['auth:admin']);

// Admin Reports Management
Router::get('/admin/reports', 'AdminReportController@index', ['auth:admin']);
Router::get('/admin/reports/{id}', 'AdminReportController@show', ['auth:admin']);
Router::post('/admin/reports/{id}/assign', 'AdminReportController@assign', ['auth:admin']);
Router::post('/admin/reports/{id}/status', 'AdminReportController@updateStatus', ['auth:admin']);
Router::get('/admin/reports/export/csv', 'AdminReportController@export', ['auth:admin']);

// Admin Waste Types Management
Router::get('/admin/waste-types', 'WasteTypeController@index', ['auth:admin']);
Router::post('/admin/waste-types', 'WasteTypeController@store', ['auth:admin']);
Router::post('/admin/waste-types/{id}/update', 'WasteTypeController@update', ['auth:admin']);
Router::post('/admin/waste-types/{id}/delete', 'WasteTypeController@delete', ['auth:admin']);

// Admin Reports & Analytics
Router::get('/admin/analytics', 'ReportAnalyticsController@index', ['auth:admin']);


// Admin Notifications
Router::get('/admin/notifications', 'NotificationController@index', ['auth:admin']);
Router::post('/admin/notifications/{id}/read', 'NotificationController@markAsRead', ['auth:admin']);
Router::post('/admin/notifications/read-all', 'NotificationController@markAllAsRead', ['auth:admin']);
Router::get('/api/notifications/unread-count', 'NotificationController@unreadCountApi', ['auth']);

// ==========================================
// 4. LEGACY / STAFF REDIRECTS (All management consolidated under Admin)
// ==========================================
Router::get('/staff', function() {
    \App\Core\Response::redirect('/admin/dashboard');
});
Router::get('/staff/dashboard', function() {
    \App\Core\Response::redirect('/admin/dashboard');
});
Router::get('/staff/jobs/{id}', function($id) {
    \App\Core\Response::redirect("/admin/reports/{$id}");
});
