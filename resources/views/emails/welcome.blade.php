<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style type="text/css">
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .content-padding {
                padding: 20px !important;
            }
            .header-padding {
                padding: 30px 20px !important;
            }
            .footer-padding {
                padding: 20px !important;
            }
            h1 {
                font-size: 24px !important;
            }
            h2 {
                font-size: 16px !important;
            }
            .feature-icon {
                width: 28px !important;
                height: 28px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <center style="width: 100%; background-color: #f3f4f6;">
        <div style="max-width: 600px; margin: 0 auto;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px;" class="email-container">
                <tr>
                    <td style="padding: 20px 0;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff; border-radius: 16px; overflow: hidden;">
                            <!-- Header with Logo -->
                            <tr>
                                <td class="header-padding" style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); padding: 40px 30px; text-align: center;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="text-align: center; padding-bottom: 20px;">
                                                <div style="display: inline-block; width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 15px; padding: 12px;">
                                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                        <line x1="9" y1="13" x2="15" y2="13"></line>
                                                        <line x1="9" y1="17" x2="15" y2="17"></line>
                                                    </svg>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold; letter-spacing: -0.5px;">
                                                    Welcome to BluMark!
                                                </h1>
                                                <p style="color: #93c5fd; margin: 8px 0 0 0; font-size: 14px;">
                                                    Security Built Into Every File
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Body -->
                            <tr>
                                <td class="content-padding" style="background-color: #ffffff; padding: 40px 30px;">
                                    <p style="font-size: 17px; color: #111827; margin: 0 0 10px 0; font-weight: 600;">
                                        Hi {{ $userName }},
                                    </p>

                                    <p style="font-size: 15px; color: #374151; margin: 0 0 20px 0; line-height: 1.6;">
                                        Thank you for joining BluMark! We're thrilled to have you as part of our community. Your account is now active and ready to help you protect and manage your important documents.
                                    </p>

                                    <p style="font-size: 15px; color: #374151; margin: 0 0 24px 0; line-height: 1.6;">
                                        Your account has been successfully created with the email address <strong style="color: #1d4ed8;">{{ $userEmail }}</strong>. You can now start watermarking your PDFs with professional-grade security features.
                                    </p>

                                    <!-- Divider -->
                                    <div style="border-top: 2px solid #e5e7eb; margin: 30px 0;"></div>

                                    <!-- What You Can Do -->
                                    <h2 style="color: #111827; font-size: 18px; margin: 0 0 16px 0; font-weight: 600;">
                                        What you can do with BluMark:
                                    </h2>

                                    <!-- Feature 1 -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 16px;">
                                        <tr>
                                            <td style="width: 40px; vertical-align: top; padding-top: 2px;">
                                                <div class="feature-icon" style="width: 32px; height: 32px; background-color: #dbeafe; border-radius: 8px; text-align: center; line-height: 32px;">
                                                    <span style="font-size: 18px;">🔒</span>
                                                </div>
                                            </td>
                                            <td style="vertical-align: top; padding-left: 12px;">
                                                <p style="margin: 0; font-size: 15px; color: #111827; font-weight: 600;">Secure PDF Watermarking</p>
                                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280; line-height: 1.5;">Add custom watermarks with ISO and lender information to protect your documents</p>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Feature 2 -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 16px;">
                                        <tr>
                                            <td style="width: 40px; vertical-align: top; padding-top: 2px;">
                                                <div class="feature-icon" style="width: 32px; height: 32px; background-color: #dbeafe; border-radius: 8px; text-align: center; line-height: 32px;">
                                                    <span style="font-size: 18px;">📊</span>
                                                </div>
                                            </td>
                                            <td style="vertical-align: top; padding-left: 12px;">
                                                <p style="margin: 0; font-size: 15px; color: #111827; font-weight: 600;">Track & Manage Files</p>
                                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280; line-height: 1.5;">Monitor your watermarked documents and manage all your files in one place</p>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Feature 3 -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 16px;">
                                        <tr>
                                            <td style="width: 40px; vertical-align: top; padding-top: 2px;">
                                                <div class="feature-icon" style="width: 32px; height: 32px; background-color: #dbeafe; border-radius: 8px; text-align: center; line-height: 32px;">
                                                    <span style="font-size: 18px;">🔗</span>
                                                </div>
                                            </td>
                                            <td style="vertical-align: top; padding-left: 12px;">
                                                <p style="margin: 0; font-size: 15px; color: #111827; font-weight: 600;">Secure Sharing</p>
                                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280; line-height: 1.5;">Share documents with controlled access and track who views your files</p>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Feature 4 -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">
                                        <tr>
                                            <td style="width: 40px; vertical-align: top; padding-top: 2px;">
                                                <div class="feature-icon" style="width: 32px; height: 32px; background-color: #dbeafe; border-radius: 8px; text-align: center; line-height: 32px;">
                                                    <span style="font-size: 18px;">✓</span>
                                                </div>
                                            </td>
                                            <td style="vertical-align: top; padding-left: 12px;">
                                                <p style="margin: 0; font-size: 15px; color: #111827; font-weight: 600;">Document Verification</p>
                                                <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280; line-height: 1.5;">Verify document authenticity and ensure your files haven't been tampered with</p>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- CTA Button -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 32px 0 24px 0;">
                                        <tr>
                                            <td style="text-align: center;">
                                                <a href="{{ $dashboardUrl }}"
                                                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 10px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);">
                                                    Get Started Now →
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Divider -->
                                    <div style="border-top: 2px solid #e5e7eb; margin: 30px 0;"></div>

                                    <!-- Need Help Section -->
                                    <div style="background-color: #f9fafb; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb;">
                                        <p style="font-size: 15px; color: #111827; margin: 0 0 8px 0; font-weight: 600;">
                                            Need help getting started?
                                        </p>
                                        <p style="font-size: 14px; color: #6b7280; margin: 0; line-height: 1.6;">
                                            Our team is here to help! If you have any questions or need assistance, don't hesitate to reach out. We're committed to making your experience with BluMark seamless and secure.
                                        </p>
                                    </div>

                                    <!-- Signature -->
                                    <p style="font-size: 15px; color: #374151; margin: 32px 0 8px 0; line-height: 1.6;">
                                        Best regards,<br>
                                        <strong style="color: #111827;">The BluMark Team</strong>
                                    </p>
                                    <p style="font-size: 14px; color: #6b7280; margin: 0; font-style: italic;">
                                        Security Built Into Every File
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td class="footer-padding" style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                                    <p style="font-size: 14px; color: #6b7280; margin: 0 0 12px 0;">
                                        <a href="{{ config('app.url') }}/dashboard" style="color: #3b82f6; text-decoration: none; margin: 0 8px;">Dashboard</a> •
                                        <a href="{{ config('app.url') }}/billing" style="color: #3b82f6; text-decoration: none; margin: 0 8px;">Billing</a> •
                                        <a href="{{ config('app.url') }}/docs/api" style="color: #3b82f6; text-decoration: none; margin: 0 8px;">API Docs</a>
                                    </p>
                                    <p style="font-size: 13px; color: #9ca3af; margin: 0;">
                                        © {{ date('Y') }} BluMark. All rights reserved.<br>
                                        <a href="{{ config('app.url') }}" style="color: #3b82f6; text-decoration: none;">www.blumark.pro</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </center>
</body>
</html>
