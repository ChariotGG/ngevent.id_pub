/**
 * Admin Reports JS
 * Export data to Excel functionality
 */

import api from '../utils/api';
import { showLoading, closeLoading, showAlert, showError } from '../utils/alert';

/**
 * Initialize reports page
 */
document.addEventListener('DOMContentLoaded', () => {
    initExportButtons();
    initDateRangeFilter();
});

/**
 * Initialize export buttons
 */
function initExportButtons() {
    // Export Events button
    const exportEventsBtn = document.getElementById('exportEvents');
    if (exportEventsBtn) {
        exportEventsBtn.addEventListener('click', () => exportEvents());
    }

    // Export Orders button
    const exportOrdersBtn = document.getElementById('exportOrders');
    if (exportOrdersBtn) {
        exportOrdersBtn.addEventListener('click', () => exportOrders());
    }

    // Export Revenue button
    const exportRevenueBtn = document.getElementById('exportRevenue');
    if (exportRevenueBtn) {
        exportRevenueBtn.addEventListener('click', () => exportRevenue());
    }
}

/**
 * Export Events to Excel
 */
async function exportEvents() {
    try {
        showLoading('Mengekspor data events...');

        const filters = getFilters();
        const response = await api.get('/admin/reports/export/events', {
            params: filters,
            responseType: 'blob'
        });

        downloadFile(response, 'events-report.xlsx');
        closeLoading();
        showAlert('Data events berhasil diekspor!');

    } catch (error) {
        closeLoading();
        showError('Gagal mengekspor data events');
        console.error('Export error:', error);
    }
}

/**
 * Export Orders to Excel
 */
async function exportOrders() {
    try {
        showLoading('Mengekspor data orders...');

        const filters = getFilters();
        const response = await api.get('/admin/reports/export/orders', {
            params: filters,
            responseType: 'blob'
        });

        downloadFile(response, 'orders-report.xlsx');
        closeLoading();
        showAlert('Data orders berhasil diekspor!');

    } catch (error) {
        closeLoading();
        showError('Gagal mengekspor data orders');
        console.error('Export error:', error);
    }
}

/**
 * Export Revenue to Excel
 */
async function exportRevenue() {
    try {
        showLoading('Mengekspor data revenue...');

        const filters = getFilters();
        const response = await api.get('/admin/reports/export/revenue', {
            params: filters,
            responseType: 'blob'
        });

        downloadFile(response, 'revenue-report.xlsx');
        closeLoading();
        showAlert('Data revenue berhasil diekspor!');

    } catch (error) {
        closeLoading();
        showError('Gagal mengekspor data revenue');
        console.error('Export error:', error);
    }
}

/**
 * Get filters from form
 */
function getFilters() {
    return {
        start_date: document.getElementById('startDate')?.value || null,
        end_date: document.getElementById('endDate')?.value || null,
        status: document.getElementById('statusFilter')?.value || null,
        organizer_id: document.getElementById('organizerFilter')?.value || null,
    };
}

/**
 * Download blob as file
 */
function downloadFile(blob, filename) {
    const url = window.URL.createObjectURL(new Blob([blob]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    link.parentNode.removeChild(link);
    window.URL.revokeObjectURL(url);
}

/**
 * Initialize date range filter
 */
function initDateRangeFilter() {
    // Check if Flatpickr is loaded (optional)
    if (typeof flatpickr !== 'undefined') {
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');

        if (startDate) {
            flatpickr(startDate, {
                dateFormat: 'Y-m-d',
                maxDate: 'today'
            });
        }

        if (endDate) {
            flatpickr(endDate, {
                dateFormat: 'Y-m-d',
                maxDate: 'today'
            });
        }
    }

    // Apply filter button
    const applyFilterBtn = document.getElementById('applyFilter');
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', applyFilters);
    }

    // Reset filter button
    const resetFilterBtn = document.getElementById('resetFilter');
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', resetFilters);
    }
}

/**
 * Apply filters (reload data with filters)
 */
function applyFilters() {
    const filters = getFilters();
    const queryString = new URLSearchParams(filters).toString();
    window.location.href = window.location.pathname + '?' + queryString;
}

/**
 * Reset filters
 */
function resetFilters() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    if (document.getElementById('statusFilter')) {
        document.getElementById('statusFilter').value = '';
    }
    if (document.getElementById('organizerFilter')) {
        document.getElementById('organizerFilter').value = '';
    }
    window.location.href = window.location.pathname;
}

console.log('📊 Admin Reports loaded');
