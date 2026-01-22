/**
 * Ticket Scanner JS
 * QR Code scanner with webcam
 */

import QRCode from 'qrcode';
import { showAlert, showError, showLoading, closeLoading } from '../utils/alert';
import api from '../utils/api';

let stream = null;
let scanning = false;

document.addEventListener('DOMContentLoaded', () => {
    initScanner();
});

function initScanner() {
    const startScanBtn = document.getElementById('startScan');
    const stopScanBtn = document.getElementById('stopScan');
    const manualInputBtn = document.getElementById('manualInput');

    if (startScanBtn) {
        startScanBtn.addEventListener('click', startScanning);
    }

    if (stopScanBtn) {
        stopScanBtn.addEventListener('click', stopScanning);
    }

    if (manualInputBtn) {
        manualInputBtn.addEventListener('click', showManualInput);
    }
}

async function startScanning() {
    try {
        const video = document.getElementById('scannerVideo');
        if (!video) return;

        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' }
        });

        video.srcObject = stream;
        video.play();
        scanning = true;

        // Start QR detection
        detectQRCode(video);

        document.getElementById('scannerContainer').classList.remove('hidden');
        document.getElementById('startScan').classList.add('hidden');
        document.getElementById('stopScan').classList.remove('hidden');

    } catch (error) {
        console.error('Scanner error:', error);
        showError('Tidak dapat mengakses kamera');
    }
}

function stopScanning() {
    scanning = false;

    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }

    const video = document.getElementById('scannerVideo');
    if (video) {
        video.srcObject = null;
    }

    document.getElementById('scannerContainer').classList.add('hidden');
    document.getElementById('startScan').classList.removeClass('hidden');
    document.getElementById('stopScan').classList.add('hidden');
}

function detectQRCode(video) {
    if (!scanning) return;

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

    // Use jsQR or similar library to detect QR
    // const code = jsQR(imageData.data, imageData.width, imageData.height);

    // For now, simulate detection
    // if (code) {
    //     validateTicket(code.data);
    //     stopScanning();
    // }

    // Continue scanning
    if (scanning) {
        requestAnimationFrame(() => detectQRCode(video));
    }
}

async function validateTicket(ticketCode) {
    try {
        showLoading('Memvalidasi tiket...');

        const response = await api.post('/organizer/tickets/validate', {
            code: ticketCode
        });

        closeLoading();

        if (response.valid) {
            showAlert('Tiket valid!', 'Berhasil');
            displayTicketInfo(response.ticket);
        } else {
            showError('Tiket tidak valid!', 'Gagal');
        }

    } catch (error) {
        closeLoading();
        showError('Gagal memvalidasi tiket');
    }
}

function showManualInput() {
    const code = prompt('Masukkan kode tiket:');
    if (code) {
        validateTicket(code);
    }
}

function displayTicketInfo(ticket) {
    // Display ticket information
    const infoEl = document.getElementById('ticketInfo');
    if (infoEl) {
        infoEl.innerHTML = `
            <div class="bg-green-500/10 border border-green-500 rounded-lg p-4">
                <h3 class="text-green-400 font-bold mb-2">✓ Tiket Valid</h3>
                <p class="text-gray-300">Nama: ${ticket.name}</p>
                <p class="text-gray-300">Event: ${ticket.event}</p>
                <p class="text-gray-300">Tipe: ${ticket.type}</p>
            </div>
        `;
    }
}

console.log('📷 Ticket Scanner loaded');
