Deploy frontend static assets to Vercel

Overview
- This project uses Vite to build frontend assets into the `public` folder. Vercel can host those static files.
- Note: PHP Blade views and API routes will not run on Vercel. Keep your Laravel backend hosted elsewhere (Render, DigitalOcean, etc.) and point the frontend to that API.

Steps
1. Push your branch to GitHub.

2. In Vercel, create a new project and import this repository.

3. In the project settings (or when prompted during import):
+   - Framework Preset: `Other` (or `Application` if Vercel asks for a preset for static builds).
+   - Build Command: `npm ci && npm run build`
+   - Output Directory: leave blank (we use `vercel.json` which sets `distDir` to `public`).

4. Environment: No server env is required for static assets. If the frontend needs to call the backend API, set `VITE_API_URL` in Vercel Environment Variables and reference it in your frontend code via `import.meta.env.VITE_API_URL`.
   - Example: `https://your-backend-service.onrender.com`

5. Deploy. Vercel will run the build and serve the `public` folder. Your assets will be available under `/build` and other static paths.

Local preview
```bash
npm ci
npm run build
# Serve the public folder locally (requires a static server such as 'serve')
npm i -g serve
serve public -p 5000
```

Notes & Caveats
- Blade templates and Laravel routes require a PHP server — they won't run on Vercel. Use Vercel only for static frontend assets and host your Laravel API elsewhere.
- Asset URLs may be hashed; reference them via Laravel helper or the manifest when integrating with backend.
