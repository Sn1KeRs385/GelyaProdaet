const spacing0to100 = Object.fromEntries(
  Array.from({ length: 101 }).map((_, index) => [`${index}px`, `${index}px`])
)
const spacing0to500 = Object.fromEntries(
  Array.from({ length: 501 }).map((_, index) => [`${index}px`, `${index}px`])
)

/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: 'tw-',
  content: ['./src/**/*.{vue,js,ts,jsx,tsx}', './index.html'],
  important: true,

  theme: {
    extend: {
      colors: {
        primary: '#604AEC',
        dark: '#222325',
        semidark: '#969696',
        lightdark: '#F0F0F5',
        light: '#FFFFFF',
      },
      borderWidth: {
        ...spacing0to100,
      },

      borderRadius: {
        ...spacing0to100,
      },

      spacing: {
        ...spacing0to500,
      },

      maxWidth: {
        ...spacing0to500,
      },

      minWidth: {
        spacing0to500,
      },

      fontSize: {
        ...spacing0to100,
      },

      lineHeight: {
        100: '100%',
        110: '110%',
        120: '120%',
        130: '130%',
        140: '140%',
      },
    },
  },
}
