<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document from {{ $senderCompany }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .document-info {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .document-info p {
            margin: 5px 0;
        }
        .document-info strong {
            color: #4f46e5;
        }
        .download-btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .download-btn:hover {
            opacity: 0.9;
        }
        .attachment-note {
            background: #ecfdf5;
            border: 1px solid #10b981;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #065f46;
        }
        .footer {
            background: #f9fafb;
            padding: 20px 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .sender-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Document Shared</h1>
    </div>

    <div class="content">
        @if(!empty($customBody))
            {{-- Custom template body --}}
            <div style="white-space: pre-wrap;">{{ $customBody }}</div>
        @else
            {{-- Default email body --}}
            <p class="greeting">Hello {{ $lenderName }},</p>

            <p>{{ $senderName }} from <strong>{{ $senderCompany }}</strong> has shared a document with you.</p>

            <div class="document-info">
                <p><strong>Document:</strong> {{ $documentName }}</p>
                <p><strong>Prepared for:</strong> {{ $lenderCompany }}</p>
                <p><strong>Sent by:</strong> {{ $senderName }} ({{ $senderCompany }})</p>
            </div>
        @endif

        @if($attachPdf)
            <div class="attachment-note">
                The document is attached to this email as a PDF file.
            </div>
        @else
            <p>Click the button below to download your document:</p>
            <p style="text-align: center;">
                <a href="{{ $downloadUrl }}" class="download-btn">Download Document</a>
            </p>
            <p style="font-size: 14px; color: #6b7280;">
                If the button doesn't work, copy and paste this link into your browser:<br>
                <a href="{{ $downloadUrl }}" style="color: #4f46e5;">{{ $downloadUrl }}</a>
            </p>
        @endif

        @if(empty($customBody))
            <div class="sender-info">
                <p>Best regards,<br><strong>{{ $senderName }}</strong><br>{{ $senderCompany }}</p>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>This email was sent via {{ config('app.name') }}</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
