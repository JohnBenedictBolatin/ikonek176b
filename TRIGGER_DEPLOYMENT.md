# How to Trigger Your First Deployment

If you see "no active deployment for this service", here's how to fix it:

---

## Step 1: Make Sure Your Code is on GitHub

Railway deploys from GitHub, so your code must be pushed there first.

### Check if your code is on GitHub:
1. Go to [github.com](https://github.com)
2. Look for your repository
3. If you don't see it, you need to push it

### Push your code to GitHub:

**If you haven't pushed yet:**
```bash
# Open terminal/command prompt in your project folder
cd "c:\Users\John Benedict\Herd\ikonek176b"

# Check if git is initialized
git status

# If not initialized, do this:
git init
git add .
git commit -m "Initial commit"

# Add your GitHub repository (replace with your actual repo URL)
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
git push -u origin main
```

**If you already have a GitHub repo:**
```bash
# Just push any changes
git add .
git commit -m "Add Railway configuration"
git push
```

---

## Step 2: Connect Railway to Your GitHub Repo

### Check if Railway is connected:
1. Go to Railway dashboard
2. Click on your **Web Service**
3. Go to **"Settings"** tab
4. Look for **"Source"** section
5. Check if it shows your GitHub repository

### If not connected:
1. In **Settings** → **Source**
2. Click **"Connect GitHub Repo"** or **"Change Source"**
3. Select your repository
4. Railway will automatically start deploying

---

## Step 3: Check Build Settings

Make sure Railway knows how to build your app:

1. Go to your **Web Service** → **Settings** tab
2. Scroll to **"Build & Deploy"** section
3. Check these settings:

### Build Command:
```
composer install --no-dev --optimize-autoloader && npm ci && npm run build
```

### Start Command:
```
php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php -S 0.0.0.0:$PORT -t public
```

**Note:** If you have `railway.json` in your project root, Railway will use those settings automatically.

---

## Step 4: Trigger Deployment Manually

### Option A: Push to GitHub (Recommended)
Railway watches your GitHub repo. Any push triggers a new deployment:

```bash
git add .
git commit -m "Trigger deployment"
git push
```

Then go to Railway → **Deployments** tab and watch it deploy.

### Option B: Manual Redeploy
1. Go to Railway → Your Web Service
2. Click **"Deployments"** tab
3. Look for **"Redeploy"** button (if available)
4. Or click **"Settings"** → **"Redeploy"**

### Option C: Check Service Status
1. Make sure your service is **enabled**
2. Go to **Settings** → Check if service is paused/stopped
3. If paused, click **"Resume"** or **"Start"**

---

## Step 5: Watch the Deployment

1. Go to **Deployments** tab
2. You should see a new deployment starting
3. Click on it to see build logs
4. Watch for any errors

---

## Common Issues & Fixes

### Issue: "No deployments found"
**Fix:** 
- Make sure your code is pushed to GitHub
- Check that Railway is connected to your repo
- Push a new commit: `git push`

### Issue: Deployment fails immediately
**Fix:**
- Check **Logs** tab for error messages
- Verify build command is correct
- Make sure `composer.json` and `package.json` exist in root

### Issue: Service shows as "Paused" or "Stopped"
**Fix:**
- Go to **Settings** → Look for **"Resume"** or **"Start"** button
- Click it to activate the service

### Issue: "Repository not found"
**Fix:**
- Go to **Settings** → **Source**
- Reconnect your GitHub repository
- Make sure Railway has access to your GitHub account

### Issue: Build keeps failing
**Fix:**
- Check the **Logs** tab for specific errors
- Common issues:
  - Missing environment variables (add them in Variables tab)
  - Wrong build command
  - Missing files in repository

---

## Quick Checklist

Before deployment, make sure:

- [ ] Code is pushed to GitHub
- [ ] Railway service is connected to GitHub repo
- [ ] Build command is set (or `railway.json` exists)
- [ ] Start command is set (or `railway.json` exists)
- [ ] Service is not paused/stopped
- [ ] At least basic environment variables are added (APP_NAME, APP_ENV, etc.)

---

## What Happens During Deployment

1. **Build Phase:**
   - Railway installs PHP dependencies (`composer install`)
   - Installs Node.js dependencies (`npm ci`)
   - Builds your assets (`npm run build`)

2. **Deploy Phase:**
   - Runs database migrations
   - Caches configuration
   - Starts the PHP server

3. **Result:**
   - Your app should be live at your Railway URL
   - Check the **Deployments** tab for status

---

## After Successful Deployment

1. ✅ Check **Deployments** tab - should show "Active" or "Success"
2. ✅ Go to **Settings** → **Domains** to get your app URL
3. ✅ Visit the URL to test your app
4. ✅ Check **Logs** tab if anything doesn't work

---

## Still Not Working?

**Check these:**
1. Is your GitHub repo private? Railway needs access
2. Are you on the free tier? Check usage limits
3. Look at **Logs** tab for specific error messages
4. Try pushing a small change to trigger deployment

**Need help?** Share:
- What you see in the Deployments tab
- Any error messages from Logs
- Whether your code is on GitHub
