# Simple Guide: Adding Environment Variables to Railway

## What Are Environment Variables?

Think of environment variables as **settings** that your app needs to work. Like:
- Your app's name
- Where your database is
- Your app's website URL
- Email settings

You need to **type these into Railway** so your app knows what to use.

---

## Step-by-Step: How to Add Them

### Step 1: Find the Variables Tab
1. Go to Railway dashboard
2. Click on your **Web Service** (the service that runs your Laravel app)
3. Click the **"Variables"** tab at the top

### Step 2: Add Each Variable One by One

For each line below, do this:
1. Click **"+ New Variable"** button
2. Type the **Variable Name** (left side)
3. Type the **Value** (right side)  
4. Click **"Add"**

---

## The Variables You Need to Add

### 1. Basic App Settings (Add These First)

Click "+ New Variable" and add these one by one:

```
Variable Name: APP_NAME
Value: Laravel
```

```
Variable Name: APP_ENV
Value: production
```

```
Variable Name: APP_DEBUG
Value: false
```

```
Variable Name: APP_URL
Value: https://your-app-name.up.railway.app
```
*(Replace "your-app-name" with your actual Railway URL - find it in Settings → Domains)*

```
Variable Name: APP_KEY
Value: (leave empty for now - we'll add this later)
```

---

### 2. Database Settings (Very Important!)

These tell your app how to connect to the database. **Type them EXACTLY as shown:**

```
Variable Name: DB_CONNECTION
Value: mysql
```

```
Variable Name: DB_HOST
Value: ${{MySQL.MYSQLHOST}}
```
*(Copy this EXACTLY - including the ${{ }} symbols)*

```
Variable Name: DB_PORT
Value: ${{MySQL.MYSQLPORT}}
```

```
Variable Name: DB_DATABASE
Value: ${{MySQL.MYSQLDATABASE}}
```

```
Variable Name: DB_USERNAME
Value: ${{MySQL.MYSQLUSER}}
```

```
Variable Name: DB_PASSWORD
Value: ${{MySQL.MYSQLPASSWORD}}
```

**Why the ${{MySQL.XXX}}?** 
- Railway automatically fills these in with your database info
- You don't need to know the actual values
- Railway does it for you!

---

### 3. Other Settings

```
Variable Name: SESSION_DRIVER
Value: database
```

```
Variable Name: CACHE_STORE
Value: database
```

```
Variable Name: QUEUE_CONNECTION
Value: database
```

```
Variable Name: VITE_APP_NAME
Value: ${APP_NAME}
```

---

### 4. Email Settings (Optional - Add Later if Needed)

```
Variable Name: MAIL_MAILER
Value: smtp
```

```
Variable Name: MAIL_HOST
Value: smtp.mailtrap.io
```

```
Variable Name: MAIL_PORT
Value: 2525
```

```
Variable Name: MAIL_USERNAME
Value: your_username
```
*(Replace with real email service username later)*

```
Variable Name: MAIL_PASSWORD
Value: your_password
```
*(Replace with real email service password later)*

```
Variable Name: MAIL_FROM_ADDRESS
Value: noreply@yourdomain.com
```

```
Variable Name: MAIL_FROM_NAME
Value: ${APP_NAME}
```

---

## Visual Example

Here's what it looks like when adding a variable:

```
┌─────────────────────────────────────────┐
│ Variables Tab                           │
├─────────────────────────────────────────┤
│                                         │
│  [+ New Variable]  ← Click this        │
│                                         │
│  After clicking, you'll see:            │
│                                         │
│  ┌───────────────────────────────────┐ │
│  │ Variable Name: [APP_NAME       ] │ │
│  │ Value:         [Laravel        ] │ │
│  │                                  │ │
│  │           [Add] [Cancel]         │ │
│  └───────────────────────────────────┘ │
│                                         │
│  After adding, it appears in the list: │
│                                         │
│  APP_NAME = Laravel                     │
│  APP_ENV = production                   │
│  ...                                    │
└─────────────────────────────────────────┘
```

---

## Quick Checklist

Add these variables in order:

- [ ] APP_NAME
- [ ] APP_ENV  
- [ ] APP_DEBUG
- [ ] APP_URL (find your URL first!)
- [ ] DB_CONNECTION
- [ ] DB_HOST (use ${{MySQL.MYSQLHOST}})
- [ ] DB_PORT (use ${{MySQL.MYSQLPORT}})
- [ ] DB_DATABASE (use ${{MySQL.MYSQLDATABASE}})
- [ ] DB_USERNAME (use ${{MySQL.MYSQLUSER}})
- [ ] DB_PASSWORD (use ${{MySQL.MYSQLPASSWORD}})
- [ ] SESSION_DRIVER
- [ ] CACHE_STORE
- [ ] QUEUE_CONNECTION
- [ ] VITE_APP_NAME

---

## Common Questions

**Q: Do I need to add all of them?**
A: Yes, at least the first 13 in the checklist above. Email ones can wait.

**Q: What if I make a typo?**
A: You can click on any variable to edit it, or delete and re-add it.

**Q: Do I need to know what these mean?**
A: Not really! Just copy them exactly as shown. They're standard Laravel settings.

**Q: What about APP_KEY?**
A: Leave it empty for now. We'll generate it in the next step.

**Q: Where do I find my Railway URL?**
A: Go to your Web Service → Settings tab → Scroll to "Domains" section → Copy the URL shown there.

---

## After Adding Variables

1. ✅ All variables are saved automatically
2. ✅ Railway will redeploy your app
3. ✅ Check the "Deployments" tab to see it deploying
4. ✅ Next step: Generate APP_KEY (we'll do this after)

---

## Still Confused?

**Think of it like this:**
- Your app is like a house
- Environment variables are like the address, phone number, and other info the house needs
- Railway is where you write down all this info
- The "Variables" tab is like a form where you fill in the info

Just follow the steps above, one variable at a time!
