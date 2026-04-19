import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { globSync } from 'node:fs';
import { resolve } from 'node:path';

const input = globSync('resources/{css,ts}/*.{css,ts}', {
  ignore: ['resources/**/_*', 'resources/**/partials/*'],
  cwd: process.cwd(),
}).map(file => resolve(process.cwd(), file));

export default defineConfig({
  plugins: [
    laravel({
      input: input,
      refresh: true,
    }),
  ],
  server: {
    host: '127.0.0.1',
    port: 5173,
    strictPort: true,
    hmr: {
      host: '127.0.0.1',
    },
  },
});
