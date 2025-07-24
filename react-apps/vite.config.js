import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { resolve } from 'path'

export default defineConfig({
  plugins: [react()],
  define: {
    'process.env.NODE_ENV': '"production"'
  },
  build: {
    outDir: '../react-dist',
    emptyOutDir: true,
    lib: {
      entry: resolve(__dirname, 'src/main.jsx'),
      formats: ['iife'],
      name: 'MoodleReact',
      fileName: (format) => `moodle-react.${format}.js`
    },
    rollupOptions: {
      external: [],
      output: {
        // Ensure React and ReactDOM are bundled
        globals: {},
        // Single file output
        inlineDynamicImports: true,
      }
    }
  },
  server: {
    port: 5173,
    cors: true,
    hmr: {
      protocol: 'ws',
      host: 'localhost'
    }
  }
})