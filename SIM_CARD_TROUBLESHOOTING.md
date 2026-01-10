# SIM Card Troubleshooting for SMS Gateway

If messages are stuck in "Queued" or "Pending" status, the issue might be with the SIM card - either it doesn't have load (credit) or the wrong SIM is being used.

## How to Check Which SIM Has Load

### Method 1: Check in SMS Gateway App
1. Open SMS Gateway app on your Android device
2. Go to Settings or Device Settings
3. Look for SIM card information
4. Check which SIM slots are available
5. Verify SIM card status (should show as "Active" or "Ready")

### Method 2: Check in Android Settings
1. Go to Android Settings → SIM cards & mobile networks
2. Check both SIM slots (if dual SIM)
3. Look for:
   - SIM 1 status
   - SIM 2 status
   - Signal strength
   - Network name (Globe, Smart, etc.)

### Method 3: Test Manual SMS
1. Open regular SMS/Message app on device
2. Try sending a test SMS from SIM 1
3. Try sending a test SMS from SIM 2
4. See which one works (has load/credit)

### Method 4: Check Balance
1. Dial balance check code for your carrier:
   - **Globe:** *143# or text BAL to 222
   - **Smart:** *123# or text BAL to 2200
   - **DITO:** *123# or check via app
2. Check balance for both SIM cards
3. Ensure at least one has sufficient credit

## How to Select the Correct SIM in SMS Gateway

### Option 1: Use Environment Variable (Recommended)

Add to your `.env` file:

**For First SIM (SIM Slot 0):**
```env
SMS_GATEWAY_DEVICE_ID=10834
SMS_GATEWAY_SIM_SLOT=0
```

**For Second SIM (SIM Slot 1):**
```env
SMS_GATEWAY_DEVICE_ID=10834
SMS_GATEWAY_SIM_SLOT=1
```

**To use default SIM (let SMS Gateway choose):**
```env
SMS_GATEWAY_DEVICE_ID=10834
# Don't set SMS_GATEWAY_SIM_SLOT, or set it to null
```

Then clear config cache:
```bash
php artisan config:clear
```

### Option 2: Use All Devices (Auto-select SIM)

If you have multiple devices or SIMs, you can let SMS Gateway automatically choose:

```env
SMS_GATEWAY_USE_ALL_DEVICES=true
```

This will use all available devices and their SIMs, automatically selecting the best one.

### Option 3: Check in Web Dashboard

1. Log into SMS Gateway web dashboard
2. Go to "Devices" section
3. Click on your device (TECNO-BG6 [10834])
4. Check SIM card information:
   - Which SIM slots are active
   - Which SIM is set as default
   - SIM card numbers/identifiers

## Common SIM Card Issues

### Issue 1: No Load/Credit
**Symptoms:**
- Messages stuck in "Pending"
- Manual SMS also fails
- Balance check shows zero or insufficient credit

**Solution:**
- Load credit to the SIM card
- Use prepaid load or postpaid account
- Ensure account is active and not suspended

### Issue 2: Wrong SIM Selected
**Symptoms:**
- Messages queued but not sent
- One SIM has load, but wrong one is being used

**Solution:**
- Identify which SIM has load
- Set `SMS_GATEWAY_SIM_SLOT` to the correct slot (0 or 1)
- Clear config cache and test again

### Issue 3: SIM Not Inserted Properly
**Symptoms:**
- Device shows "No SIM" or "SIM error"
- SMS Gateway can't detect SIM

**Solution:**
- Remove and reinsert SIM card
- Ensure SIM is properly seated
- Check if SIM card is damaged
- Try SIM in different slot if dual SIM

### Issue 4: SIM Card Blocked/Deactivated
**Symptoms:**
- SIM shows but can't send SMS
- Network shows but no service

**Solution:**
- Contact your carrier to check SIM status
- Reactivate SIM if needed
- Replace SIM if damaged or expired

### Issue 5: Network Issues
**Symptoms:**
- SIM has load but no signal
- Can't connect to network

