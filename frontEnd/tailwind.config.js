/** @type {import('tailwindcss').Config} */
import flowbite from 'flowbite/plugin';
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',// Inclui todos os arquivos Vue e JS/TS
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {}, // Aqui você pode personalizar o tema do Tailwind
  },
  plugins: [flowbite],
}

