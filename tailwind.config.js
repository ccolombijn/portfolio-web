/** @type {import('tailwindcss').Config} */
export default {
    content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
      extend: {
        // @theme rule in .scss has prevalence
        fontFamily: {
          sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui'],
        },
      },
    },
    plugins: [],
  }