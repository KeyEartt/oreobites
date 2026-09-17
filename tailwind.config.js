/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Cookie (dark)
        'oreo-noir':    '#1A1A1A',
        'cookie-brown': '#3E2723',
        'chocolate':    '#6B4423',
        // Milk (light)
        'milk-cream':   '#FAF3E0',
        'vanilla':      '#FFFFFF',
        // Golden accent
        'golden':       '#C9A961',
      },
      fontFamily: {
        display: ['Outfit', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        sans:    ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        mono:    ['JetBrains Mono', 'ui-monospace', 'monospace'],
      },
      boxShadow: {
        'soft':  '0 2px 8px rgba(26, 26, 26, 0.06)',
        'card':  '0 4px 16px rgba(26, 26, 26, 0.08)',
        'lift':  '0 8px 24px rgba(26, 26, 26, 0.12)',
      },
      animation: {
        'pulse-soft': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
    },
  },
  plugins: [],
}