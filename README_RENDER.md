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

4) Add a managed database in Render and set DB_* variables accordingly.
   - Use Render's managed MySQL service. Do not use SQLite in production on Render because the app filesystem is ephemeral.
   - If you want SQLite only, keep the backend local or host it on a server with persistent disk storage instead of Render.
   - Set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in Render.

5) Set the Start Command or Health Check:
   - Render will use the Dockerfile; no custom start command is needed.
   - To run migrations after deploy, configure a deploy hook or run manually via a one-off shell on Render:

```bash
php artisan migrate --force
```

6) Deploy and monitor build logs. If the backend fails, check `Dockerfile`, `composer.lock`, and whether the managed database credentials are correct.

Notes:
- This is the backend deployment guide. Use Render for the Laravel API/backend and Vercel only for frontend static assets.
- This Dockerfile uses `php:8.2-apache` and a simple multi-stage build. For higher-performance production, consider using a dedicated PHP-FPM + Nginx setup or Laravel-specific platforms like Vapor.
