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
            .stat-card {
                margin-bottom: 12px !important;
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
                                <td class="header-padding" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); padding: 40px 30px; text-align: center;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="text-align: center; padding-bottom: 20px;">
                                                <div style="display: inline-block; width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 15px; padding: 12px;">
                                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M3 3v18h18"></path>
                                                        <path d="M18 17V9"></path>
                                                        <path d="M13 17V5"></path>
                                                        <path d="M8 17v-3"></path>
                                                    </svg>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h1 style="color: #ffffff; margin: 0; font-size: 26px; font-weight: bold; letter-spacing: -0.5px;">
                                                    Daily User Activity Report
                                                </h1>
                                                <p style="color: #ddd6fe; margin: 8px 0 0 0; font-size: 14px;">
                                                    {{ $reportDate }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Body -->
                            <tr>
                                <td class="content-padding" style="background-color: #ffffff; padding: 40px 30px;">
                                    <p style="font-size: 15px; color: #111827; margin: 0 0 24px 0; line-height: 1.6;">
                                        Here's your daily summary of BluMark user activity.
                                    </p>

                                    <!-- Statistics Grid -->
                                    <h2 style="color: #111827; font-size: 18px; margin: 0 0 16px 0; font-weight: 600;">
                                        📊 Key Metrics
                                    </h2>

                                    <!-- Stat Card: New Users -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #1e40af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">New Users Today</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #1e3a8a; font-weight: bold;">{{ $reportData['newUsersToday'] }}</p>
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(30, 64, 175, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">👥</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Stat Card: Total Users -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #065f46; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Users</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #064e3b; font-weight: bold;">{{ $reportData['totalUsers'] }}</p>
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(6, 95, 70, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">📈</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Stat Card: Active Users -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #92400e; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Active Today</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #78350f; font-weight: bold;">{{ $reportData['activeUsersToday'] }}</p>
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(146, 64, 14, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">⚡</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Stat Card: Watermark Jobs -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #fce7f3, #fbcfe8); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #9f1239; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Watermark Jobs Today</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #881337; font-weight: bold;">{{ $reportData['watermarkJobsToday'] }}</p>
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(159, 18, 57, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">📄</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Stat Card: Lender Submissions -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #3730a3; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Lender Submissions Today</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #312e81; font-weight: bold;">{{ $reportData['lenderSubmissionsToday'] }}</p>
                                                    @if($reportData['totalLendersSentToday'] > 0)
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #4338ca;">{{ $reportData['totalLendersSentToday'] }} lenders received documents</p>
                                                    @endif
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(55, 48, 163, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">📤</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Stat Card: Revenue Today -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #d1fae5, #6ee7b7); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #065f46; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Revenue Today</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #064e3b; font-weight: bold;">${{ number_format($reportData['revenueToday'] / 100, 2) }}</p>
                                                    @if($reportData['creditRevenueToday'] > 0 || $reportData['subscriptionRevenueToday'] > 0)
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #047857;">
                                                        Credits: ${{ number_format($reportData['creditRevenueToday'] / 100, 2) }} •
                                                        Subscriptions: ${{ number_format($reportData['subscriptionRevenueToday'] / 100, 2) }}
                                                        @if($reportData['newSubscriptionsToday'] > 0)
                                                        ({{ $reportData['newSubscriptionsToday'] }} new)
                                                        @endif
                                                    </p>
                                                    @endif
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(6, 95, 70, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">💰</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Stat Card: Revenue This Month -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #3730a3; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Revenue This Month</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #312e81; font-weight: bold;">${{ number_format($reportData['revenueMonth'] / 100, 2) }}</p>
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(55, 48, 163, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">📊</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Stat Card: MRR -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #fef9c3, #fde047); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #92400e; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Monthly Recurring Revenue (MRR)</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #78350f; font-weight: bold;">${{ number_format($reportData['mrr'] / 100, 2) }}</p>
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #a16207;">From active subscriptions</p>
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(146, 64, 14, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">🔄</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Divider -->
                                    <div style="border-top: 2px solid #e5e7eb; margin: 30px 0;"></div>

                                    <!-- Recent Users Section -->
                                    @if(!empty($reportData['recentUsers']) && count($reportData['recentUsers']) > 0)
                                    <h2 style="color: #111827; font-size: 18px; margin: 0 0 16px 0; font-weight: 600;">
                                        🆕 Recent Registrations
                                    </h2>

                                    <div style="background-color: #f9fafb; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb;">
                                        @foreach($reportData['recentUsers'] as $index => $user)
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="@if($index > 0) margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; @endif">
                                            <tr>
                                                <td style="vertical-align: top; width: 50px;">
                                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px;">
                                                        {{ strtoupper(substr($user->getFullName(), 0, 1)) }}
                                                    </div>
                                                </td>
                                                <td style="vertical-align: top; padding-left: 12px;">
                                                    <p style="margin: 0; font-size: 15px; color: #111827; font-weight: 600;">{{ $user->getFullName() }}</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #3b82f6;">{{ $user->email }}</p>
                                                    @if($user->company_name)
                                                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #6b7280;">{{ $user->company_name }}@if($user->company_type) • {{ $user->getCompanyTypeLabel() }}@endif</p>
                                                    @endif
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #9ca3af;">Registered {{ $user->created_at->diffForHumans() }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                        @endforeach
                                    </div>
                                    @else
                                    <div style="background-color: #f9fafb; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb; text-align: center;">
                                        <p style="margin: 0; font-size: 14px; color: #6b7280;">No new users registered today.</p>
                                    </div>
                                    @endif

                                    <!-- CTA Button -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 32px 0 24px 0;">
                                        <tr>
                                            <td style="text-align: center;">
                                                <a href="{{ $dashboardUrl }}"
                                                   style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #a78bfa, #7c3aed); color: #ffffff; text-decoration: none; font-weight: 600; font-size: 15px; border-radius: 10px; box-shadow: 0 4px 6px rgba(124, 58, 237, 0.3);">
                                                    View Full Dashboard →
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="font-size: 13px; color: #6b7280; margin: 24px 0 0 0; line-height: 1.6; font-style: italic; text-align: center;">
                                        This report is automatically generated daily at midnight
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
