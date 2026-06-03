Render deployment instructions

1) Push your repository to GitHub (branch `main`).

2) Sign in to Render and create a new Web Service.
   - Connect your GitHub repo and select this repository.
   - Environment: Docker (Render will build using the `Dockerfile`).
   - Branch: `main`.

3) Set Environment Variables in Render's dashboard (Service → Environment):
   - Add the variables from `.env.production.example` and set `APP_KEY` to a value generated locally:

```bash
php artisan key:generate --show
```

Copy the output and paste it into `APP_KEY` on Render.

4) (Optional) Add a managed database in Render and set DB_* variables accordingly.

5) Set the Start Command or Health Check:
   - Render will use the Dockerfile; no custom start command is needed.
   - To run migrations after deploy, configure a deploy hook or run manually via a one-off shell on Render:

```bash
php artisan migrate --force
```

6) Deploy and monitor build logs. If assets fail to build, make sure `vite.config.js` and `package.json` scripts are correctly configured.

Notes:
- This Dockerfile uses `php:8.1-apache` and a simple multi-stage build. For higher-performance production, consider using a dedicated PHP-FPM + Nginx setup or Laravel-specific platforms like Vapor.
