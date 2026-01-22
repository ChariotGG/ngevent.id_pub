/**
 * Ticket Validation Page JS
 * Public ticket QR validation
 */

import QRCode from 'qrcode';
import api from '../utils/api';
import { showError, showAlert } from '../utils/alert';

document.addEventListener('DOMContentLoaded', () => {
    initTicketLookup();
    generateQRCode();
});

function initTicketLookup() {
    const form = document.getElementById('ticketLookupForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const code = document.getElementById('ticketCode').value;

        try {
            const response = await api.post('/tickets/lookup', { code });

            if (response.valid) {
                displayTicket(response.ticket);
            } else {
                showError('Tiket tidak ditemukan');
            }

        } catch (error) {
            showError('Gagal mencari tiket');
        }
    });
}

function generateQRCode() {
    const ticketCode = document.getElementById('ticketQRCode')?.dataset.code;
    if (!ticketCode) return;

    const canvas = document.getElementById('qrCodeCanvas');
    if (!canvas) return;

    QRCode.toCanvas(canvas, ticketCode, {
        width: 300,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        }
    });
}

function displayTicket(ticket) {
    // Display ticket details
    const ticketEl = document.getElementById('ticketDetails');
    if (ticketEl) {
        ticketEl.classList.remove('hidden');
        // Populate ticket data
    }
}

console.log('🎟️ Ticket Validation loaded');
