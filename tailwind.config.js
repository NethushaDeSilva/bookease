import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Corner-radius convention for this app (not enforced via theme overrides,
    // since Jetstream/vendor views rely on Tailwind's default scale):
    // rounded-md   -> inputs, checkboxes, small badges/tags
    // rounded-lg   -> buttons
    // rounded-xl   -> cards, panels
    // rounded-2xl+ -> marketing/auth hero surfaces only
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