**Solution:**
- Check signal strength
- Move to area with better coverage
- Restart device
- Check if carrier has service issues

## Step-by-Step: Finding the Right SIM

### Step 1: Identify Your SIMs
1. Check which SIM slots are populated
2. Note the phone numbers for each SIM
3. Check which carrier each SIM uses (Globe, Smart, DITO)

### Step 2: Check Load/Balance
1. Check balance for SIM 1
2. Check balance for SIM 2
3. Identify which SIM has sufficient credit

### Step 3: Test Manual Sending
1. Open SMS app
2. Send test SMS using SIM 1
3. Send test SMS using SIM 2
4. See which one successfully sends

### Step 4: Configure SMS Gateway
1. Use the SIM that has load and works
2. Set `SMS_GATEWAY_SIM_SLOT` accordingly:
   - `0` for first SIM slot
   - `1` for second SIM slot
3. Clear config: `php artisan config:clear`
4. Test OTP sending

## Testing Your Configuration

### Test 1: Check Current SIM Selection
Look at your Laravel logs when sending OTP:
```bash
tail -f storage/logs/laravel.log | grep "SMS Gateway"
```

Check the `devices` parameter in the request:
- `10834` = Device only (uses default SIM)
- `10834|0` = Device 10834, SIM slot 0 (first SIM)
- `10834|1` = Device 10834, SIM slot 1 (second SIM)

### Test 2: Send Test Message
1. Use SMS Gateway web interface
2. Select specific device and SIM slot
3. Send test message
4. See if it sends successfully

### Test 3: Check Message Status
1. In SMS Gateway dashboard, check message status
2. If status is "Sent", the SIM is working
3. If status is "Pending" or "Failed", check SIM/load

## Quick Fix: Try Both SIMs

If you're not sure which SIM has load, try both:

**Try SIM Slot 0 (First SIM):**
```env
SMS_GATEWAY_DEVICE_ID=10834
SMS_GATEWAY_SIM_SLOT=0
```
```bash
php artisan config:clear
```
Test sending OTP.

**Try SIM Slot 1 (Second SIM):**
```env
SMS_GATEWAY_DEVICE_ID=10834
SMS_GATEWAY_SIM_SLOT=1
```
```bash
php artisan config:clear
```
Test sending OTP.

## Best Practices

1. **Always Load Both SIMs:**
   - Keep both SIM cards loaded if using dual SIM
   - This provides backup if one SIM runs out

2. **Monitor SIM Balance:**
   - Regularly check SIM balance
   - Set up low balance alerts
   - Auto-reload if possible

3. **Use SIM with Better Signal:**
   - Choose SIM with stronger signal
   - Better signal = faster message delivery

4. **Document Your Setup:**
   - Note which SIM slot has which number
   - Keep track of which SIM has load
   - Update configuration when SIM changes

## Environment Variables Reference

```env
# Required
SMS_GATEWAY_API_KEY=your_api_key_here

# Device Selection (choose one approach)
# Option A: Specific device and SIM
SMS_GATEWAY_DEVICE_ID=10834
SMS_GATEWAY_SIM_SLOT=0  # 0 = first SIM, 1 = second SIM

# Option B: Use all devices (auto-selects best SIM)
SMS_GATEWAY_USE_ALL_DEVICES=true

# Option C: Primary device, default SIM
SMS_GATEWAY_DEVICE_ID=0
# Don't set SMS_GATEWAY_SIM_SLOT
```

## Still Not Working?

1. **Verify SIM in Device:**
   - Physically check SIM card is inserted
   - Try removing and reinserting
   - Check for SIM card damage

2. **Check Carrier Status:**
   - Contact carrier support
   - Verify account is active
   - Check for service outages

3. **Try Different SIM:**
   - Test with different SIM card
   - Use known working SIM
   - Verify SMS Gateway works with that SIM

4. **Check SMS Gateway App:**
   - Ensure app has SMS permissions
   - Check app can access SIM cards
   - Verify app version is up to date

