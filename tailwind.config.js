import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],
    darkMode: "class",

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Space Grotesk"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: "#1e3a8a", // Navy Blue
                    50: "#eff6ff",
                    100: "#dbeafe",
                    200: "#bfdbfe",
                    300: "#93c5fd",
                    400: "#60a5fa",
                    500: "#3b82f6",
                    600: "#2563eb",
                    700: "#1d4ed8",
                    800: "#1e40af",
                    900: "#1e3a8a",
                    950: "#172554",
                },
                secondary: {
                    DEFAULT: "#c89b4f", // Gold
                    50: "#fdf8f0",
                    100: "#faedd8",
                    200: "#f3d7ad",
                    300: "#eabf7c",
                    400: "#e1a451",
                    500: "#d68b36",
                    600: "#ba6f2b",
                    700: "#9b5526",
                    800: "#7e4425",
                    900: "#653720",
                    950: "#361b0f",
                },
            },
        },
    },

    plugins: [forms],
};
