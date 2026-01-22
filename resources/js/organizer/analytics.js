/**
 * Organizer Analytics JS
 * Sales charts for organizer dashboard
 */

import { Chart, registerables } from 'chart.js';
import api from '../utils/api';

Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', () => {
    initSalesChart();
    initTicketSalesChart();
});

async function initSalesChart() {
    const canvas = document.getElementById('salesChart');
    if (!canvas) return;

    try {
        const response = await api.get('/organizer/analytics/sales');
        const data = response.data;

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Penjualan (IDR)',
                    data: data.values,
                    borderColor: '#FF8FC7',
                    backgroundColor: 'rgba(255, 143, 199, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1A1A1A',
                        callbacks: {
                            label: (ctx) => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#A3A3A3' },
                        grid: { color: '#1F2937' }
                    },
                    x: {
                        ticks: { color: '#A3A3A3' },
                        grid: { display: false }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Sales chart error:', error);
    }
}

async function initTicketSalesChart() {
    const canvas = document.getElementById('ticketSalesChart');
    if (!canvas) return;

    try {
        const response = await api.get('/organizer/analytics/tickets');
        const data = response.data;

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Tiket Terjual',
                    data: data.values,
                    backgroundColor: 'rgba(255, 143, 199, 0.8)',
                    borderColor: '#FF8FC7',
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#A3A3A3', precision: 0 },
                        grid: { color: '#1F2937' }
                    },
                    x: {
                        ticks: { color: '#A3A3A3' },
                        grid: { display: false }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Ticket sales chart error:', error);
    }
}

console.log('📊 Organizer Analytics loaded');
