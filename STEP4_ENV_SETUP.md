# Step 4: Configure Environment Variables - Detailed Guide

## Overview
This guide will walk you through adding all necessary environment variables to your Railway Web Service.

---

## Part 1: Accessing the Variables Tab

### Step 1.1: Navigate to Your Web Service
1. Log into [Railway Dashboard](https://railway.app)
2. Click on your **project** (the one you created)
3. You'll see two services:
   - **Web Service** (your Laravel app) - Click on this one
   - **MySQL** (your database) - Don't click this one

### Step 1.2: Open Variables Tab
1. Once you're in the Web Service, you'll see tabs at the top:
   - **Deployments**
   - **Variables** ← Click this one
   - **Settings**
   - **Metrics**
   - **Logs**

2. Click on **"Variables"** tab

---

## Part 2: Adding Environment Variables

### Method A: Adding Variables One by One (Recommended for First Time)

For each variable below, follow these steps:
1. Click **"+ New Variable"** button
2. Enter the **Variable Name** (left side)
3. Enter the **Value** (right side)
4. Click **"Add"** or press Enter
5. Repeat for each variable

### Method B: Bulk Import (Faster)

1. Click **"+ New Variable"**
2. Look for **"Raw Editor"** or **"Import"** button (if available)
3. Paste all variables at once (see format below)

---

## Part 3: Required Variables List

Copy and add these variables one by one:

### Application Variables

| Variable Name | Value | Notes |
|--------------|-------|-------|
| `APP_NAME` | `Laravel` | Or your app name |
| `APP_ENV` | `production` | Must be "production" |
| `APP_KEY` | *(Leave empty for now)* | We'll generate this in Step 5 |
| `APP_DEBUG` | `false` | Important: set to false |
| `APP_URL` | `https://your-app-name.up.railway.app` | Replace with your actual Railway URL |

**How to find your Railway URL:**
- Go to your Web Service → **Settings** tab
- Scroll to **"Domains"** section
- Copy the domain shown (e.g., `your-app-name.up.railway.app`)
- Use it as your `APP_URL` value

### Database Variables (Use Railway's Reference Syntax)

| Variable Name | Value | Notes |
|--------------|-------|-------|
| `DB_CONNECTION` | `mysql` | |
| `DB_HOST` | `${{MySQL.MYSQLHOST}}` | **Important:** Use exact syntax with `${{}}` |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` | Railway will replace this automatically |
| `DB_DATABASE` | `${{MySQL.MYSQLDATABASE}}` | Railway will replace this automatically |
| `DB_USERNAME` | `${{MySQL.MYSQLUSER}}` | Railway will replace this automatically |
| `DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` | Railway will replace this automatically |

**Important:** The `${{MySQL.XXX}}` syntax tells Railway to automatically pull values from your MySQL service. Make sure you've created the MySQL service first!

### Session & Cache Variables

| Variable Name | Value | Notes |
|--------------|-------|-------|
| `SESSION_DRIVER` | `database` | |
| `CACHE_STORE` | `database` | |
| `QUEUE_CONNECTION` | `database` | |

### Mail Configuration (Basic Setup)

| Variable Name | Value | Notes |
|--------------|-------|-------|
| `MAIL_MAILER` | `smtp` | |
| `MAIL_HOST` | `smtp.mailtrap.io` | For testing - change later |
| `MAIL_PORT` | `2525` | |
| `MAIL_USERNAME` | `your_username` | Replace with actual credentials |
| `MAIL_PASSWORD` | `your_password` | Replace with actual credentials |
| `MAIL_FROM_ADDRESS` | `noreply@yourdomain.com` | Replace with your domain |
| `MAIL_FROM_NAME` | `${APP_NAME}` | Uses APP_NAME variable |

**Note:** For production, you'll want to use a real email service like:
- SendGrid
- Mailgun
- AWS SES
- Postmark

### Vite Configuration

| Variable Name | Value | Notes |
|--------------|-------|-------|
| `VITE_APP_NAME` | `${APP_NAME}` | Uses APP_NAME variable |

---

## Part 4: Visual Guide - Step by Step

### Adding Your First Variable

1. **Click "+ New Variable"**
   ```
   [Variables Tab]
   ┌─────────────────────────────────────┐
   │  + New Variable                    │ ← Click here
   └─────────────────────────────────────┘
   ```

2. **Enter Variable Details**
   ```
   ┌─────────────────────────────────────┐
   │ Variable Name: [APP_NAME        ]  │
   │ Value:         [Laravel         ]  │
   │                                    │
   │              [Add] [Cancel]        │
   └─────────────────────────────────────┘
   ```

3. **Click "Add"** - Variable appears in the list

4. **Repeat** for all variables

---

## Part 5: Verifying Database Connection Variables

After adding all variables, verify the MySQL references:

1. Go back to your **Project** dashboard (not Web Service)
2. Click on your **MySQL** service
3. Go to **"Variables"** tab
4. You should see variables like:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`

5. These are what your Web Service references with `${{MySQL.XXX}}`

---

## Part 6: Common Mistakes to Avoid

### ❌ Wrong:
```
DB_HOST=localhost
DB_HOST=127.0.0.1
DB_HOST=$MySQL.MYSQLHOST
```

### ✅ Correct:
```
DB_HOST=${{MySQL.MYSQLHOST}}
```

### ❌ Wrong:
```
APP_URL=http://localhost
APP_URL=your-app-name.up.railway.app
```

### ✅ Correct:
```
APP_URL=https://your-app-name.up.railway.app
```
(Note: Must include `https://`)

---

## Part 7: Quick Copy-Paste Template

Here's a template you can use. **Replace the placeholders** before adding:

```env
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://YOUR-APP-NAME.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=${APP_NAME}

VITE_APP_NAME=${APP_NAME}
```

---

## Part 8: After Adding Variables

1. **Save** - Variables are saved automatically in Railway
2. **Redeploy** - Railway will automatically redeploy with new variables
3. **Check Logs** - Go to **Logs** tab to verify no errors
4. **Generate APP_KEY** - Proceed to Step 5 to generate the application key

---

## Troubleshooting

### Variables Not Showing Up?
- Make sure you're in the **Web Service**, not the MySQL service
- Refresh the page
- Check that you clicked "Add" after entering each variable

### Database Connection Errors?
- Verify MySQL service is running (green status)
- Double-check the `${{MySQL.XXX}}` syntax is exact
- Make sure there are no extra spaces

### Can't Find Railway URL?
- Go to Web Service → **Settings** → **Domains**
- If no domain shown, click **"Generate Domain"**

### Variables Not Applied?
- Railway auto-redeploys when variables change
- Check **Deployments** tab for new deployment
- View **Logs** to see if variables are being read

---

## Next Step

After completing Step 4, proceed to **Step 5: Generate Application Key**

You'll need to:
1. Install Railway CLI (if not already installed)
2. Run: `railway run php artisan key:generate --show`
3. Copy the generated key
4. Add it as `APP_KEY` in Railway Variables
