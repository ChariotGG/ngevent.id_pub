import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global Alpine components
Alpine.data('countdown', (expiresAt) => ({
    remaining: 0,
    minutes: '00',
    seconds: '00',
    expired: false,

    init() {
        this.updateCountdown();
        setInterval(() => this.updateCountdown(), 1000);
    },

    updateCountdown() {
        const now = new Date().getTime();
        const target = new Date(expiresAt).getTime();
        this.remaining = Math.max(0, Math.floor((target - now) / 1000));

        if (this.remaining <= 0) {
            this.expired = true;
            this.minutes = '00';
            this.seconds = '00';
            return;
        }

        this.minutes = String(Math.floor(this.remaining / 60)).padStart(2, '0');
        this.seconds = String(this.remaining % 60).padStart(2, '0');
    }
}));

Alpine.data('ticketSelector', () => ({
    quantities: {},
    total: 0,

    init() {
        this.calculateTotal();
    },

    updateQuantity(variantId, price, change) {
        const current = this.quantities[variantId] || 0;
        const newValue = Math.max(0, current + change);
        this.quantities[variantId] = newValue;
        this.calculateTotal();
    },

    setQuantity(variantId, value) {
        this.quantities[variantId] = Math.max(0, parseInt(value) || 0);
        this.calculateTotal();
    },

    calculateTotal() {
        this.total = 0;
        for (const [variantId, quantity] of Object.entries(this.quantities)) {
            const priceEl = document.querySelector(`[data-variant-price="${variantId}"]`);
            if (priceEl) {
                this.total += quantity * parseInt(priceEl.value);
            }
        }
    },

    getTotalQuantity() {
        return Object.values(this.quantities).reduce((a, b) => a + b, 0);
    },

    canCheckout() {
        return this.getTotalQuantity() > 0;
    }
}));

Alpine.data('imagePreview', () => ({
    preview: null,

    handleFileChange(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.preview = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },

    clearPreview() {
        this.preview = null;
    }
}));

Alpine.start();

// Format currency helper
window.formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
};
