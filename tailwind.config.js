/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./*.html"],
  theme: {
    extend: {
      fontFamily: {
        bitcount: ['"Bitcount Grid Single"', "sans-serif"],
        jersey: ['"Jersey 20 Charted"', "sans-serif"],
      },
    },
  },
  plugins: [],
}
