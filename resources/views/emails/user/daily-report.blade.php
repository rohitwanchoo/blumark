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
                                                <h1 style="color: #ffffff; margin: 0; font-size: 26px; font-weight: bold; letter-spacing: -0.5px;">
                                                    Your Daily Activity Summary
                                                </h1>
                                                <p style="color: #93c5fd; margin: 8px 0 0 0; font-size: 14px;">
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
                                    <p style="font-size: 17px; color: #111827; margin: 0 0 10px 0; font-weight: 600;">
                                        Hi {{ $user->getFullName() }},
                                    </p>

                                    <p style="font-size: 15px; color: #374151; margin: 0 0 24px 0; line-height: 1.6;">
                                        Here's a quick summary of your BluMark activity and account status.
                                    </p>

                                    <!-- Account Overview -->
                                    <h2 style="color: #111827; font-size: 18px; margin: 0 0 16px 0; font-weight: 600;">
                                        📊 Your Account
                                    </h2>

                                    <!-- Current Plan -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #1e40af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Current Plan</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 28px; color: #1e3a8a; font-weight: bold;">{{ $userData['planName'] }}</p>
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(30, 64, 175, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">⭐</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Credits Remaining -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #065f46; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Credits Available</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #064e3b; font-weight: bold;">{{ number_format($userData['creditsRemaining']) }}</p>
                                                    @if($userData['creditsRemaining'] < 10)
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #dc2626; font-weight: 600;">⚠️ Running low - consider purchasing more</p>
                                                    @endif
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(6, 95, 70, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">💳</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Monthly Usage -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #92400e; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Jobs This Month</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #78350f; font-weight: bold;">{{ $userData['monthlyJobCount'] }}@if($userData['monthlyJobLimit']) / {{ $userData['monthlyJobLimit'] }}@endif</p>
                                                    @if($userData['monthlyJobLimit'])
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #a16207;">{{ $userData['jobsRemaining'] }} jobs remaining this month</p>
                                                    @else
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #a16207;">Unlimited jobs</p>
                                                    @endif
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(146, 64, 14, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">📈</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Today's Activity -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #fce7f3, #fbcfe8); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #9f1239; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Jobs Today</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #881337; font-weight: bold;">{{ $userData['jobsToday'] }}</p>
                                                    @if($userData['jobsToday'] > 0)
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #be123c;">Great job staying productive! 🎉</p>
                                                    @else
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #be123c;">No jobs created today</p>
                                                    @endif
                                                </td>
                                                <td style="vertical-align: middle; text-align: right; width: 60px;">
                                                    <div style="width: 50px; height: 50px; background: rgba(159, 18, 57, 0.15); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                                                        <span style="font-size: 24px;">📄</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Lender Submissions -->
                                    <div class="stat-card" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    <p style="margin: 0; font-size: 14px; color: #3730a3; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Submissions to Lenders</p>
                                                    <p style="margin: 8px 0 0 0; font-size: 32px; color: #312e81; font-weight: bold;">{{ $userData['lenderSubmissionsToday'] }}</p>
                                                    @if($userData['totalLendersSentToday'] > 0)
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #4338ca;">{{ $userData['totalLendersSentToday'] }} lender{{ $userData['totalLendersSentToday'] > 1 ? 's' : '' }} received documents today</p>
                                                    @else
                                                    <p style="margin: 6px 0 0 0; font-size: 12px; color: #4338ca;">This month: {{ $userData['lenderSubmissionsMonth'] }} submission{{ $userData['lenderSubmissionsMonth'] != 1 ? 's' : '' }}</p>
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

                                    <!-- Divider -->
                                    <div style="border-top: 2px solid #e5e7eb; margin: 30px 0;"></div>

                                    <!-- Recent Jobs -->
                                    @if(!empty($userData['recentJobs']) && count($userData['recentJobs']) > 0)
                                    <h2 style="color: #111827; font-size: 18px; margin: 0 0 16px 0; font-weight: 600;">
                                        📋 Recent Watermark Jobs
                                    </h2>

                                    <div style="background-color: #f9fafb; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                                        @foreach($userData['recentJobs'] as $index => $job)
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="@if($index > 0) margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; @endif">
                                            <tr>
                                                <td style="vertical-align: top; width: 50px;">
                                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                                        @if($job->status === 'completed')
                                                        ✓
                                                        @elseif($job->status === 'processing')
                                                        ⏳
                                                        @elseif($job->status === 'failed')
                                                        ✗
                                                        @else
                                                        •
                                                        @endif
                                                    </div>
                                                </td>
                                                <td style="vertical-align: top; padding-left: 12px;">
                                                    <p style="margin: 0; font-size: 15px; color: #111827; font-weight: 600;">{{ Str::limit($job->original_filename, 40) }}</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #6b7280;">
                                                        Status:
                                                        @if($job->status === 'completed')
                                                        <span style="color: #059669; font-weight: 600;">Completed</span>
                                                        @elseif($job->status === 'processing')
                                                        <span style="color: #f59e0b; font-weight: 600;">Processing</span>
                                                        @elseif($job->status === 'failed')
                                                        <span style="color: #dc2626; font-weight: 600;">Failed</span>
                                                        @else
                                                        <span style="color: #6b7280; font-weight: 600;">{{ ucfirst($job->status) }}</span>
                                                        @endif
                                                    </p>
                                                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #9ca3af;">{{ $job->created_at->diffForHumans() }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                        @endforeach
                                    </div>
                                    @endif

                                    <!-- Quick Actions -->
                                    <div style="background-color: #eff6ff; border-radius: 12px; padding: 24px; border: 1px solid #dbeafe; margin-bottom: 24px;">
                                        <h3 style="color: #1e40af; font-size: 16px; margin: 0 0 12px 0; font-weight: 600;">
                                            🚀 Quick Actions
                                        </h3>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <a href="{{ route('watermark-jobs.create') }}" style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 500;">
                                                        → Create new watermark job
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <a href="{{ route('watermark-jobs.index') }}" style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 500;">
                                                        → View all your jobs
                                                    </a>
                                                </td>
                                            </tr>
                                            @if($userData['creditsRemaining'] < 10)
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <a href="{{ route('billing') }}" style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 500;">
                                                        → Purchase more credits
                                                    </a>
                                                </td>
                                            </tr>
                                            @endif
                                            @if($user->isOnFreePlan())
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <a href="{{ route('billing') }}" style="color: #3b82f6; text-decoration: none; font-size: 14px; font-weight: 500;">
                                                        → Upgrade to Pro or Enterprise
                                                    </a>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>

                                    <!-- CTA Button -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 32px 0 24px 0;">
                                        <tr>
                                            <td style="text-align: center;">
                                                <a href="{{ $dashboardUrl }}"
                                                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 10px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);">
                                                    Go to Dashboard →
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="font-size: 13px; color: #6b7280; margin: 24px 0 0 0; line-height: 1.6; text-align: center;">
                                        Have questions? Reply to this email and we'll be happy to help!
                                    </p>

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
                                <td style="background-color: #f9fafb; padding: 24px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                                    <p style="font-size: 13px; color: #6b7280; margin: 0 0 8px 0;">
                                        To unsubscribe from daily reports, visit your
                                        <a href="{{ route('profile.show') }}" style="color: #3b82f6; text-decoration: none;">account settings</a>
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
