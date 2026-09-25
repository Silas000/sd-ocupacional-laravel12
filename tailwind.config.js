import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',

        /*
         * App\Support\Theme guarda as classes da paleta e dos ícones
         * escolhidos em Configurações. Elas não aparecem literais em
         * nenhum Blade, então o arquivo precisa ser varrido para que o
         * Tailwind as gere.
         */
        './app/Support/Theme.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
