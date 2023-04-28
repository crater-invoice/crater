import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    server: {
      watch: {
        ignored: ['**/.env/**'],
      },
    },
    plugins: [
      vue(),
      laravel({
        input: ['resources/scripts/main.js'],
        valetTls: false,
      }),
    ],
    resolve: {
      alias: {
        '@': '/resources',
        'vue-i18n': 'vue-i18n/dist/vue-i18n.cjs.js'
      },
    },
  })