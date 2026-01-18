<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum Upload Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in megabytes for PDF uploads.
    |
    */

    'max_upload_mb' => (int) env('MAX_UPLOAD_MB', 50),

    /*
    |--------------------------------------------------------------------------
    | File Retention
    |--------------------------------------------------------------------------
    |
    | Number of days to retain uploaded and processed files before deletion.
    |
    */

    'retention_days' => (int) env('FILE_RETENTION_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Default Watermark Settings
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'opacity' => (int) env('WATERMARK_DEFAULT_OPACITY', 20),
        'font_size' => (int) env('WATERMARK_DEFAULT_FONT_SIZE', 15),
        'color' => env('WATERMARK_DEFAULT_COLOR', '#878787'),
        'rotation' => (int) env('WATERMARK_DEFAULT_ROTATION', 45),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Paths
    |--------------------------------------------------------------------------
    |
    | Paths within the storage disk for uploaded and processed files.
    | These are stored outside the public folder for security.
    |
    */

    'paths' => [
        'uploads' => 'private/watermark/uploads',
        'outputs' => 'private/watermark/outputs',
        'watermark_images' => 'private/watermark/images',
    ],

    /*
    |--------------------------------------------------------------------------
    | Preset Templates
    |--------------------------------------------------------------------------
    |
    | Predefined watermark text templates for quick selection.
    |
    */

    'presets' => [
        'confidential' => 'CONFIDENTIAL',
        'draft' => 'DRAFT',
        'sample' => 'SAMPLE',
        'do_not_copy' => 'DO NOT COPY',
        'internal_use' => 'INTERNAL USE ONLY',
        'proprietary' => 'PROPRIETARY',
        'review_copy' => 'REVIEW COPY',
        'unofficial' => 'UNOFFICIAL',
    ],

    /*
    |--------------------------------------------------------------------------
    | Position Modes
    |--------------------------------------------------------------------------
    */

    'positions' => [
        'center' => 'Center',
        'diagonal' => 'Diagonal',
        'tiled' => 'Tiled',
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Limits
    |--------------------------------------------------------------------------
    */

    'processing' => [
        'timeout' => 300, // seconds
        'memory_limit' => '512M',
        'max_pages' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    */

    'allowed_mimes' => [
        'application/pdf',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Image Types for Watermark Images
    |--------------------------------------------------------------------------
    */

    'allowed_image_mimes' => [
        'image/png',
        'image/jpeg',
        'image/gif',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for advanced security features including fingerprinting,
    | OCR testing, and invisible watermarks.
    |
    */

    'security' => [
        // Enable document fingerprinting for tracking
        'fingerprint_enabled' => env('WATERMARK_FINGERPRINT_ENABLED', true),

        // Enable QR code watermarks for verification
        'qr_watermark_enabled' => env('WATERMARK_QR_ENABLED', true),

        // Enable multi-layer watermarks for anti-removal
        'multi_layer_enabled' => env('WATERMARK_MULTI_LAYER_ENABLED', true),

        // Enable invisible watermarks (metadata, structure embedding)
        'invisible_watermark_enabled' => env('WATERMARK_INVISIBLE_ENABLED', true),

        // Enable OCR testing functionality
        'ocr_testing_enabled' => env('WATERMARK_OCR_TESTING_ENABLED', true),

        // Track all document downloads
        'track_downloads' => env('WATERMARK_TRACK_DOWNLOADS', true),

        // Enable tamper detection
        'tamper_detection_enabled' => env('WATERMARK_TAMPER_DETECTION_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | OCR Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for OCR (Optical Character Recognition) services used for
    | watermark detection testing.
    |
    */

    'ocr' => [
        // Default OCR engine: 'tesseract' or 'google_vision'
        'default_engine' => env('OCR_DEFAULT_ENGINE', 'tesseract'),

        // Path to Tesseract binary
        'tesseract_path' => env('TESSERACT_PATH', '/usr/bin/tesseract'),

        // Tesseract language for OCR
        'tesseract_language' => env('TESSERACT_LANGUAGE', 'eng'),

        // Google Cloud Vision API key (optional)
        'google_vision_key' => env('GOOGLE_VISION_API_KEY'),

        // OCR timeout in seconds
        'timeout' => (int) env('OCR_TIMEOUT', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for document verification system.
    |
    */

    'verification' => [
        // Base URL for verification links
        'url_base' => env('VERIFICATION_URL_BASE', env('APP_URL') . '/verify'),

        // Verification token expiry in days (0 = never expires)
        'token_expiry_days' => (int) env('VERIFICATION_TOKEN_EXPIRY_DAYS', 365),

        // Encryption key for QR code data (defaults to app key)
        'encryption_key' => env('WATERMARK_ENCRYPTION_KEY'),

        // Enable public verification page (no auth required)
        'public_verification' => env('VERIFICATION_PUBLIC', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Layer Watermark Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for multi-layer watermark protection.
    |
    */

    'multi_layer' => [
        // Enable background pattern layer
        'background_layer' => env('WATERMARK_BACKGROUND_LAYER', false),

        // Background pattern text
        'background_text' => env('WATERMARK_BACKGROUND_TEXT', 'CONFIDENTIAL'),

        // Background pattern opacity (1-10)
        'background_opacity' => (int) env('WATERMARK_BACKGROUND_OPACITY', 5),

        // Enable edge/margin watermarks
        'edge_watermarks' => env('WATERMARK_EDGE_ENABLED', true),

        // Edge watermark opacity (1-30)
        'edge_opacity' => (int) env('WATERMARK_EDGE_OPACITY', 15),

        // Enable OCR-resistant elements
        'ocr_resistant' => env('WATERMARK_OCR_RESISTANT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | QR Code Settings
    |--------------------------------------------------------------------------
    |
    | Settings for QR code watermarks.
    |
    */

    'qr' => [
        // QR code size in pixels
        'size' => (int) env('WATERMARK_QR_SIZE', 150),

        // QR code position: 'top-left', 'top-right', 'bottom-left', 'bottom-right', 'center'
        'position' => env('WATERMARK_QR_POSITION', 'bottom-right'),

        // QR code size in PDF (mm)
        'pdf_size' => (int) env('WATERMARK_QR_PDF_SIZE', 20),

        // Page(s) to add QR: 'all', 'first', 'last', or page number
        'page' => env('WATERMARK_QR_PAGE', 'first'),

        // QR code opacity (0.1-1.0)
        'opacity' => (float) env('WATERMARK_QR_OPACITY', 0.9),

        // Include URL only in QR (smaller) or full data
        'url_only' => env('WATERMARK_QR_URL_ONLY', true),

        // Label text below QR
        'label' => env('WATERMARK_QR_LABEL', 'Scan to verify'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Access Tracking Settings
    |--------------------------------------------------------------------------
    |
    | Settings for document access tracking and logging.
    |
    */

    'tracking' => [
        // Days to retain access logs (0 = forever)
        'log_retention_days' => (int) env('ACCESS_LOG_RETENTION_DAYS', 365),

        // Enable geo-location lookup for IPs
        'geo_lookup' => env('ACCESS_GEO_LOOKUP', false),

        // Suspicious access threshold (downloads per hour from single IP)
        'suspicious_threshold' => (int) env('ACCESS_SUSPICIOUS_THRESHOLD', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Alerts Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for real-time security alerting on suspicious activity.
    |
    */

    'alerts' => [
        // Severity levels that trigger admin email notifications
        'notify_severities' => ['critical', 'high'],

        // Minutes to suppress duplicate alerts (deduplication window)
        'deduplication_minutes' => (int) env('ALERT_DEDUP_MINUTES', 30),

        // Rapid download detection: threshold count
        'rapid_download_threshold' => (int) env('ALERT_RAPID_DOWNLOAD_THRESHOLD', 3),

        // Rapid download detection: time window in minutes
        'rapid_download_window_minutes' => (int) env('ALERT_RAPID_DOWNLOAD_WINDOW', 5),

        // Multi-job access detection: threshold for different jobs from same IP
        'multi_job_threshold' => (int) env('ALERT_MULTI_JOB_THRESHOLD', 5),

        // Multi-job access detection: time window in hours
        'multi_job_hours' => (int) env('ALERT_MULTI_JOB_HOURS', 24),

        // Excessive downloads: threshold for total downloads on single document
        'excessive_download_threshold' => (int) env('ALERT_EXCESSIVE_DOWNLOAD_THRESHOLD', 50),
    ],

];
