import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            colors: {
                "tertiary-container": "#ac3b12",
                "error-container": "#ffdad6",
                "surface-dim": "#dcdad6",
                "on-background": "#1b1c1a",
                "inverse-on-surface": "#f3f0ed",
                "on-secondary": "#ffffff",
                "on-primary-fixed": "#002117",
                "on-error-container": "#93000a",
                "on-secondary-fixed": "#0d1f1b",
                "secondary-fixed": "#d3e7e0",
                "outline-variant": "#bec9c3",
                background: "#fcf9f5",
                "primary-fixed": "#a0f3d4",
                surface: "#fcf9f5",
                "secondary-fixed-dim": "#b7cbc4",
                "on-tertiary-fixed": "#3a0b00",
                "on-surface": "#1b1c1a",
                error: "#ba1a1a",
                "on-primary-container": "#9aedcf",
                secondary: "#50625d",
                outline: "#6f7a74",
                "on-primary-fixed-variant": "#00513e",
                "on-tertiary-container": "#ffd3c6",
                "on-secondary-container": "#566863",
                "tertiary-fixed-dim": "#ffb59e",
                "on-error": "#ffffff",
                "surface-bright": "#fcf9f5",
                "surface-container-lowest": "#ffffff",
                "surface-variant": "#e5e2df",
                "surface-tint": "#086b53",
                "on-primary": "#ffffff",
                "inverse-primary": "#84d6b9",
                primary: "#005440",
                "surface-container-high": "#eae8e4",
                "surface-container": "#f0edea",
                "on-tertiary": "#ffffff",
                tertiary: "#892600",
                "primary-fixed-dim": "#84d6b9",
                "tertiary-fixed": "#ffdbd0",
                "surface-container-highest": "#e5e2df",
                "on-surface-variant": "#3f4944",
                "primary-container": "#0f6e56",
                "inverse-surface": "#30302e",
                "on-tertiary-fixed-variant": "#852400",
                "secondary-container": "#d3e7e0",
                "surface-container-low": "#f6f3f0",
                "on-secondary-fixed-variant": "#394a45",
            },

            borderRadius: {
                DEFAULT: "0.25rem",
                lg: "0.5rem",
                xl: "0.75rem",
                full: "9999px",
            },

            fontFamily: {
                headline: ["Inter", ...defaultTheme.fontFamily.sans],
                body: ["Inter", ...defaultTheme.fontFamily.sans],
                label: ["Inter", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
