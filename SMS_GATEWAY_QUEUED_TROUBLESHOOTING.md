# Why SMS Gateway Messages Get Stuck in "Queued" or "Pending" Status

When messages show "Pending" or "Queued" status, it means the SMS Gateway API has accepted your message, but the Android device hasn't sent it yet. Here are the most common reasons and solutions:

## Common Reasons Messages Get Stuck

### 1. **SMS Gateway App Not Running** ⚠️ MOST COMMON
**Problem:** The SMS Gateway app on your Android device is closed or force-stopped.

**Solution:**
- Open the SMS Gateway app on your Android device
- Keep it running in the background (don't force close it)
- Enable "Keep app running" or "Background activity" in Android settings
- Disable battery optimization for the SMS Gateway app

**How to Check:**
- Open the SMS Gateway app
- If you see a login screen, the app was closed
- The app should show your device status as "Online" or "Connected"

---

### 2. **Device Not Connected to Internet** 🌐
**Problem:** Your Android device doesn't have an active internet connection.

**Solution:**
- Ensure WiFi or mobile data is enabled
- Check if you can browse the internet on the device
- Try disconnecting and reconnecting to WiFi
- Restart mobile data if using cellular connection

**How to Check:**
- Open a browser on the device and try to load a website
- Check the SMS Gateway app - it should show connection status

---

### 3. **SMS Permissions Not Granted** 📱
**Problem:** The SMS Gateway app doesn't have permission to send SMS.

**Solution:**
- Go to Android Settings → Apps → SMS Gateway
- Tap "Permissions"
- Enable "SMS" permission
- Enable "Phone" permission (if required)
- Some devices require "Default SMS app" permission

**How to Check:**
- In SMS Gateway app, check if there's a permission warning
- Try sending a test message from the app directly

---

### 4. **Device Not Registered/Active** 🔌
**Problem:** The device isn't properly registered with your SMS Gateway account.

**Solution:**
- Log out and log back into the SMS Gateway app
- Verify your device appears in the web dashboard
- Check that the device shows as "Online" in the dashboard
- Re-register the device if necessary

**How to Check:**
- Log into SMS Gateway web dashboard
- Go to "Devices" section
- Verify your device (TECNO-BG6 [10834]) is listed and shows "Online"

---

### 5. **Battery Optimization Killing the App** 🔋
**Problem:** Android's battery optimization is closing the app to save battery.

**Solution:**
- Go to Android Settings → Battery → Battery Optimization
- Find "SMS Gateway" app
- Select "Don't optimize" or "Not optimized"
- Some devices: Settings → Apps → SMS Gateway → Battery → Unrestricted

**How to Check:**
- Check if the app closes after a few minutes of inactivity
- Look for battery optimization warnings in the app

---

### 6. **SIM Card Issues** 📞
**Problem:** The SIM card in the device has issues or no credit.

**Solution:**
- Ensure SIM card is properly inserted
- Check if SIM has credit/balance (for prepaid)
- Verify SIM card is active and not blocked
- Try removing and reinserting the SIM card
- Test if you can send SMS manually from the device

**How to Check:**
- Try sending a regular SMS from the device's messaging app
- Check SIM card status in Android Settings

---

### 7. **App Version Outdated** 📲
**Problem:** You're using an old version of the SMS Gateway app.

**Solution:**
- Update the SMS Gateway app from Google Play Store or APK
- Check for app updates in the SMS Gateway app settings
- Reinstall the app if updates don't work

**How to Check:**
- Check app version in SMS Gateway app settings
- Compare with latest version on SMS Gateway website

---

### 8. **Too Many Queued Messages** 📬
**Problem:** There are too many messages in the queue, causing delays.

**Solution:**
- Wait for the queue to process
- Clear old queued messages from the web dashboard
- Reduce the number of simultaneous requests
- Check message queue in the SMS Gateway dashboard

**How to Check:**
- Log into SMS Gateway web dashboard
- Check "Messages" section for queued messages
- Look for message count and processing status

---

### 9. **Device Sleep/Doze Mode** 😴
**Problem:** Android's Doze mode is preventing the app from working.

**Solution:**
- Disable Doze mode for SMS Gateway app
- Keep device screen on (for testing)
- Connect device to charger (prevents aggressive doze)
- Settings → Apps → SMS Gateway → Battery → Unrestricted

**How to Check:**
- Notice if messages only send when device is active
- Check if app works better when device is charging

---

### 10. **Network Firewall/Blocking** 🚫
**Problem:** Network firewall or carrier is blocking SMS Gateway connections.

**Solution:**
- Try different network (switch WiFi or use mobile data)
- Check if your network/carrier blocks certain connections
- Contact network administrator if on corporate network
- Use mobile data instead of WiFi (or vice versa)

**How to Check:**
- Try sending from different network
- Check if other apps can access internet on same network

---

## Quick Diagnostic Steps

### Step 1: Check App Status
1. Open SMS Gateway app on Android device
2. Verify you're logged in
3. Check if device shows as "Online" or "Connected"
4. Look for any error messages or warnings

### Step 2: Test Manual Send
1. Use SMS Gateway web interface
2. Send a test message manually
3. If manual send works but API doesn't, check API configuration
4. If manual send also fails, the issue is with the device/app

### Step 3: Check Web Dashboard
1. Log into SMS Gateway web dashboard
2. Go to "Messages" section
3. Check message status and error codes
4. Look for device status (should be "Online")

### Step 4: Check Device Logs
1. In SMS Gateway app, check for error logs
2. Look for connection issues or permission errors
3. Check Android system logs if possible

---

## Status Meanings

- **Pending/Queued:** Message accepted by API, waiting for device to send
- **Sent:** Message successfully sent from device
- **Delivered:** Message delivered to recipient (if delivery reports enabled)
- **Failed:** Message failed to send (check error code)
- **Error [124]:** Device offline or not responding

---

## Best Practices to Prevent Queued Messages

1. **Keep App Running:**
   - Don't force close the SMS Gateway app
   - Disable battery optimization
   - Keep device connected to power when possible

2. **Monitor Device Status:**
   - Regularly check if device is online in dashboard
   - Set up alerts for device disconnections

3. **Maintain Internet Connection:**
   - Use stable WiFi or mobile data
   - Avoid networks with firewalls

4. **Keep App Updated:**
   - Regularly update SMS Gateway app
   - Check for compatibility with Android updates

5. **Test Regularly:**
   - Send test messages periodically
   - Monitor message delivery rates

---

## If Nothing Works

1. **Restart Everything:**
   - Restart Android device
   - Restart SMS Gateway app
   - Clear app cache (Settings → Apps → SMS Gateway → Storage → Clear Cache)

2. **Re-register Device:**
   - Log out from SMS Gateway app
   - Log back in
   - Verify device appears in dashboard

3. **Reinstall App:**
   - Uninstall SMS Gateway app
   - Reinstall from official source
   - Log in and re-register device

4. **Contact Support:**
   - Check SMS Gateway documentation
   - Contact SMS Gateway support with:
     - Device model and Android version
     - App version
     - Error messages from logs
     - Screenshots of issues

---

## Monitoring Your Setup

Add these to your `.env` for better monitoring:
```env
SMS_GATEWAY_API_KEY=your_key
SMS_GATEWAY_USE_ALL_DEVICES=true
```

Check Laravel logs regularly:
```bash
tail -f storage/logs/laravel.log | grep "SMS Gateway"
```

Look for:
- Message status changes
- Error codes
- Device connectivity issues
- API response details

