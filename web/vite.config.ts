import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import components from 'unplugin-vue-components/vite'
import autoImport from 'unplugin-auto-import/vite'
import UnoCSS from 'unocss/vite'
import { vitePluginForArco } from '@arco-plugins/vite-vue'
import { resolve } from 'node:path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueJsx(),
    UnoCSS(),
    vitePluginForArco({
      style: 'css'
    }),
    components({
      dirs: ['src/components'],
      resolvers: [],
      dts: './src/components.d.ts'
    }),
    autoImport({
      imports: [
        'vue',
        'pinia',
        'vue-router',
        {
          '@arco-design/web-vue': ['Message', 'Notification'],
          '@vueuse/core': [
            'useDebounce',
            'useDebounceFn',
            'useThrottle',
            'useStorage',
            'useLocalStorage',
            'useSession'
          ]
        }
      ],
      resolvers: [],
      dts: './src/auto-imports.d.ts',
      dirs: [
        'src/router',
        'src/stores',
        'src/types',
        'src/utils',
        'src/api',
        'src/hooks',
        'src/components/*/**'
      ],
      eslintrc: {
        enabled: true
      }
    })
  ],
  css: {
    preprocessorOptions: {
      less: {
        modifyVars: {
          hack: `true; @import (reference) "${resolve('src/assets/style/breakpoint.less')}";`
        },
        javascriptEnabled: true
      }
    }
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    port: 8013,
    host: '0.0.0.0'
  },
  base: '/admin/',
  build: {
    //outDir: '../../../../public/admin/',
    rollupOptions: {
      output: {
        entryFileNames: 'assets/[hash].js',
        chunkFileNames: 'assets/[hash].js',
        assetFileNames: () => {
          return `assets/[hash][extname]`
        },
        manualChunks(id) {
          if (id.includes('node_modules')) {
            return 'vendor'
          }
        }
      }
    }
  }
})
