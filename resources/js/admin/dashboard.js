/**
 * Admin Dashboard JS
 * Chart.js implementation for admin analytics
 */

import { Chart, registerables } from 'chart.js';
import api from '../utils/api';
import { showError, showLoading, closeLoading } from '../utils/alert';

// Register Chart.js components
Chart.register(...registerables);

// Chart.js default configuration for dark theme
Chart.defaults.color = '#A3A3A3';
Chart.defaults.borderColor = '#374151';
Chart.defaults.backgroundColor = 'rgba(255, 143, 199, 0.1)';

/**
 * Initialize admin dashboard
 */
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Load dashboard data
        await initRevenueChart();
        await initEventsChart();
        await initUsersChart();

        // Auto-refresh every 5 minutes
        setInterval(() => {
            refreshDashboardData();
        }, 300000);

    } catch (error) {
        console.error('Dashboard initialization error:', error);
        showError('Gagal memuat data dashboard');
    }
});

/**
 * Revenue Chart (Line Chart)
 */
async function initRevenueChart() {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) return;

    try {
        const response = await api.get('/admin/analytics/revenue');
        const data = response.data;

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: data.labels, // ['Jan', 'Feb', 'Mar', ...]
                datasets: [{
                    label: 'Revenue (IDR)',
                    data: data.values, // [5000000, 7500000, ...]
                    borderColor: '#FF8FC7',
                    backgroundColor: 'rgba(255, 143, 199, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#FF8FC7',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#FAFAFA',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1A1A1A',
                        titleColor: '#FAFAFA',
                        bodyColor: '#A3A3A3',
                        borderColor: '#374151',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ' + formatRupiah(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#A3A3A3',
                            callback: function(value) {
                                return formatRupiahShort(value);
                            }
                        },
                        grid: {
                            color: '#1F2937',
                            drawBorder: false,
                        }
                    },
                    x: {
                        ticks: {
                            color: '#A3A3A3'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Revenue chart error:', error);
    }
}

/**
 * Events Chart (Bar Chart)
 */
async function initEventsChart() {
    const canvas = document.getElementById('eventsChart');
    if (!canvas) return;

    try {
        const response = await api.get('/admin/analytics/events');
        const data = response.data;

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: data.labels, // ['Published', 'Draft', 'Cancelled']
                datasets: [{
                    label: 'Events',
                    data: data.values, // [45, 12, 3]
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)', // Success green
                        'rgba(245, 158, 11, 0.8)', // Warning yellow
                        'rgba(239, 68, 68, 0.8)',  // Error red
                    ],
                    borderColor: [
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1A1A1A',
                        titleColor: '#FAFAFA',
                        bodyColor: '#A3A3A3',
                        borderColor: '#374151',
                        borderWidth: 1,
                        padding: 12,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#A3A3A3',
                            precision: 0
                        },
                        grid: {
                            color: '#1F2937',
                            drawBorder: false,
                        }
                    },
                    x: {
                        ticks: {
                            color: '#A3A3A3'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Events chart error:', error);
    }
}

/**
 * Users Growth Chart (Doughnut Chart)
 */
async function initUsersChart() {
    const canvas = document.getElementById('usersChart');
    if (!canvas) return;

    try {
        const response = await api.get('/admin/analytics/users');
        const data = response.data;

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: ['Organizers', 'Regular Users'],
                datasets: [{
                    data: [data.organizers, data.users],
                    backgroundColor: [
                        'rgba(255, 143, 199, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                    ],
                    borderColor: [
                        '#FF8FC7',
                        '#3B82F6',
                    ],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#FAFAFA',
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1A1A1A',
                        titleColor: '#FAFAFA',
                        bodyColor: '#A3A3A3',
                        borderColor: '#374151',
                        borderWidth: 1,
                        padding: 12,
                    }
                }
            }
        });
    } catch (error) {
        console.error('Users chart error:', error);
    }
}

/**
 * Refresh dashboard data
 */
async function refreshDashboardData() {
    try {
        // Could re-fetch and update charts
        console.log('Refreshing dashboard data...');
    } catch (error) {
        console.error('Refresh error:', error);
    }
}

/**
 * Format Rupiah
 */
function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
}

/**
 * Format Rupiah Short (for chart labels)
 */
function formatRupiahShort(number) {
    if (number >= 1000000000) {
        return 'Rp ' + (number / 1000000000).toFixed(1) + 'M';
    } else if (number >= 1000000) {
        return 'Rp ' + (number / 1000000).toFixed(1) + 'jt';
    } else if (number >= 1000) {
        return 'Rp ' + (number / 1000).toFixed(0) + 'rb';
    }
    return 'Rp ' + number;
}

console.log('📊 Admin Dashboard loaded');
