const colors = require('tailwindcss/colors')
const svgToDataUri = require('mini-svg-data-uri')

module.exports = {
  mode: 'jit',
  purge: [
    './resources/views/**/*.php',
    './resources/scripts/**/*.js',
    './resources/scripts/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#F7F6FD',
          100: '#EEEEFB',
          200: '#D5D4F5',
          300: '#BCB9EF',
          400: '#8A85E4',
          500: '#5851D8',
          600: '#4F49C2',
          700: '#353182',
          800: '#282461',
          900: '#1A1841',
        },
        black: '#040405',
        red: colors.red,
        teal: colors.teal,
        gray: colors.blueGray,
      },
      spacing: {
        88: '22rem',
      },
      backgroundImage: (theme) => ({
        'multiselect-caret': `url("${svgToDataUri(
          `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
  <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
</svg>`
        )}")`,
        'multiselect-spinner': `url("${svgToDataUri(
          `<svg viewBox="0 0 512 512" fill="${theme(
            'colors.primary.500'
          )}" xmlns="http://www.w3.org/2000/svg"><path d="M456.433 371.72l-27.79-16.045c-7.192-4.152-10.052-13.136-6.487-20.636 25.82-54.328 23.566-118.602-6.768-171.03-30.265-52.529-84.802-86.621-144.76-91.424C262.35 71.922 256 64.953 256 56.649V24.56c0-9.31 7.916-16.609 17.204-15.96 81.795 5.717 156.412 51.902 197.611 123.408 41.301 71.385 43.99 159.096 8.042 232.792-4.082 8.369-14.361 11.575-22.424 6.92z"></path></svg>`
        )}")`,
        'multiselect-remove': `url("${svgToDataUri(
          `<svg viewBox="0 0 320 512" fill="${theme(
            'colors.white'
          )}" xmlns="http://www.w3.org/2000/svg"><path d="M207.6 256l107.72-107.72c6.23-6.23 6.23-16.34 0-22.58l-25.03-25.03c-6.23-6.23-16.34-6.23-22.58 0L160 208.4 52.28 100.68c-6.23-6.23-16.34-6.23-22.58 0L4.68 125.7c-6.23 6.23-6.23 16.34 0 22.58L112.4 256 4.68 363.72c-6.23 6.23-6.23 16.34 0 22.58l25.03 25.03c6.23 6.23 16.34 6.23 22.58 0L160 303.6l107.72 107.72c6.23 6.23 16.34 6.23 22.58 0l25.03-25.03c6.23-6.23 6.23-16.34 0-22.58L207.6 256z"></path></svg>`
        )}")`,
      }),
    },

    fontFamily: {
      base: ['Poppins', 'sans-serif'],
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('tailwind-scrollbar'),
    require('@rvxlab/tailwind-plugin-ios-full-height'),
  ],
  variants: {
    scrollbar: ['rounded'],
  },
}
