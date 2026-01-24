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
            h1 {
                font-size: 22px !important;
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
                            <!-- Header -->
                            <tr>
                                <td class="header-padding" style="background: linear-gradient(135deg, #059669, #10b981); padding: 40px 30px; text-align: center;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="text-align: center; padding-bottom: 20px;">
                                                <div style="display: inline-block; width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 15px; padding: 12px;">
                                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="8.5" cy="7" r="4"></circle>
                                                        <line x1="20" y1="8" x2="20" y2="14"></line>
                                                        <line x1="23" y1="11" x2="17" y2="11"></line>
                                                    </svg>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h1 style="color: #ffffff; margin: 0; font-size: 26px; font-weight: bold; letter-spacing: -0.5px;">
                                                    New User Registration
                                                </h1>
                                                <p style="color: #a7f3d0; margin: 8px 0 0 0; font-size: 14px;">
                                                    BluMark Admin Notification
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Body -->
                            <tr>
                                <td class="content-padding" style="background-color: #ffffff; padding: 40px 30px;">
                                    <p style="font-size: 15px; color: #111827; margin: 0 0 20px 0; line-height: 1.6;">
                                        A new user has registered on BluMark.
                                    </p>

                                    <!-- User Details Card -->
                                    <div style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                                        <h2 style="color: #111827; font-size: 16px; margin: 0 0 16px 0; font-weight: 600;">
                                            User Details
                                        </h2>

                                        <!-- Name -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 12px;">
                                            <tr>
                                                <td style="width: 140px; vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #6b7280; font-weight: 500;">Full Name:</p>
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #111827; font-weight: 600;">{{ $user->getFullName() }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Email -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 12px;">
                                            <tr>
                                                <td style="width: 140px; vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #6b7280; font-weight: 500;">Email:</p>
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #1d4ed8; font-weight: 600;">{{ $user->email }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                        @if($user->company_name)
                                        <!-- Company -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 12px;">
                                            <tr>
                                                <td style="width: 140px; vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #6b7280; font-weight: 500;">Company:</p>
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #111827;">{{ $user->company_name }}@if($user->company_type) ({{ $user->getCompanyTypeLabel() }})@endif</p>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        @if($user->phone)
                                        <!-- Phone -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 12px;">
                                            <tr>
                                                <td style="width: 140px; vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #6b7280; font-weight: 500;">Phone:</p>
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #111827;">{{ $user->getFormattedPhone() }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        <!-- Registration Date -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 12px;">
                                            <tr>
                                                <td style="width: 140px; vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #6b7280; font-weight: 500;">Registered:</p>
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #111827;">{{ $registeredAt }}</p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Email Verified -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="width: 140px; vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #6b7280; font-weight: 500;">Email Verified:</p>
                                                </td>
                                                <td style="vertical-align: top;">
                                                    @if($user->email_verified_at)
                                                    <span style="display: inline-block; padding: 4px 12px; background-color: #d1fae5; color: #065f46; border-radius: 6px; font-size: 13px; font-weight: 600;">Verified</span>
                                                    @else
                                                    <span style="display: inline-block; padding: 4px 12px; background-color: #fee2e2; color: #991b1b; border-radius: 6px; font-size: 13px; font-weight: 600;">Not Verified</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>

                                        @if($user->socialAccounts->isNotEmpty())
                                        <!-- Social Login -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 12px;">
                                            <tr>
                                                <td style="width: 140px; vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #6b7280; font-weight: 500;">Social Login:</p>
                                                </td>
                                                <td style="vertical-align: top;">
                                                    <p style="margin: 0; font-size: 14px; color: #111827;">
                                                        @foreach($user->socialAccounts as $account)
                                                        <span style="display: inline-block; padding: 4px 10px; background-color: #dbeafe; color: #1e40af; border-radius: 6px; font-size: 13px; margin-right: 6px;">{{ ucfirst($account->provider) }}</span>
                                                        @endforeach
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif
                                    </div>

                                    <!-- CTA Button -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 24px 0;">
                                        <tr>
                                            <td style="text-align: center;">
                                                <a href="{{ $userUrl }}"
                                                   style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; text-decoration: none; font-weight: 600; font-size: 15px; border-radius: 10px; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);">
                                                    View User Profile →
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="font-size: 13px; color: #6b7280; margin: 24px 0 0 0; line-height: 1.6; font-style: italic; text-align: center;">
                                        This is an automated notification from BluMark
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style="background-color: #f9fafb; padding: 24px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
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
