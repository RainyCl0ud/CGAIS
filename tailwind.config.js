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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'blue-yellow-gradient': 'linear-gradient(135deg, #FFD700 0%, #fff 50%, #1E3A8A 100%)',
            },
            colors: {
                primaryblue: '#1E3A8A',
                primaryyellow: '#FFD700',
            },
        },
    },

    plugins: [forms],
};
