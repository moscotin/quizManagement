import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],

    safelist: [
        // -------------------------
        // POSITIONING (from before)
        // -------------------------
        { pattern: /^(top|right|bottom|left)-\d+$/ },
        { pattern: /^-(top|right|bottom|left)-\d+$/ },
        { pattern: /^inset(-[xy])?-\d+$/ },
        { pattern: /^-inset(-[xy])?-\d+$/ },

        // -------------------------
        // WIDTHS (core)
        // -------------------------
        // w-0, w-1, w-48, w-96, etc.
        { pattern: /^w-\d+$/ },

        // w-px
        { pattern: /^w-px$/ },

        // w-full, w-screen, w-auto, w-fit, w-min, w-max
        { pattern: /^w-(full|screen|auto|min|max|fit)$/ },

        // w-1/2, w-3/4, w-2/5, etc.
        { pattern: /^w-\d+\/\d+$/ },

        // -------------------------
        // MAX / MIN WIDTHS
        // -------------------------
        { pattern: /^(min-w|max-w)-\d+$/ },
        { pattern: /^(min-w|max-w)-(full|screen|min|max|fit)$/ },
        { pattern: /^(min-w|max-w)-\d+\/\d+$/ },

        // -------------------------
        // ARBITRARY WIDTHS
        // -------------------------
        // w-[123px], w-[10rem], w-[42%]
        { pattern: /^w-\[[^\]]+]$/ },
        { pattern: /^(min-w|max-w)-\[[^\]]+]$/ },

        // -------------------------
        // HEIGHTS (core)
        // -------------------------
        // h-0, h-1, h-48, h-96, etc.
        { pattern: /^h-\d+$/ },

        // h-px
        { pattern: /^h-px$/ },

        // h-full, h-screen, h-auto, h-fit, h-min, h-max
        { pattern: /^h-(full|screen|auto|min|max|fit)$/ },

        // h-1/2, h-3/4, h-2/5, etc.
        { pattern: /^h-\d+\/\d+$/ },

        // -------------------------
        // MAX / MIN HEIGHTS
        // -------------------------
        { pattern: /^(min-h|max-h)-\d+$/ },
        { pattern: /^(min-h|max-h)-(full|screen|min|max|fit)$/ },
        { pattern: /^(min-h|max-h)-\d+\/\d+$/ },

        // -------------------------
        // ARBITRARY HEIGHTS
        // -------------------------
        // h-[123px], h-[10rem], h-[42%]
        { pattern: /^h-\[[^\]]+]$/ },
        { pattern: /^(min-h|max-h)-\[[^\]]+]$/ },
    ],
};
