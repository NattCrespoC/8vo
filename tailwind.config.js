/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    navy: '#102C57',
                    emerald: '#00A86B',
                    accent: '#F59E0B',
                    dark: '#0B1C36',
                }
            }
        },
    },
    plugins: [],
};
