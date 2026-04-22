/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        // Terracotta Cartography Palette
        copper: {
          DEFAULT: '#B8751A',
          mid: '#D4924D',
          light: '#F0B96B',
          pale: '#FDF5EB',
        },
        ivory: {
          DEFAULT: '#F7F2EC',
          alt: '#FDFAF6',
          deep: '#EDE5D8',
        },
        ink: {
          DEFAULT: '#1C1410',
          1: '#2A2018',
          2: '#3D3025',
        },
        malachite: {
          DEFAULT: '#0F5132',
          v: '#1A7A4A',
          pale: '#E8F5EE',
        },
        sienna: {
          DEFAULT: '#A0341E',
          v: '#C84B2F',
          pale: '#FBF0ED',
        },
        text: {
          primary: '#1A1208',
          mid: '#5C4F3D',
          soft: '#9C8E7A',
        },
        border: {
          DEFAULT: '#C8B99A',
          light: '#EDE5D8',
        },
      },
      fontFamily: {
        display: ['"Playfair Display"', 'Georgia', 'serif'],
        sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
      },
      animation: {
        'grain-shift': 'grainShift 0.6s steps(2) infinite',
        'marquee-roll': 'marqueeRoll 26s linear infinite',
        'word-reveal': 'wordReveal 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards',
        'fade-up': 'fadeUp 0.8s ease forwards',
        'fc-in': 'fcIn 0.9s cubic-bezier(0.16, 1, 0.3, 1) both',
        'fc-float': 'fcFloat 5.2s ease-in-out infinite',
        'toast-in': 'toastIn 0.4s cubic-bezier(0.16, 1, 0.3, 1)',
        'glimmer': 'glimmer 3.5s ease-in-out infinite',
      },
      keyframes: {
        grainShift: {
          '0%': { transform: 'translate(0, 0)' },
          '33%': { transform: 'translate(-3px, 2px)' },
          '66%': { transform: 'translate(2px, -3px)' },
          '100%': { transform: 'translate(-1px, 3px)' },
        },
        marqueeRoll: {
          to: { transform: 'translateX(-50%)' },
        },
        wordReveal: {
          to: { transform: 'translateY(0)', opacity: '1' },
        },
        fadeUp: {
          from: { opacity: '0', transform: 'translateY(10px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        fcIn: {
          from: { opacity: '0', transform: 'translateY(20px) scale(0.96)' },
          to: { opacity: '1', transform: 'none' },
        },
        fcFloat: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-12px)' },
        },
        toastIn: {
          from: { opacity: '0', transform: 'translateX(20px)' },
          to: { opacity: '1', transform: 'none' },
        },
        glimmer: {
          '0%, 100%': { left: '-100%' },
          '50%': { left: '150%' },
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}