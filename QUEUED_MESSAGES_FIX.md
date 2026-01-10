# Fix: Messages Stuck in "Queued" State

If messages are stuck in "Queued" or "Pending" state in SMS Gateway, it means the API accepted the message, but your Android device hasn't sent it yet. Here's how to fix it:

## Immediate Actions to Take

### 1. Check SMS Gateway App Status ⚠️ MOST IMPORTANT

**On Your Android Device:**
1. Open the SMS Gateway app
2. Check if you're logged in
3. Verify the app shows device as "Online" or "Connected"
4. **Keep the app running** - don't close it or force stop it

**If app is closed:**
- Open it and log in
- Leave it running in the background
- Don't swipe it away from recent apps

### 2. Verify Device is Online

**In SMS Gateway Web Dashboard:**
1. Log into https://app.sms-gateway.app
2. Go to "Devices" section
3. Check if your device (TECNO-BG6 [10834]) shows as:
   - ✅ **"Online"** - Device is connected (good!)
   - ❌ **"Offline"** - Device is not connected (problem!)

**If device is offline:**
- Check internet connection on Android device
- Restart SMS Gateway app
- Restart Android device if needed

### 3. Check SIM Card Has Load

Since you're using `SMS_GATEWAY_USE_ALL_DEVICES=true`, it will try all available SIMs, but if NONE have load, messages will queue.

**Check both SIM cards:**
- **Globe:** Dial `*143#` or text `BAL` to `222`
- **Smart:** Dial `*123#` or text `BAL` to `2200`
- **DITO:** Dial `*123#`

**Load the SIM that has no credit:**
- At least one SIM needs to have load/credit
- Prepaid: Load credit
- Postpaid: Ensure account is active

### 4. Verify SMS Permissions

**On Android Device:**
1. Go to Settings → Apps → SMS Gateway
2. Tap "Permissions"
3. Ensure **"SMS"** permission is enabled
4. Some devices also need **"Phone"** permission

**Test:**
- Try sending a test SMS from SMS Gateway web interface
- If it fails, permissions are likely the issue

### 5. Disable Battery Optimization

**On Android Device:**
1. Go to Settings → Battery → Battery Optimization
2. Find "SMS Gateway" app
3. Select "Don't optimize" or "Not optimized"
4. Some devices: Settings → Apps → SMS Gateway → Battery → Unrestricted

**Why this matters:**
- Android kills background apps to save battery
- This prevents SMS Gateway from sending queued messages

## Configuration Check

Your current `.env` has:
```env
SMS_GATEWAY_USE_ALL_DEVICES=true
```

This means it will use ALL available devices and SIMs. This is good, but you need to ensure:
- At least ONE device is online
- At least ONE SIM has load

## Alternative: Use Specific Device/SIM

If `USE_ALL_DEVICES` isn't working, try specifying the device and SIM that has load:

**In `.env` file, change to:**
```env
SMS_GATEWAY_USE_ALL_DEVICES=false
SMS_GATEWAY_DEVICE_ID=10834
SMS_GATEWAY_SIM_SLOT=0  # or 1, depending on which SIM has load
```

**Then clear config:**
```bash
php artisan config:clear
```

## Step-by-Step Diagnostic

### Step 1: Check App is Running
- [ ] SMS Gateway app is open on Android device
- [ ] App shows "Online" or "Connected" status
- [ ] App is not force-stopped

### Step 2: Check Device Online Status
- [ ] Log into SMS Gateway web dashboard
- [ ] Device shows as "Online" (not "Offline")
- [ ] Device appears in device list

### Step 3: Check Internet Connection
- [ ] Android device has WiFi or mobile data
- [ ] Can browse internet on device
- [ ] SMS Gateway app can connect to server

### Step 4: Check SIM Cards
- [ ] At least one SIM card has load/credit
- [ ] SIM card is properly inserted
- [ ] SIM card is active (not blocked)

### Step 5: Check Permissions
- [ ] SMS permission is granted
- [ ] Phone permission is granted (if required)
- [ ] App is not restricted by battery optimization

### Step 6: Test Manual Send
- [ ] Use SMS Gateway web interface
- [ ] Send test message manually
- [ ] Check if it sends successfully

## Common Issues and Solutions

### Issue: App Keeps Closing
**Solution:**
- Disable battery optimization
- Enable "Keep app running" in app settings
- Don't force close the app

### Issue: Device Shows Offline
**Solution:**
- Check internet connection
- Restart SMS Gateway app
- Restart Android device
- Re-login to SMS Gateway app

### Issue: Messages Queue But Never Send
**Solution:**
- Check SIM has load
- Verify SMS permissions
- Ensure app is running
- Check device is online in dashboard

### Issue: Some Messages Send, Others Don't
**Solution:**
- SIM might be running out of load
- Network connectivity issues
- Try using specific SIM slot instead of all devices

## Testing After Fix

1. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

2. **Send test OTP:**
   - Go to registration page
   - Enter phone number
   - Click "Send OTP"

3. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep "SMS Gateway"
   ```

4. **Check SMS Gateway dashboard:**
   - Go to "Messages" section
   - See if message status changes from "Pending" to "Sent"

## If Still Not Working

1. **Restart Everything:**
   - Restart Android device
   - Restart SMS Gateway app
   - Clear app cache (Settings → Apps → SMS Gateway → Storage → Clear Cache)

2. **Re-register Device:**
   - Log out from SMS Gateway app
   - Log back in
   - Verify device appears in dashboard

3. **Try Specific SIM:**
   - Identify which SIM has load
   - Set `SMS_GATEWAY_SIM_SLOT=0` or `1`
   - Set `SMS_GATEWAY_USE_ALL_DEVICES=false`

4. **Check Logs for Errors:**
   - Look for error codes in Laravel logs
   - Check SMS Gateway dashboard for error messages
   - Look for "errorCode" or "resultCode" in responses

## Quick Checklist

Before sending OTP, ensure:
- ✅ SMS Gateway app is running on Android device
- ✅ Device shows "Online" in web dashboard
- ✅ At least one SIM has load/credit
- ✅ SMS permissions are granted
- ✅ Battery optimization is disabled
- ✅ Device has internet connection

## Monitoring

After fixing, monitor:
- Message status in SMS Gateway dashboard
- Laravel logs for any errors
- Device online status
- SIM balance regularly

If messages still queue, the issue is almost always:
1. App not running (90% of cases)
2. Device offline (5% of cases)
3. No SIM load (4% of cases)
4. Permissions/battery optimization (1% of cases)

