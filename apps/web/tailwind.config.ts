import type { Config } from 'tailwindcss'

const config: Config = {
  content: ['./src/**/*.{js,ts,jsx,tsx,mdx}'],
  theme: {
    extend: {
      colors: {
        brand: {
          black: '#080C12',
          dark: '#0B1018',
          surface: '#141D2B',
          amber: '#E8922A',
          amber2: '#F5B050',
          muted: '#7088A8',
          text: '#DCE5F2',
          green: '#2ECC8A',
          red: '#E05252',
          blue: '#4A9FE0',
        },
      },
      fontFamily: {
        sans: ['Outfit', 'sans-serif'],
        serif: ['Cormorant Garamond', 'serif'],
        mono: ['JetBrains Mono', 'monospace'],
      },
    },
  },
  plugins: [],
}

export default config
