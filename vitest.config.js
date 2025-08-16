import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'happy-dom',
    globals: true,
    setupFiles: ['./tests/vue/setup.js'],
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, './resources/js'),
      '~': resolve(__dirname, './resources'),
    },
  },
});