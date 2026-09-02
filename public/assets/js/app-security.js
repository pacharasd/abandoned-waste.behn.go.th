/**
 * Unified Client-Side Security & Safe DOM Engine
 * - Automates CSRF token injection for fetch/AJAX
 * - Provides DOM-based XSS sanitizers
 * - Builds secure, injection-proof Leaflet Map Popups
 */
(function(window) {
    'use strict';

    const AppSecurity = {
        /**
         * Retrieve active CSRF token from document meta tag
         */
        csrfToken: function() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        /**
         * Escape HTML string safely to prevent Cross-Site Scripting (XSS)
         */
        escapeHtml: function(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        /**
         * Secure Fetch Wrapper with automatic CSRF header and standard JSON parsing
         */
        fetch: function(url, options = {}) {
            options = Object.assign({}, options);
            options.headers = Object.assign({}, options.headers || {});

            const method = (options.method || 'GET').toUpperCase();
            if (method !== 'GET' && method !== 'HEAD') {
                const token = AppSecurity.csrfToken();
                if (token && !options.headers['X-CSRF-TOKEN']) {
                    options.headers['X-CSRF-TOKEN'] = token;
                }
            }

            if (!options.headers['X-Requested-With']) {
                options.headers['X-Requested-With'] = 'XMLHttpRequest';
            }

            return window.fetch(url, options).then(response => {
                if (response.status === 419) {
                    alert('Session หรือ CSRF Token หมดอายุ กรุณารีเฟรชหน้าเว็บ');
                    window.location.reload();
                    return Promise.reject(new Error('CSRF token expired'));
                }
                if (response.status === 429) {
                    alert('คุณส่งคำขอถี่เกินกำหนด กรุณารอสักครู่ก่อนทำรายการใหม่');
                    return Promise.reject(new Error('Rate limit exceeded'));
                }
                return response;
            });
        },

        /**
         * Create safe Leaflet marker popup DOM element (Free of innerHTML injection risks)
         */
        createSafePopup: function(point, baseUrl = '') {
            const container = document.createElement('div');
            container.className = 'p-1 font-sans text-xs space-y-2';

            const header = document.createElement('div');
            header.className = 'flex items-center justify-between gap-2 border-b pb-1.5';

            const reportNum = document.createElement('span');
            reportNum.className = 'font-bold font-mono text-emerald-700';
            reportNum.textContent = point.report_number || '-';
            header.appendChild(reportNum);

            const statusBadge = document.createElement('span');
            statusBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700';
            statusBadge.textContent = point.status || '-';
            header.appendChild(statusBadge);

            container.appendChild(header);

            const typeDiv = document.createElement('div');
            typeDiv.className = 'font-semibold text-slate-800';
            typeDiv.textContent = '🏷️ ' + (point.waste_type || 'ขยะทั่วไป');
            container.appendChild(typeDiv);

            const addressDiv = document.createElement('div');
            addressDiv.className = 'text-slate-600 line-clamp-2';
            addressDiv.textContent = '📍 ' + (point.address || '-');
            container.appendChild(addressDiv);

            if (point.image) {
                const img = document.createElement('img');
                img.src = (baseUrl ? baseUrl.replace(/\/$/, '') + '/' : '') + point.image;
                img.alt = 'Waste preview';
                img.className = 'w-full h-24 object-cover rounded-lg mt-1 border';
                img.loading = 'lazy';
                container.appendChild(img);
            }

            const trackLink = document.createElement('a');
            trackLink.href = (baseUrl ? baseUrl.replace(/\/$/, '') : '') + '/track?search=' + encodeURIComponent(point.report_number || '');
            trackLink.className = 'block text-center mt-2 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition text-[11px]';
            trackLink.textContent = 'ดูรายละเอียดงาน';
            container.appendChild(trackLink);

        }
    };

    // Declarative Event Delegation for Modals, Alerts, and UI actions (No inline 'onclick' needed)
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            // 1. Alert Dismiss
            const dismissBtn = e.target.closest('[data-dismiss="alert"]');
            if (dismissBtn) {
                const box = dismissBtn.closest('.flash-alert') || dismissBtn.parentElement;
                if (box) box.remove();
                return;
            }

            // 2. Modal Open
            const openModalBtn = e.target.closest('[data-modal-open]');
            if (openModalBtn) {
                const targetId = openModalBtn.getAttribute('data-modal-open');
                const modal = document.getElementById(targetId);
                if (modal) modal.classList.remove('hidden');
                return;
            }

            // 3. Modal Close
            const closeModalBtn = e.target.closest('[data-modal-close]');
            if (closeModalBtn) {
                const targetId = closeModalBtn.getAttribute('data-modal-close');
                const modal = document.getElementById(targetId);
                if (modal) modal.classList.add('hidden');
                return;
            }

            // 4. Modal Backdrop direct click
            const backdropModal = e.target.closest('.modal-backdrop-auto');
            if (backdropModal && e.target === backdropModal) {
                backdropModal.classList.add('hidden');
                return;
            }
        });
    });

    window.AppSecurity = AppSecurity;
})(window);
