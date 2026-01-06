import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                portal: {
                    navy: '#0f172a',
                    navySoft: '#1e293b',
                    gold: '#fbbf24',
                    sky: '#0ea5e9',
                    background: '#f3f4f6',
                },
            },
        },
    },

    plugins: [forms],
};
