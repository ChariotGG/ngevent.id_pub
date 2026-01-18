import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // CSS
                'resources/css/app.css',

                // JS - Global
                'resources/js/app.js',

                // JS - Admin
                'resources/js/admin/dashboard.js',
                'resources/js/admin/users.js',

                // JS - Organizer
                'resources/js/organizer/event-form.js',
                'resources/js/organizer/analytics.js',

                // JS - Pages
                'resources/js/pages/checkout.js',
                'resources/js/pages/event-detail.js',
            ],
            refresh: true,
        }),
    ],
});
