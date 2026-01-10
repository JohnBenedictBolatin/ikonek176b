# SMS Gateway Setup Guide

This guide will walk you through setting up SMS Gateway.app for OTP messaging in your iKonek176B application.

## Step 1: Create an Account on SMS Gateway.app

1. Visit [https://app.sms-gateway.app/index.php](https://app.sms-gateway.app/index.php)
2. Click on "Register" or "Sign up" to create a new account
3. Fill in the required information and complete the registration process
4. Verify your email address if required

## Step 2: Download and Install the SMS Gateway App

1. Download the SMS Gateway APK v7.0 from the website or use the provided download link
2. Install the app on your Android device
3. Open the app and log in using your account credentials
4. Grant the app necessary permissions:
   - SMS permissions
   - Phone permissions
   - Internet access

## Step 3: Get Your API Key

1. Log in to your SMS Gateway account on the web dashboard at [https://app.sms-gateway.app/api.php](https://app.sms-gateway.app/api.php)
2. Navigate to your account settings or API section
3. Copy your unique API key
4. **Important**: Keep this API key secure and do not share it publicly

## Step 4: Configure Your Laravel Application

### 4.1 Update Your .env File

Add the following environment variables to your `.env` file:

```env
# SMS Gateway Configuration
SMS_GATEWAY_API_KEY=your_api_key_here
SMS_GATEWAY_API_URL=https://app.sms-gateway.app/services/send.php
```

**Replace `your_api_key_here` with your actual API key from Step 3.**

### 4.2 Verify Configuration

The configuration has already been updated in:
- `config/services.php` - Added SMS Gateway service configuration
- `app/Services/OtpService.php` - Updated to use SMS Gateway API

## Step 5: Test the Integration

1. Make sure your Android device with the SMS Gateway app is:
   - Connected to the internet
   - Has the SMS Gateway app running and logged in
   - Has sufficient SMS balance (if applicable)

2. Test the OTP functionality:
   - Navigate to the registration page
   - Enter a valid phone number (10 or 11 digits)
   - Click "Send OTP"
   - Check your phone for the OTP message
   - Verify the OTP code

## Step 6: Monitor and Debug

### Check Logs

If you encounter any issues, check the Laravel logs:

```bash
tail -f storage/logs/laravel.log
```

The logs will show:
- API requests being sent
- Response from SMS Gateway
- Any errors or issues

### Common Issues and Solutions

1. **"SMS service is not configured"**
   - Make sure `SMS_GATEWAY_API_KEY` is set in your `.env` file
   - Run `php artisan config:clear` to clear cached config

2. **"SMS service authentication failed"**
   - Verify your API key is correct
   - Check if your SMS Gateway account is active

3. **"Failed to send OTP"**
   - Ensure the SMS Gateway app is running on your Android device
   - Check if your device has internet connection
   - Verify the phone number format is correct (should be 10 or 11 digits)

4. **OTP not received**
   - Check if the SMS Gateway app is logged in and active on your Android device
   - Verify the phone number format (should be in international format with +: +63XXXXXXXXXX)
   - Check device SMS permissions
   - Ensure the Android device is connected to the internet
   - Verify the device has the SMS Gateway app running in the background

## API Details

### Endpoint
```
https://app.sms-gateway.app/services/send.php
```

### Request Method
**POST** (form-encoded data)

### Request Parameters (for single message/OTP)
- `key`: Your API key
- `number`: Recipient's phone number in international format with + prefix (e.g., +639123456789)
- `message`: The OTP message content
- `devices`: Device ID (use `0` for primary device, or specific device ID)
- `type`: Set to `"sms"`
- `prioritize`: Set to `1` to prioritize the message (important for OTP)

### Response Format
```json
{
  "success": true,
  "data": {
    "messages": [
      {
        "ID": "message_id_here",
        "number": "+639123456789",
        "message": "Your OTP message...",
        "deviceID": "1",
        "simSlot": "0",
        "status": "Pending",
        "type": "sms"
      }
    ]
  }
}
```

Or for single message:
```json
{
  "success": true,
  "data": {
    "message": {
      "ID": "message_id_here",
      "status": "Pending"
    }
  }
}
```

## Security Notes

1. **Never commit your `.env` file** to version control
2. Keep your API key secure and rotate it if compromised
3. The OTP codes are stored in cache for 5 minutes only
4. Rate limiting is set to 5 requests per hour per phone number

## Next Steps

After successful setup:
1. Test the complete registration flow
2. Monitor the first few OTP sends to ensure everything works
3. Consider removing the OTP code from the response in production (currently shown for testing)

## Support

If you encounter issues:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify your SMS Gateway account status
3. Ensure the Android app is running and connected
4. Review the SMS Gateway documentation at [https://app.sms-gateway.app/api.php](https://app.sms-gateway.app/api.php)

