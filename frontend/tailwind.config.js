/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js}'],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#f16722',
          hover: '#d95614',
          tint: '#fff3ed',
        },
        app: '#f4f4f4',
        ink: '#111827',
        muted: '#4b5563',
        success: '#10b981',
      },
      boxShadow: {
        card: '0 1px 3px rgb(17 24 39 / 8%)',
      },
    },
  },
  plugins: [],
};
