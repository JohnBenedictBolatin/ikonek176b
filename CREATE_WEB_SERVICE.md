# Creating Your Web Service in Railway

If you don't see a "Web Service" in your Railway dashboard, you need to create it first. Here's how:

---

## Option 1: Deploy from GitHub (Recommended)

### Step 1: Make Sure Your Code is on GitHub
1. Your Laravel project should be pushed to a GitHub repository
2. If not, push it:
   ```bash
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
   git push -u origin main
   ```

### Step 2: Create New Project in Railway
1. Go to [railway.app](https://railway.app)
2. Click **"New Project"** (top right or in dashboard)
3. Select **"Deploy from GitHub repo"**
4. If prompted, authorize Railway to access your GitHub
5. Select your repository from the list
6. Click **"Deploy Now"**

### Step 3: Railway Auto-Detection
- Railway should automatically detect it's a Laravel/PHP app
- It will create a service automatically
- You should see a service appear (might be named after your repo or "Web Service")

---

## Option 2: Manual Service Creation

If Railway didn't auto-create a service, or you want to create it manually:

### Step 1: Create Empty Service
1. In your Railway project dashboard
2. Click **"+ New"** button (usually top right or in the middle)
3. Select **"Empty Service"** or **"GitHub Repo"**

### Step 2: Connect Your Repository
1. If you selected "GitHub Repo":
   - Select your repository
   - Railway will auto-detect the framework
   
2. If you selected "Empty Service":
   - Click on the service
   - Go to **"Settings"** tab
   - Under **"Source"**, click **"Connect GitHub Repo"**
   - Select your repository

### Step 3: Configure the Service
1. Railway should auto-detect Laravel
2. If not, go to **"Settings"** → **"Build & Deploy"**
3. Make sure these are set:
   - **Build Command:** `composer install --no-dev --optimize-autoloader && npm ci && npm run build`
   - **Start Command:** `php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php -S 0.0.0.0:$PORT -t public`

---

## What You Should See

After creating the service, your Railway dashboard should show:

```
┌─────────────────────────────────────┐
│  Your Project Name                 │
├─────────────────────────────────────┤
│                                     │
│  ┌──────────────┐  ┌─────────────┐ │
│  │ Web Service  │  │   MySQL     │ │
│  │  (or repo    │  │  Database   │ │
│  │    name)     │  │             │ │
│  └──────────────┘  └─────────────┘ │
│                                     │
└─────────────────────────────────────┘
```

---

## Troubleshooting

### I don't see "+ New" button
- Make sure you're in a **Project**, not just the Railway dashboard
- Create a new project first: Click "New Project" → "Empty Project"

### Railway didn't detect Laravel
- Go to your service → **Settings** → **Build & Deploy**
- Set the build command manually (see Step 3 above)
- Make sure `composer.json` and `package.json` are in the root

### Service is deploying but failing
- Check the **Logs** tab for errors
- Make sure your `railway.json` file is in the root directory
- Verify your repository has all necessary files

### I see a service but it's not named "Web Service"
- That's fine! Railway might name it after your repository
- Click on that service - it's your web service
- You can rename it in Settings if you want

---

## Next Steps

Once you see your Web Service:
1. ✅ Click on it
2. ✅ Go to **"Variables"** tab
3. ✅ Proceed with Step 4 (adding environment variables)

---

## Quick Checklist

- [ ] Code is pushed to GitHub
- [ ] Created Railway project
- [ ] Connected GitHub repository
- [ ] Service is created and visible
- [ ] Service is deploying (check Deployments tab)
- [ ] Ready to add environment variables

---

## Still Having Issues?

1. **Check Railway Status:** Make sure Railway is operational
2. **Check Repository:** Verify your GitHub repo is accessible
3. **Check Files:** Ensure `composer.json` exists in root
4. **View Logs:** Check the Deployments tab for error messages

If you're still stuck, share:
- What you see in your Railway dashboard
- Any error messages from the Logs tab
- Whether your code is on GitHub
