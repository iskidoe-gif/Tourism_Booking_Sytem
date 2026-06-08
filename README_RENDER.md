# Render Deployment Guide for Tourism Booking System

This guide covers deploying the complete Tourism Booking System (backend + frontend) to Render.com using the `render.yaml` configuration file.

## Quick Start (Recommended)

The easiest way to deploy is using the `render.yaml` configuration file included in this repository:

1. **Push your code to GitHub** (branch `main`)
2. **Create a new Web Service on Render**:
   - Go to [dashboard.render.com](https://dashboard.render.com)
   - Click "New +" → "Web Service"
   - Connect your GitHub repository
   - Select the repository and branch `main`
   - Render will automatically detect the `render.yaml` file
   - Click "Create Web Service"

3. **Render will automatically**:
   - Build the Docker image using the included `Dockerfile`
   - Create a PostgreSQL database
   - Set up environment variables
   - Run migrations on deployment
   - Start the application

4. **Access your application** at the URL provided by Render (e.g., `https://your-app-name.onrender.com`)

## Manual Deployment

If you prefer manual configuration instead of using `render.yaml`:

### Step 1: Create PostgreSQL Database

1. Go to Render Dashboard → "New +" → "PostgreSQL"
2. Name: `tourism-db` (or your preferred name)
3. Database: `tourism_booking`
4. User: `tourism_user`
5. Region: Choose closest to your users
6. Plan: Free (or paid for better performance)
7. Click "Create Database"

### Step 2: Create Web Service

1. Go to Render Dashboard → "New +" → "Web Service"
2. Connect your GitHub repository
3. **Settings**:
   - Name: `tourism-booking-system`
   - Region: Same as your database
   - Branch: `main`
   - Runtime: Docker
   - Dockerfile Path: `./Dockerfile`
   - Plan: Free (or paid)

### Step 3: Configure Environment Variables

In your Web Service → "Environment" tab, add these variables:

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
DB_CONNECTION=pgsql
DB_HOST=<from database page>
DB_PORT=5432
DB_DATABASE=<from database page>
DB_USERNAME=<from database page>
DB_PASSWORD=<from database page>
RUN_MIGRATIONS=true
RUN_SEEDS=false
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CORS_ALLOW_ORIGIN=*
```

**Generate APP_KEY locally:**
```bash
php artisan key:generate --show
```
Copy the output and paste it into the `APP_KEY` environment variable.

### Step 4: Deploy

Click "Create Web Service" and monitor the build logs.

## Project Architecture

This is a **monolithic Laravel application** that includes both backend and frontend:

- **Backend**: Laravel 12 framework with PHP 8.2
- **Frontend**: Laravel Blade templates with Vite-built assets (CSS/JS)
- **Database**: PostgreSQL (Render managed)
- **Asset Building**: Frontend assets are built during Docker image creation

## Environment Variables

### Required Variables
- `APP_KEY`: Laravel application key (generate with `php artisan key:generate --show`)
- `APP_URL`: Your Render application URL
- `DB_*`: Database connection settings (auto-configured by render.yaml)

### Optional Variables
- `RUN_MIGRATIONS`: Run database migrations on startup (default: `true`)
- `RUN_SEEDS`: Run database seeders on startup for demo data (default: `false`)
- `FORCE_APP_DEBUG`: Enable debug mode for troubleshooting (default: `false`)

## Features

### Automatic Migrations
The Docker entrypoint automatically runs migrations on container startup when `RUN_MIGRATIONS=true`. This ensures your database schema is always up to date.

### Asset Building
Frontend assets are built during the Docker image creation process:
- CSS is built with Tailwind CSS via Vite
- JavaScript is bundled with Vite
- Assets are output to `public/build/`

### Health Check
The application includes a health check endpoint at `/health` that Render uses to monitor service health.

## Troubleshooting

### Build Failures
- Check the Docker build logs in Render dashboard
- Ensure `composer.json` and `package.json` are valid
- Verify all dependencies are compatible with PHP 8.2

### Runtime Errors
- Enable debug mode temporarily: set `FORCE_APP_DEBUG=true`
- Check logs: Render Dashboard → Logs
- Verify database connection settings
- Ensure PostgreSQL database is accessible

### Database Issues
- Verify database credentials match Render's database page
- Check if migrations ran successfully in logs
- Test database connection using Render's shell (paid plans)

### Asset Loading Issues
- Clear config cache: set environment variable to trigger cache rebuild
- Check that Vite build completed successfully during Docker build
- Verify asset paths in compiled views

## Post-Deployment Setup

### 1. Set APP_URL
Update the `APP_URL` environment variable to match your Render application URL.

### 2. Create Admin User
Access `/admin/login` and create an admin user, or run a seeder:
```bash
# In Render shell (paid plan) or locally with remote DB
php artisan db:seed --class=AdminSeeder --force
```

### 3. Configure Email (Optional)
Set up mail settings for notifications:
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### 4. Enable Seeders for Demo Data (Optional)
To populate the database with demo data:
```bash
RUN_SEEDS=true
```
Then redeploy. Seeders are idempotent and won't duplicate data on subsequent deployments.

## Scaling Considerations

### Free Tier Limitations
- Free instances spin down after inactivity
- Database connections may be limited
- File storage is ephemeral (use object storage for uploads)

### Production Recommendations
- Upgrade to paid plans for better performance
- Use Render's disk storage for persistent file uploads
- Configure Redis for caching and sessions
- Set up a CDN for static assets
- Enable SSL (automatic on Render)
- Configure monitoring and error tracking

## Maintenance

### Running Migrations
Migrations run automatically on deployment. To run manually:
```bash
# In Render shell
php artisan migrate --force
```

### Clearing Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Viewing Logs
- Render Dashboard → Your Service → Logs
- Application logs are also in `storage/logs/laravel.log`

## Support

For issues specific to this deployment:
1. Check Render status: [status.render.com](https://status.render.com)
2. Review deployment logs in Render Dashboard
3. Verify environment variables are set correctly
4. Check database connectivity and status

## Architecture Notes

- **Single Service**: Backend and frontend are deployed together as one Docker container
- **Frontend**: Uses Laravel Blade templates with Vite for asset compilation
- **API**: Includes REST API endpoints for mobile/integration (see routes/api.php)
- **Authentication**: Session-based for web, token-based for API
- **File Uploads**: Local storage (consider S3/R2 for production)

## Next Steps

After successful deployment:
1. Test all user flows (booking, registration, admin panel)
2. Configure email notifications
3. Set up monitoring and alerting
4. Configure custom domain (Render Dashboard → Settings → Domains)
5. Set up backup strategy for database
