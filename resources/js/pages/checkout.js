/**
 * Checkout Page JS
 * Xendit payment integration
 */

import { showError, showLoading, closeLoading } from '../utils/alert';
import api from '../utils/api';

document.addEventListener('DOMContentLoaded', () => {
    initCheckoutForm();
    initPaymentPolling();
});

function initCheckoutForm() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
            showLoading('Memproses pembayaran...');

            const formData = new FormData(form);
            const response = await api.post('/checkout/process', formData);

            closeLoading();

            // Redirect to payment page
            if (response.payment_url) {
                window.location.href = response.payment_url;
            }

        } catch (error) {
            closeLoading();
            console.error('Checkout error:', error);
        }
    });
}

function initPaymentPolling() {
    const orderId = document.getElementById('orderId')?.value;
    if (!orderId) return;

    // Poll payment status every 3 seconds
    const interval = setInterval(async () => {
        try {
            const response = await api.get(`/checkout/status/${orderId}`);

            if (response.status === 'paid') {
                clearInterval(interval);
                window.location.href = `/checkout/success/${orderId}`;
            } else if (response.status === 'failed' || response.status === 'expired') {
                clearInterval(interval);
                window.location.href = `/checkout/failed/${orderId}`;
            }

        } catch (error) {
            console.error('Polling error:', error);
        }
    }, 3000);

    // Stop polling after 10 minutes
    setTimeout(() => {
        clearInterval(interval);
    }, 600000);
}

console.log('💳 Checkout loaded');
