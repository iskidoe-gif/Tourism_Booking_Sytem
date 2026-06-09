# Railway Environment Variables Setup Guide

This guide explains how to set up environment variables for Railway deployment.

## CRITICAL: Add PostgreSQL Database First

**Before deploying, you MUST add a PostgreSQL database to your Railway project:**

1. In your Railway project, click "New Service"
2. Select "Database" → "PostgreSQL"
3. Railway will automatically create and link the database
4. Railway will automatically populate these environment variables:
   - `DATABASE_URL` (full connection string)
   - `DB_HOST`
   - `DB_PORT`
   - `DB_USERNAME`
   - `DB_PASSWORD`

**Without a database, the application will not work - no data will be stored and authentication will fail.**

## Required Environment Variables in Railway

Add these variables in your Railway project settings (Project Settings → Variables):

### Core Application Settings
```
APP_NAME=TourismBooking
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app
```

**Note**: Replace `your-app-name.up.railway.app` with your actual Railway URL after deployment.

### Database Settings
After adding the PostgreSQL database, set:
```
DB_CONNECTION=pgsql
DB_DATABASE=tourismdb
```

**Important**: Railway will automatically populate `DB_HOST`, `DB_PORT`, `DB_USERNAME`, and `DB_PASSWORD` from the linked database. You only need to set `DB_CONNECTION=pgsql` and `DB_DATABASE=tourismdb`.

### Cache and Session
```
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Logging
```
LOG_CHANNEL=stack
LOG_LEVEL=info
```

### Railway-Specific Settings
```
RUN_MIGRATIONS=true
RUN_SEEDS=true
```

**Important**: Set `RUN_SEEDS=true` to populate the database with sample data (tour packages, destinations, etc.) This is required for the UI to display content.

### Optional Settings
```
CORS_ALLOW_ORIGIN=*
```

## Setup Steps

### 1. Create Railway Project
1. Go to [Railway Dashboard](https://railway.app/dashboard)
2. Click "New Project" → "Deploy from GitHub repo"
3. Select your repository

### 2. Add PostgreSQL Database
1. In your Railway project, click "Create"
2. Select "Database" → "PostgreSQL"
3. Railway will automatically link it and populate DB_* variables

### 3. Set Environment Variables
1. Go to Project Settings → Variables
2. Add the required variables listed above
3. Railway will automatically add DATABASE_URL and DB_* variables from the linked database

### 4. Deploy
Railway will automatically deploy when you push to GitHub.

## Local Development (.env)

For local development, your `.env` file should use:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Or for SQLite:
```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

## Troubleshooting

### Database Connection Issues
- Ensure PostgreSQL is linked to your app in Railway
- Check that DB_CONNECTION is set to `pgsql`
- Verify Railway has auto-populated DB_HOST, DB_USERNAME, DB_PASSWORD

### APP_KEY Issues
- If APP_KEY is not set, the entrypoint script will auto-generate it
- You can also manually generate: `php artisan key:generate`

### Migration Failures
- Check Railway logs for specific error messages
- Ensure database is properly linked
- Verify DB_DATABASE name matches

## Variable Reference

| Variable | Purpose | Railway Value | Local Value |
|----------|---------|--------------|-------------|
| APP_ENV | Environment | production | local |
| APP_DEBUG | Debug mode | false | true |
| DB_CONNECTION | Database type | pgsql | mysql/sqlite |
| DB_HOST | Database host | (auto) | 127.0.0.1 |
| DB_PORT | Database port | 5432 | 3306 |
| CACHE_DRIVER | Cache backend | file | database |
| SESSION_DRIVER | Session storage | file | database |
