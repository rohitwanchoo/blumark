# PDF Watermarking Platform

A production-ready PDF watermarking platform built with Laravel 11. Upload PDFs and add customizable text or image watermarks with support for various positions, opacity, rotation, and tiling.

## Features

- **Text Watermarks**: Add custom text with adjustable font size, color, opacity, and rotation
- **Image Watermarks**: Upload logos or images as watermarks with scalable sizing
- **Multiple Positions**: Center, diagonal, or tiled watermark placement
- **Queue Processing**: Large PDFs are processed in the background via Laravel queues
- **User Authentication**: Secure login/registration with Laravel Breeze
- **Job Management**: Track processing status, view history, and download completed files
- **Auto-Cleanup**: Scheduled command to remove old files after configurable retention period
- **Security**: Files stored outside public directory, owner-only access to downloads

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade templates + Alpine.js + Tailwind CSS (via CDN)
- **PDF Processing**: FPDI + TCPDF
- **Database**: MySQL (SQLite supported for development)
- **Queue**: Laravel Queue (database driver)
- **Authentication**: Laravel Breeze (email/password)

## PDF Library Choice

This platform uses **FPDI** combined with **TCPDF** for PDF watermarking:

- **FPDI** (Free PDF Document Importer): Imports existing PDF pages as templates
- **TCPDF**: Provides the PDF generation engine with alpha/transparency support

### Tradeoffs

**Pros:**
- Well-maintained and actively supported
- Native PHP, no external dependencies required
- Full alpha/opacity support for text and images
- Handles multi-page PDFs efficiently
- Works with most standard PDFs

**Cons:**
- Cannot preserve interactive elements (forms, links, annotations)
- Very large or complex PDFs may be slow to process
- Some encrypted PDFs may not be processable
- PDFs with certain compression types may have issues

**Alternatives Considered:**
- **mPDF**: Good but less flexible for importing existing PDFs
- **FPDF**: Lighter but no built-in alpha support
- **Ghostscript/ImageMagick**: Requires external binaries

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+ (or SQLite for development)
- Node.js & npm (optional, for asset compilation)

### PHP Extensions

- ext-gd (for image processing)
- ext-mbstring
- ext-pdo_mysql (or pdo_sqlite)
- ext-zip

## Installation

### 1. Clone and Install Dependencies

```bash
cd /var/www/html/watermarking
composer install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=watermarking
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Queue (use 'sync' for development without queue worker)
QUEUE_CONNECTION=database

# Watermarking Configuration
MAX_UPLOAD_MB=50
FILE_RETENTION_DAYS=7
WATERMARK_DEFAULT_OPACITY=50
WATERMARK_DEFAULT_FONT_SIZE=48
WATERMARK_DEFAULT_COLOR=#888888
WATERMARK_DEFAULT_ROTATION=-45
```

### 3. Create Database

```bash
mysql -u root -p -e "CREATE DATABASE watermarking;"
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. (Optional) Seed Demo User

```bash
php artisan db:seed
```

This creates a demo user:
- **Email**: demo@example.com
- **Password**: password

### 6. Create Storage Directories

```bash
php artisan storage:link
mkdir -p storage/app/private/watermark/{uploads,outputs,images}
chmod -R 775 storage
```

### 7. Start the Queue Worker

For production:
```bash
php artisan queue:work --daemon
```

For development (processes jobs immediately):
```bash
# Set QUEUE_CONNECTION=sync in .env
```

### 8. Start Development Server

```bash
php artisan serve
```

Visit: http://localhost:8000

## Production Deployment

### Supervisor Configuration (Queue Worker)

Create `/etc/supervisor/conf.d/watermark-worker.conf`:

```ini
[program:watermark-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/watermarking/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/watermarking/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start watermark-worker:*
```

### Scheduled Tasks (Cleanup)

Add to crontab:

```bash
* * * * * cd /var/www/html/watermarking && php artisan schedule:run >> /dev/null 2>&1
```

The cleanup command runs daily and removes jobs older than `FILE_RETENTION_DAYS`.

### Manual Cleanup

```bash
# Dry run (preview what will be deleted)
php artisan watermark:cleanup --dry-run

# Delete files older than 7 days (default)
php artisan watermark:cleanup

# Delete files older than 3 days
php artisan watermark:cleanup --days=3
```

## Configuration

### Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `MAX_UPLOAD_MB` | 50 | Maximum PDF upload size in megabytes |
| `FILE_RETENTION_DAYS` | 7 | Days to keep files before auto-deletion |
| `WATERMARK_DEFAULT_OPACITY` | 50 | Default opacity percentage (1-100) |
| `WATERMARK_DEFAULT_FONT_SIZE` | 48 | Default font size for text watermarks |
| `WATERMARK_DEFAULT_COLOR` | #888888 | Default text color (hex) |
| `WATERMARK_DEFAULT_ROTATION` | -45 | Default rotation angle in degrees |

### Config File

Additional settings in `config/watermark.php`:

- Preset templates (CONFIDENTIAL, DRAFT, etc.)
- Storage paths
- Processing limits (timeout, memory, max pages)
- Allowed MIME types

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dashboard` | Dashboard with upload form |
| GET | `/jobs` | List user's watermark jobs |
| POST | `/jobs` | Create new watermark job |
| GET | `/jobs/{id}` | View job details |
| DELETE | `/jobs/{id}` | Delete job and files |
| GET | `/jobs/{id}/status` | Get job status (JSON) |
| GET | `/jobs/{id}/download` | Download watermarked PDF |
| GET | `/jobs/{id}/preview` | View PDF inline |

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Test Coverage

- **Feature Tests**: Upload validation, job creation, queue dispatch, download authorization
- **Unit Tests**: PdfWatermarkService text/image watermarking, color conversion

## Security Considerations

1. **File Storage**: All PDFs stored in `storage/app/private/` (not publicly accessible)
2. **Authorization**: Users can only access their own jobs and files
3. **Validation**: Strict file type and size validation
4. **CSRF Protection**: All forms protected against CSRF
5. **File Cleanup**: Automatic deletion of old files

### TODO: Virus Scanning

For production environments, consider adding virus scanning:

```php
// In StoreWatermarkJobRequest or a custom middleware
// Using ClamAV or similar
```

## Troubleshooting

### PDF Processing Fails

1. Check PHP memory limit: `memory_limit = 512M`
2. Check max execution time: `max_execution_time = 300`
3. Verify GD extension is installed: `php -m | grep gd`
4. Check storage permissions: `chmod -R 775 storage`

### Queue Jobs Not Processing

1. Verify queue worker is running: `php artisan queue:work`
2. Check failed jobs: `php artisan queue:failed`
3. Retry failed jobs: `php artisan queue:retry all`

### Large File Uploads Fail

1. Check `upload_max_filesize` in php.ini
2. Check `post_max_size` in php.ini
3. Check `MAX_UPLOAD_MB` in .env
4. Check nginx/apache client_max_body_size

## License

MIT License

## Credits

Built with:
- [Laravel](https://laravel.com)
- [FPDI](https://www.setasign.com/products/fpdi/about/)
- [TCPDF](https://tcpdf.org)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
