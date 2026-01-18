<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; margin: 0; padding: 0; background-color: #f3f4f6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f3f4f6;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0ea5e9, #0369a1); padding: 30px; border-radius: 16px 16px 0 0; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;">
                                Verify Your Email
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background-color: #ffffff; padding: 40px 30px; border-radius: 0 0 16px 16px;">
                            <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0;">
                                Hi {{ $userName }},
                            </p>

                            <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0;">
                                Thanks for signing up for {{ config('app.name') }}! Please verify your email address to complete your registration and access all features.
                            </p>

                            <!-- Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0;">
                                <tr>
                                    <td style="background-color: #eff6ff; padding: 16px; border-radius: 12px; border: 1px solid #bfdbfe;">
                                        <p style="font-size: 14px; color: #1e40af; margin: 0;">
                                            <strong>Email:</strong> {{ $userEmail }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center; padding: 20px 0;">
                                        <a href="{{ $verificationUrl }}"
                                           style="display: inline-block; padding: 14px 32px; background-color: #0ea5e9; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px;">
                                            Verify Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 14px; color: #6b7280; margin: 20px 0 0 0;">
                                This verification link will expire in 60 minutes. If you did not create an account, no further action is required.
                            </p>

                            <!-- Link fallback -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 30px;">
                                <tr>
                                    <td style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
                                        <p style="font-size: 12px; color: #9ca3af; margin: 0 0 8px 0;">
                                            If the button above doesn't work, copy and paste this link into your browser:
                                        </p>
                                        <p style="font-size: 12px; color: #0ea5e9; margin: 0; word-break: break-all;">
                                            {{ $verificationUrl }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px; text-align: center;">
                            <p style="font-size: 13px; color: #9ca3af; margin: 0;">
                                Sent via <a href="{{ config('app.url') }}" style="color: #0ea5e9; text-decoration: none;">{{ config('app.name') }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
