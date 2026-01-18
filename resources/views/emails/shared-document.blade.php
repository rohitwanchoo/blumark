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
                                Document Shared With You
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background-color: #ffffff; padding: 40px 30px; border-radius: 0 0 16px 16px;">
                            @if($recipientName)
                            <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0;">
                                Hi {{ $recipientName }},
                            </p>
                            @endif

                            <p style="font-size: 16px; color: #374151; margin: 0 0 20px 0;">
                                <strong>{{ $senderCompany ?: $senderName }}</strong> has shared a document with you.
                            </p>

                            <!-- File Info Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td style="background-color: #f9fafb; padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="width: 50px; vertical-align: top;">
                                                    <div style="width: 40px; height: 40px; background-color: #fee2e2; border-radius: 8px; text-align: center; line-height: 40px;">
                                                        <span style="color: #dc2626; font-size: 18px;">PDF</span>
                                                    </div>
                                                </td>
                                                <td style="vertical-align: middle; padding-left: 15px;">
                                                    <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 600;">
                                                        {{ $filename }}
                                                    </p>
                                                    <p style="font-size: 13px; color: #6b7280; margin: 5px 0 0 0;">
                                                        Expires on {{ $expiresAt }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($hasPassword)
                            <p style="font-size: 14px; color: #6b7280; margin: 0 0 20px 0; padding: 15px; background-color: #fef3c7; border-radius: 8px;">
                                <strong>Note:</strong> This document is password protected. The sender will provide the password separately.
                            </p>
                            @endif

                            <!-- Download Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center; padding: 20px 0;">
                                        <a href="{{ $downloadUrl }}"
                                           style="display: inline-block; padding: 14px 32px; background-color: #0ea5e9; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px;">
                                            Download Document
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 13px; color: #9ca3af; margin: 30px 0 0 0; text-align: center;">
                                If the button doesn't work, copy and paste this link into your browser:<br>
                                <a href="{{ $downloadUrl }}" style="color: #0ea5e9; word-break: break-all;">{{ $downloadUrl }}</a>
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
