// tailwind.config.cjs
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        colors : {
            white_transp : '#ffffff70'
        },
      extend: {
        keyframes: {
          'pan-clouds': {
            '0%': { transform: 'translateX(0)' },
            '100%': { transform: 'translateX(-50%)' },
          }
        },
        animation: {
          'pan-clouds': 'pan-clouds 180s linear infinite',
        }
      },
    },
    plugins: [],
  }