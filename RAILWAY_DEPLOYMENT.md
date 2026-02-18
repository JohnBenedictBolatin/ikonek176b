# Railway Deployment Guide for Laravel Application

## Prerequisites

1. **GitHub Account** - Your code needs to be in a GitHub repository
2. **Railway Account** - Sign up at [railway.app](https://railway.app) (free tier available)
3. **Node.js & Composer** - Installed locally (for testing builds)

---

## Step 1: Prepare Your Repository

### 1.1 Ensure your code is on GitHub

```bash
# If not already done, initialize git and push to GitHub
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
git push -u origin main
```

### 1.2 Create `.gitignore` (if not exists)

Make sure these are ignored:
- `.env`
- `node_modules/`
- `vendor/`
- `storage/logs/*`
- `.phpunit.result.cache`

---

## Step 2: Create Railway Account & Project

### 2.1 Sign up for Railway
1. Go to [railway.app](https://railway.app)
2. Click "Start a New Project"
3. Sign in with GitHub (recommended for easy deployment)

### 2.2 Create New Project
1. Click "New Project"
2. Select "Deploy from GitHub repo"
3. Choose your repository
4. Railway will auto-detect it's a Laravel app

---

## Step 3: Add MySQL Database

### 3.1 Add MySQL Service
1. In your Railway project dashboard, click "+ New"
2. Select "Database" → "Add MySQL"
3. Railway will create a MySQL database automatically
4. Note the connection details (you'll need them in Step 4)

---

## Step 4: Configure Environment Variables

### 4.1 Access Environment Variables
1. Click on your **Web Service** (not the database)
2. Go to the "Variables" tab
3. Add the following variables:

### 4.2 Required Environment Variables

```env
# Application
APP_NAME="Your App Name"
APP_ENV=production
APP_KEY=                    # Will be generated in Step 5
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app

# Database (from MySQL service)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (configure based on your needs)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Vite (for asset building)
VITE_APP_NAME="${APP_NAME}"
```

**Important Notes:**
- `${{MySQL.MYSQLHOST}}` syntax references Railway's MySQL service variables
- Replace `your-app-name.up.railway.app` with your actual Railway URL
- Set `APP_DEBUG=false` in production
- Generate `APP_KEY` in Step 5

---

## Step 5: Configure Build & Deploy Settings

### 5.1 Set Build Command
1. In your Web Service, go to "Settings"
2. Under "Build Command", set:
```bash
composer install --no-dev --optimize-autoloader && npm ci && npm run build
```

### 5.2 Set Start Command
Under "Start Command", set:
```bash
php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php -S 0.0.0.0:$PORT -t public
```

### 5.3 Set Root Directory (if needed)
Leave blank unless your Laravel app is in a subdirectory.

---

## Step 6: Generate Application Key

### Option A: Using Railway CLI (Recommended)
1. Install Railway CLI: `npm i -g @railway/cli`
2. Login: `railway login`
3. Link project: `railway link`
4. Generate key: `railway run php artisan key:generate`
5. Copy the generated key to Railway dashboard → Variables → `APP_KEY`

### Option B: Using Railway Dashboard
1. Go to your Web Service → "Deployments" tab
2. Click on the latest deployment → "View Logs"
3. Open the "Shell" tab
4. Run: `php artisan key:generate --show`
5. Copy the key and add it to Environment Variables as `APP_KEY`

---

## Step 7: Run Database Migrations

### 7.1 First Migration
1. Go to your Web Service → "Deployments"
2. Click on latest deployment → "Shell"
3. Run:
```bash
php artisan migrate --force
```

### 7.2 (Optional) Seed Database
If you have seeders:
```bash
php artisan db:seed --force
```

---

## Step 8: Configure Storage

### 8.1 Create Storage Link
In Railway Shell:
```bash
php artisan storage:link
```

**Note:** Railway's filesystem is ephemeral. For production, consider:
- Using S3-compatible storage (AWS S3, DigitalOcean Spaces, etc.)
- Or Railway's Volume service for persistent storage

---

## Step 9: Set Up Queue Worker (Optional)

If your app uses queues (you have `QUEUE_CONNECTION=database`):

### 9.1 Add Queue Worker Service
1. In Railway dashboard, click "+ New"
2. Select "Empty Service"
3. Name it "Queue Worker"
4. Connect it to the same GitHub repo
5. Set Build Command: `composer install --no-dev --optimize-autoloader`
6. Set Start Command: `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
7. Copy all environment variables from your Web Service

---

## Step 10: Configure Custom Domain (Optional)

### 10.1 Add Custom Domain
1. Go to your Web Service → "Settings"
2. Scroll to "Domains"
3. Click "Generate Domain" or "Add Custom Domain"
4. Update `APP_URL` in environment variables to match

---

## Step 11: Deploy & Test

### 11.1 Trigger Deployment
1. Push a commit to your GitHub repo:
```bash
git add .
git commit -m "Configure for Railway deployment"
git push
```

2. Railway will automatically detect the push and deploy

### 11.2 Monitor Deployment
1. Go to Railway dashboard → "Deployments"
2. Watch the build logs
3. Check for any errors

### 11.3 Test Your Application
1. Visit your Railway URL: `https://your-app-name.up.railway.app`
2. Test key functionality:
   - User registration/login
   - Database operations
   - File uploads (if applicable)
   - Queue jobs (if configured)

---

## Troubleshooting

### Build Fails
- Check build logs in Railway dashboard
- Ensure `composer.json` and `package.json` are correct
- Verify Node.js version compatibility

### Database Connection Errors
- Verify MySQL service is running
- Check environment variables are correctly set
- Ensure `DB_HOST` uses `${{MySQL.MYSQLHOST}}` syntax

### 500 Errors
- Check application logs: Railway → Web Service → "Logs"
- Enable `APP_DEBUG=true` temporarily (remember to disable after)
- Verify `APP_KEY` is set
- Check storage permissions: `php artisan storage:link`

### Assets Not Loading
- Ensure `npm run build` completed successfully
- Check `public/build` directory exists
- Verify `VITE_APP_NAME` is set

### Queue Not Processing
- Ensure Queue Worker service is running
- Check queue connection is set to `database`
- Verify migrations ran: `php artisan migrate`

---

## Railway Free Tier Limits

- **$5 credit/month** - Usually enough for small apps
- **512MB RAM** per service
- **1GB storage** for databases
- **100GB bandwidth/month**

Monitor usage in Railway dashboard → "Usage"

---

## Additional Tips

1. **Enable Railway Analytics** - Monitor performance and errors
2. **Set up Health Checks** - Use `/up` endpoint (already configured in Laravel)
3. **Use Railway Volumes** - For persistent file storage
4. **Configure Cron Jobs** - Use Railway's Cron service for scheduled tasks
5. **Set up Monitoring** - Consider Sentry or similar for error tracking

---

## Next Steps

- Set up CI/CD pipelines
- Configure email service (SendGrid, Mailgun, etc.)
- Set up file storage (S3, etc.)
- Configure CDN for assets
- Set up backups for database

---

## Support

- Railway Docs: https://docs.railway.app
- Railway Discord: https://discord.gg/railway
- Laravel Docs: https://laravel.com/docs
