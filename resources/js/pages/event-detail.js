/**
 * Event Detail Page JS
 * Image lightbox, social share
 */

import { showError } from '../utils/alert';

document.addEventListener('DOMContentLoaded', () => {
    initImageGallery();
    initSocialShare();
    initTicketSelection();
});

function initImageGallery() {
    const images = document.querySelectorAll('[data-gallery-image]');

    images.forEach(img => {
        img.addEventListener('click', () => {
            openLightbox(img.src, img.alt);
        });
    });
}

function openLightbox(src, alt) {
    const lightbox = document.createElement('div');
    lightbox.className = 'fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center p-4';
    lightbox.innerHTML = `
        <button class="absolute top-4 right-4 text-white text-4xl" onclick="this.parentElement.remove()">×</button>
        <img src="${src}" alt="${alt}" class="max-w-full max-h-full object-contain">
    `;
    document.body.appendChild(lightbox);

    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            lightbox.remove();
        }
    });
}

function initSocialShare() {
    const shareBtn = document.getElementById('shareBtn');
    if (!shareBtn) return;

    shareBtn.addEventListener('click', async () => {
        const url = window.location.href;
        const title = document.querySelector('h1')?.textContent || 'Event';

        if (navigator.share) {
            try {
                await navigator.share({ title, url });
            } catch (error) {
                console.log('Share cancelled');
            }
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(url);
            showAlert('Link disalin ke clipboard!');
        }
    });
}

function initTicketSelection() {
    // Already handled by Alpine.js in app.js
    // This is just for additional JS if needed
}

console.log('🎫 Event Detail loaded');
