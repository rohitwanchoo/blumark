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
        'opacity' => (int) env('WATERMARK_DEFAULT_OPACITY', 33),
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

];
