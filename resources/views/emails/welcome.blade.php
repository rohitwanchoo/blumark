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
                                Welcome to {{ config('app.name') }}!
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
                                Thank you for signing up! We're excited to have you on board.
                            </p>

                            <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0;">
                                Your account has been created with the email address <strong>{{ $userEmail }}</strong>.
                            </p>

                            <!-- Features Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td style="background-color: #f9fafb; padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb;">
                                        <p style="font-size: 14px; color: #111827; margin: 0 0 15px 0; font-weight: 600;">
                                            With {{ config('app.name') }}, you can:
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #374151; font-size: 14px;">
                                            <li style="margin-bottom: 8px;">Securely watermark your PDF documents</li>
                                            <li style="margin-bottom: 8px;">Track and manage your watermarked files</li>
                                            <li style="margin-bottom: 8px;">Share documents with secure access controls</li>
                                            <li style="margin-bottom: 0;">Verify document authenticity</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center; padding: 20px 0;">
                                        <a href="{{ $dashboardUrl }}"
                                           style="display: inline-block; padding: 14px 32px; background-color: #0ea5e9; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px;">
                                            Go to Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 14px; color: #6b7280; margin: 20px 0 0 0;">
                                If you have any questions, feel free to reach out to our support team.
                            </p>
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
