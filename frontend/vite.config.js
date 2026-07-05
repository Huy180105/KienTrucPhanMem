import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [
    tailwindcss(),
  ],
  server: {
    port: 5050,
    proxy: {
      '/api': {
        target: 'http://localhost:8880',
        changeOrigin: true,
        secure: false,
      }
    }
  }
});
