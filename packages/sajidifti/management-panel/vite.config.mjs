import { defineConfig } from 'vite'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)
export default defineConfig({
  plugins: [],
  // Disable Vite's publicDir feature because we build directly into the package `public/` folder.
  // When `outDir` and `publicDir` point to the same folder Vite warns that the feature
  // may not work correctly. We don't need the automatic public dir copy here.
  publicDir: false,
  root: __dirname,
  build: {
    outDir: path.resolve(__dirname, 'public'),
    emptyOutDir: false,
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'resources/js/app.js'),
        style: path.resolve(__dirname, 'resources/css/app.tailwind.css')
      },
      output: {
        entryFileNames: 'js/[name]-[hash].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (chunkInfo) => {
          if (chunkInfo.name && chunkInfo.name.endsWith('.css')) return 'css/[name]-[hash][extname]'
          return 'assets/[name]-[hash][extname]'
        }
      }
    },
    target: 'es2018'
  }
})
