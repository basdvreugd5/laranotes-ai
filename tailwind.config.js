import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                gray: {
                    900: '#101822', // App background
                    850: '#151D2C', // Nav background
                    800: '#1A2332', // Card background
                    700: '#232E42', // Input background
                    600: '#3A4B6B', // Borders
                    400: '#8A9BB7', // Secondary text
                },
                blue: {
                    500: '#3B82F6', // Primary accent
                    600: '#2563EB', // Hover state
                }
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};