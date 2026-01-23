# SMTP Provider Setup Guide

## Overview
The system now supports automated SMTP configuration through popular email providers:
- **Gmail / Google Workspace** (OAuth 2.0)
- **SendGrid** (API Key)
- **Mailgun** (API Key)
- **Custom SMTP** (Manual configuration)

## Gmail / Google Workspace Setup

### 1. Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Gmail API for your project

### 2. Create OAuth 2.0 Credentials
1. Navigate to **APIs & Services > Credentials**
2. Click **Create Credentials > OAuth client ID**
3. Select **Web application**
4. Add authorized redirect URI:
   ```
   https://yourdomain.com/smtp-settings/provider/gmail/callback
   ```
5. Copy the Client ID and Client Secret

### 3. Configure Environment Variables
Add to your `.env` file:
```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
```

### 4. Install Google Client Library
```bash
composer require google/apiclient:"^2.0"
```

### 5. Configure OAuth Consent Screen
1. Go to **APIs & Services > OAuth consent screen**
2. Add required scopes:
   - `https://www.googleapis.com/auth/gmail.send`
   - `https://www.googleapis.com/auth/userinfo.email`
3. Add test users if in development mode

## SendGrid Setup

### 1. Get API Key
1. Log in to [SendGrid](https://sendgrid.com/)
2. Navigate to **Settings > API Keys**
3. Create a new API key with **Full Access** or **Mail Send** permissions

### 2. Configure in Application
1. Go to **SMTP Settings > Connect Provider**
2. Select **SendGrid**
3. Enter your API key (starts with `SG.`)
4. Enter your from email and name

## Mailgun Setup

### 1. Get SMTP Credentials
1. Log in to [Mailgun](https://www.mailgun.com/)
2. Go to **Sending > Domain Settings**
3. Find your SMTP credentials under **SMTP**

### 2. Configure in Application
1. Go to **SMTP Settings > Connect Provider**
2. Select **Mailgun**
3. Enter SMTP username (usually `postmaster@your-domain.mailgun.org`)
4. Enter SMTP password
5. Select region (US or EU)
6. Enter your from email and name

## Environment Variables Reference

Add these to your `.env` file as needed:

```env
# Gmail OAuth (Required for Gmail provider)
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret

# Optional: Configure callback URL in config/services.php
```

## Troubleshooting

### Gmail OAuth Issues
- **Error: redirect_uri_mismatch**: Ensure the callback URL in Google Cloud Console exactly matches your application's callback URL
- **Token expired**: The system automatically refreshes tokens, but you may need to reconnect if the refresh token is invalid

### SendGrid Issues
- **Invalid API key**: Ensure the API key starts with `SG.` and has proper permissions
- **Sender not verified**: Verify your sender email in SendGrid dashboard

### Mailgun Issues
- **Authentication failed**: Check your SMTP credentials in Mailgun dashboard
- **Wrong region**: Ensure you selected the correct region (US or EU) based on your Mailgun account

## Security Notes

- All sensitive data (passwords, API keys, OAuth tokens) are encrypted in the database
- OAuth tokens are automatically refreshed before expiration
- Only one SMTP configuration can be active at a time per user
