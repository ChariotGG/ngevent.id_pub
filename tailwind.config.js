import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                // Primary brand colors
                primary: {
                    DEFAULT: '#FF8FC7',
                    hover: '#E67CAF',
                    active: '#CC6997',
                },
                // Background colors (dark mode)
                background: {
                    DEFAULT: '#0A0A0A',
                    card: '#1A1A1A',
                    elevated: '#2A2A2A',
                },
                // Text colors
                text: {
                    primary: '#FAFAFA',
                    secondary: '#A3A3A3',
                    accent: '#FF8FC7',
                },
                // Semantic colors
                semantic: {
                    success: '#10B981',
                    error: '#EF4444',
                    warning: '#F59E0B',
                    info: '#3B82F6',
                }
            },
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
