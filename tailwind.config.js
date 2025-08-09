import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f3f2ff',
                    100: '#e8e5ff',
                    200: '#d4cefd',
                    300: '#b6a9fa',
                    400: '#9279f5',
                    500: '#7556ee', // PHP紫色為基底
                    600: '#6442e3',
                    700: '#5533ca',
                    800: '#472ba6',
                    900: '#3c2685',
                    950: '#241757',
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
