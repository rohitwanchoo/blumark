# SSO Setup Guide for BluMark.pro

This guide explains how to configure Single Sign-On (SSO) providers for BluMark.pro.

## Supported SSO Providers

- Google OAuth
- Apple Sign In
- LinkedIn OAuth
- GitHub OAuth
- Facebook Login

---

## 1. Apple Sign In Setup

### Prerequisites
- Apple Developer Account ($99/year)
- Registered domain with SSL (https://)

### Steps

#### A. Create App ID
1. Go to [Apple Developer Portal](https://developer.apple.com/account)
2. Navigate to **Certificates, Identifiers & Profiles**
3. Click **Identifiers** → **+** button
4. Select **App IDs** → **Continue**
5. Select **App** → **Continue**
6. Configure:
   - Description: `BluMark Web App`
   - Bundle ID: `com.blumark.webapp` (or your domain in reverse)
   - Capabilities: Enable **Sign in with Apple**
7. Click **Continue** → **Register**

#### B. Create Services ID
1. Go to **Identifiers** → **+** button
2. Select **Services IDs** → **Continue**
3. Configure:
   - Description: `BluMark Web Service`
   - Identifier: `com.blumark.webapp.service`
   - Enable **Sign in with Apple**
4. Click **Configure** next to Sign in with Apple
5. Set Primary App ID: Select your App ID from step A
6. Add Website URLs:
   - **Domains**: `blumark.pro`
   - **Return URLs**: `https://blumark.pro/auth/apple/callback`
7. Click **Save** → **Continue** → **Register**

#### C. Create Private Key
1. Go to **Keys** → **+** button
2. Configure:
   - Key Name: `BluMark Apple Sign In Key`
   - Enable **Sign in with Apple**
3. Click **Configure** → Select your Primary App ID
4. Click **Save** → **Continue** → **Register**
5. **Download the .p8 key file** (you can only download once!)
6. Note the **Key ID** shown

#### D. Get Team ID
1. Go to [Membership Page](https://developer.apple.com/account/#!/membership)
2. Copy your **Team ID**

#### E. Configure Environment Variables

Add to your `.env` file:

```bash
APPLE_CLIENT_ID=com.blumark.webapp.service
APPLE_CLIENT_SECRET=<generated-jwt-token>
APPLE_REDIRECT_URI=https://blumark.pro/auth/apple/callback
APPLE_TEAM_ID=<your-team-id>
APPLE_KEY_ID=<your-key-id>
APPLE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----
<your-private-key-contents>
-----END PRIVATE KEY-----"
```

**Note**: For APPLE_CLIENT_SECRET, you need to generate a JWT token. The socialiteproviders/apple package will handle this automatically using the private key.

---

## 2. LinkedIn OAuth Setup

### Prerequisites
- LinkedIn Developer Account (free)
- Registered company page (optional but recommended)

### Steps

#### A. Create LinkedIn App
1. Go to [LinkedIn Developers](https://www.linkedin.com/developers/apps)
2. Click **Create app**
3. Fill in details:
   - **App name**: `BluMark`
   - **LinkedIn Page**: Select your company page (or create one)
   - **App logo**: Upload BluMark logo
   - **Legal agreement**: Accept terms
4. Click **Create app**

#### B. Configure OAuth Settings
1. Go to **Auth** tab
2. Under **OAuth 2.0 settings**:
   - **Authorized redirect URLs**: Add `https://blumark.pro/auth/linkedin/callback`
3. Click **Update**

#### C. Request API Access
1. Go to **Products** tab
2. Request access to:
   - **Sign In with LinkedIn using OpenID Connect** (Recommended)
   - OR **Sign In with LinkedIn** (Legacy)
3. Wait for approval (usually instant for Sign In with LinkedIn)

#### D. Get Credentials
1. Go to **Auth** tab
2. Copy **Client ID**
3. Copy **Client Secret** (click "Show" to reveal)

#### E. Configure Environment Variables

Add to your `.env` file:

```bash
LINKEDIN_CLIENT_ID=<your-client-id>
LINKEDIN_CLIENT_SECRET=<your-client-secret>
LINKEDIN_REDIRECT_URI=https://blumark.pro/auth/linkedin/callback
```

---

## 3. Google OAuth Setup (Already Configured)

Add to `.env`:
```bash
GOOGLE_CLIENT_ID=<your-client-id>
GOOGLE_CLIENT_SECRET=<your-client-secret>
GOOGLE_REDIRECT_URI=https://blumark.pro/auth/google/callback
```

---

## 4. GitHub OAuth Setup (Already Configured)

Add to `.env`:
```bash
GITHUB_CLIENT_ID=<your-client-id>
GITHUB_CLIENT_SECRET=<your-client-secret>
GITHUB_REDIRECT_URI=https://blumark.pro/auth/github/callback
```

---

## 5. Facebook Login Setup (Already Configured)

Add to `.env`:
```bash
FACEBOOK_CLIENT_ID=<your-app-id>
FACEBOOK_CLIENT_SECRET=<your-app-secret>
FACEBOOK_REDIRECT_URI=https://blumark.pro/auth/facebook/callback
```

---

## Testing SSO Locally

For local development, use different redirect URIs:

### Apple
```bash
APPLE_REDIRECT_URI=http://localhost:8000/auth/apple/callback
```
**Note**: Apple requires https:// in production. For local testing, you may need to use ngrok or similar tunneling service.

### LinkedIn
```bash
LINKEDIN_REDIRECT_URI=http://localhost:8000/auth/linkedin/callback
```

### Update Provider Settings
Make sure to add the localhost redirect URIs to your provider's app settings.

---

## Troubleshooting

### Apple Sign In Issues

**Error: "invalid_client"**
- Verify your Services ID matches APPLE_CLIENT_ID
- Ensure the private key is correctly formatted
- Check Team ID and Key ID are correct

**Error: "redirect_uri_mismatch"**
- Verify the redirect URI in Apple Developer Portal matches exactly
- Ensure the domain is verified in Apple Developer Portal

### LinkedIn Issues

**Error: "redirect_uri_mismatch"**
- Add the exact redirect URI to LinkedIn app settings
- Check for trailing slashes (should not have one)

**Error: "unauthorized_scope_error"**
- Request access to "Sign In with LinkedIn" product
- Wait for approval before testing

### General Issues

**Error: "Provider not found"**
- Clear Laravel cache: `php artisan cache:clear`
- Clear config cache: `php artisan config:clear`
- Restart queue workers if using queues

**Social account not linking**
- Check database migration ran: `php artisan migrate`
- Verify `social_accounts` table exists
- Check user email matches between accounts

---

## Security Best Practices

1. **Never commit credentials**: Keep `.env` file out of version control
2. **Use environment variables**: Store all secrets in `.env`
3. **Rotate keys regularly**: Change secrets periodically
4. **Monitor OAuth logs**: Track failed authentication attempts
5. **Validate redirect URIs**: Only whitelist your actual domains
6. **Use HTTPS**: Always use SSL in production
7. **Limit scopes**: Only request necessary user data

---

## Production Checklist

Before going live:

- [ ] All OAuth apps set to production mode
- [ ] Production redirect URIs configured
- [ ] SSL certificate installed (https://)
- [ ] Environment variables set on production server
- [ ] Test each SSO provider works
- [ ] Monitor error logs for SSO failures
- [ ] Set up email notifications for OAuth errors
- [ ] Document which team member has access to each provider account

---

## Support

For issues:
- Apple Developer Support: https://developer.apple.com/support/
- LinkedIn Developer Support: https://www.linkedin.com/help/linkedin/answer/a1348684
- Check Laravel Socialite docs: https://laravel.com/docs/socialite
- SocialiteProviders docs: https://socialiteproviders.com/

---

## Routes

SSO routes are automatically registered:

- Login: `GET /auth/{provider}/redirect`
- Callback: `GET /auth/{provider}/callback`

Where `{provider}` is: `google`, `apple`, `linkedin`, `github`, or `facebook`
