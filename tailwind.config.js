/** @type {import('tailwindcss').Config} */
import forms from "@tailwindcss/forms";

export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                playfair: ["Playfair Display", "serif"],
                lora: ["Lora", "serif"],
                "open-sans": ["Open Sans", "sans-serif"],
            },
            colors: {
                "primary-green": "#21a055",
                "dark-green": "#006400",
                "muted-coral": "#D4A5A5",
                gold: "#D4AF37",
                "dark-red": "#e53137",
                "light-gray": "#f8f9fa",
                "dark-gray": "#6c757d",
                primary: "#28a745",
                secondary: "#20c997",
                accent: "#17a2b8",
                dark: "#1a1a1a",
                light: "#f5f5f5",
                surface: "#f8f9fa",
                glass: "rgba(255, 255, 255, 0.1)",
            },

            fontFamily: {
                playfair: ["Playfair Display", "serif"],
                inter: ["Inter", "sans-serif"],
                "open-sans": ["Open Sans", "sans-serif"],
            },
        },
    },
    plugins: [forms],
};
