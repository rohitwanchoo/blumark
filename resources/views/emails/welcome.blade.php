<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome to {{ config('app.name', 'Blumark') }}</title>
    <style type="text/css">
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 5% !important;
            }
            .feature-row td {
                display: block !important;
                width: 100% !important;
                padding: 10px 0 !important;
            }
            .stats-row td {
                display: block !important;
                width: 100% !important;
                border-left: none !important;
                border-right: none !important;
                border-bottom: 1px solid #cbd5e1 !important;
            }
            .stats-row td:last-child {
                border-bottom: none !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #e2e8f0; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, sans-serif;">

    <!-- Preheader text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Welcome to BluMark Pro - Your secure document watermarking platform is ready!
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #e2e8f0;">
        <tr>
            <td align="center" class="email-wrapper" style="padding: 30px 10%;">

                <!-- Main Container - 80% width -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 25px rgba(0, 0, 0, 0.12);">

                    <!-- Header with Logo -->
                    <tr>
                        <td style="padding: 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 50%, #172554 100%); padding: 50px 40px; text-align: center;">
                                        <!-- Logo Icon -->
                                        <div style="width: 80px; height: 80px; background-color: rgba(255,255,255,0.15); border-radius: 20px; margin: 0 auto 20px auto; line-height: 80px;">
                                            <span style="font-size: 40px;">📄</span>
                                        </div>
                                        <h1 style="margin: 0; color: #ffffff; font-size: 38px; font-weight: 800; letter-spacing: -1px;">BluMark</h1>
                                        <p style="margin: 10px 0 0 0; color: #93c5fd; font-size: 14px; text-transform: uppercase; letter-spacing: 2px;">Secure Document Platform</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Welcome Message -->
                    <tr>
                        <td style="padding: 40px 40px 25px 40px; background-color: #f8fafc;">
                            <h2 style="margin: 0; color: #0f172a; font-size: 28px; font-weight: 700;">Welcome, {{ $userName }}! 👋</h2>
                            <p style="margin: 15px 0 0 0; color: #475569; font-size: 16px; line-height: 1.7;">
                                Thank you for joining <span style="color: #1e40af; font-weight: 600;">BluMark Pro</span>. We are excited to have you on board. Start protecting your documents today!
                            </p>
                        </td>
                    </tr>

                    <!-- Features Grid -->
                    <tr>
                        <td style="padding: 15px 30px; background-color: #f8fafc;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">

                                <!-- Feature Row 1 -->
                                <tr class="feature-row">
                                    <td width="50%" style="padding: 10px; vertical-align: top;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 14px; border: 1px solid #cbd5e1;">
                                            <tr>
                                                <td style="padding: 25px;">
                                                    <div style="width: 55px; height: 55px; background: linear-gradient(135deg, #1e40af, #1e3a8a); border-radius: 14px; text-align: center; line-height: 55px; margin-bottom: 15px;">
                                                        <span style="font-size: 26px;">🔒</span>
                                                    </div>
                                                    <h3 style="margin: 0 0 10px 0; color: #0f172a; font-size: 17px; font-weight: 700;">Watermark PDFs</h3>
                                                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.5;">Custom text watermarks with full styling control</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" style="padding: 10px; vertical-align: top;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 14px; border: 1px solid #cbd5e1;">
                                            <tr>
                                                <td style="padding: 25px;">
                                                    <div style="width: 55px; height: 55px; background: linear-gradient(135deg, #059669, #047857); border-radius: 14px; text-align: center; line-height: 55px; margin-bottom: 15px;">
                                                        <span style="font-size: 26px;">📤</span>
                                                    </div>
                                                    <h3 style="margin: 0 0 10px 0; color: #0f172a; font-size: 17px; font-weight: 700;">Secure Distributions</h3>
                                                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.5;">Share & track document access in real-time</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Feature Row 2 -->
                                <tr class="feature-row">
                                    <td width="50%" style="padding: 10px; vertical-align: top;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 14px; border: 1px solid #cbd5e1;">
                                            <tr>
                                                <td style="padding: 25px;">
                                                    <div style="width: 55px; height: 55px; background: linear-gradient(135deg, #d97706, #b45309); border-radius: 14px; text-align: center; line-height: 55px; margin-bottom: 15px;">
                                                        <span style="font-size: 26px;">🏦</span>
                                                    </div>
                                                    <h3 style="margin: 0 0 10px 0; color: #0f172a; font-size: 17px; font-weight: 700;">Manage Lenders</h3>
                                                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.5;">Submit applications directly from BluMark</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" style="padding: 10px; vertical-align: top;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 14px; border: 1px solid #cbd5e1;">
                                            <tr>
                                                <td style="padding: 25px;">
                                                    <div style="width: 55px; height: 55px; background: linear-gradient(135deg, #7c3aed, #6d28d9); border-radius: 14px; text-align: center; line-height: 55px; margin-bottom: 15px;">
                                                        <span style="font-size: 26px;">✅</span>
                                                    </div>
                                                    <h3 style="margin: 0 0 10px 0; color: #0f172a; font-size: 17px; font-weight: 700;">Verify Authenticity</h3>
                                                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.5;">Confirm document origin instantly</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                    <!-- CTA Section -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f8fafc;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); border-radius: 14px;">
                                <tr>
                                    <td style="padding: 35px; text-align: center;">
                                        <p style="margin: 0 0 20px 0; color: #ffffff; font-size: 20px; font-weight: 700;">Ready to secure your documents?</p>
                                        <a href="{{ $dashboardUrl }}" style="display: inline-block; background-color: #ffffff; color: #1e3a8a; padding: 16px 50px; text-decoration: none; border-radius: 50px; font-size: 16px; font-weight: 700; box-shadow: 0 6px 16px rgba(0,0,0,0.2);">Launch Dashboard →</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Stats Section -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; background-color: #f8fafc;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 1px solid #cbd5e1;">
                                <tr class="stats-row">
                                    <td width="33.33%" style="text-align: center; padding: 30px 15px;">
                                        <p style="margin: 0; color: #1e40af; font-size: 30px; font-weight: 800;">256-bit</p>
                                        <p style="margin: 6px 0 0 0; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Encryption</p>
                                    </td>
                                    <td width="33.33%" style="text-align: center; padding: 30px 15px; border-left: 1px solid #cbd5e1; border-right: 1px solid #cbd5e1;">
                                        <p style="margin: 0; color: #1e40af; font-size: 30px; font-weight: 800;">99.9%</p>
                                        <p style="margin: 6px 0 0 0; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Uptime</p>
                                    </td>
                                    <td width="33.33%" style="text-align: center; padding: 30px 15px;">
                                        <p style="margin: 0; color: #1e40af; font-size: 30px; font-weight: 800;">24/7</p>
                                        <p style="margin: 6px 0 0 0; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Support</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1e293b; padding: 35px 40px; text-align: center;">
                            <p style="margin: 0 0 12px 0; color: #f1f5f9; font-size: 18px; font-weight: 700;">The BluMark Team</p>
                            <p style="margin: 0 0 20px 0;">
                                <a href="{{ config('app.url') }}" style="color: #60a5fa; text-decoration: none; font-size: 15px;">www.blumark.pro</a>
                            </p>
                            <p style="margin: 0; color: #94a3b8; font-size: 12px; line-height: 1.7;">
                                Need help? Simply reply to this email.<br>
                                &copy; {{ date('Y') }} BluMark. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
