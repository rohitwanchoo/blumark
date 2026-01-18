<?php

namespace App\Services;

use App\Models\DocumentFingerprint;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Tcpdf\Fpdi;

class QrWatermarkService
{
    protected DocumentFingerprintService $fingerprintService;

    public function __construct(DocumentFingerprintService $fingerprintService)
    {
        $this->fingerprintService = $fingerprintService;
    }

    /**
     * Generate a QR code image for document verification.
     */
    public function generateVerificationQr(DocumentFingerprint $fingerprint, array $options = []): string
    {
        $qrData = $this->fingerprintService->generateQrData($fingerprint);
        $qrContent = json_encode($qrData);

        // For small QR, just use the URL
        if (($options['url_only'] ?? false) || strlen($qrContent) > 500) {
            $qrContent = $qrData['url'];
        }

        $size = $options['size'] ?? 150;
        $margin = $options['margin'] ?? 10;

        $result = (new Builder())
            ->writer(new PngWriter())
            ->data($qrContent)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size($size)
            ->margin($margin)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        // Save to temp file
        $tempPath = sys_get_temp_dir() . '/qr_' . uniqid() . '.png';
        $result->saveToFile($tempPath);

        return $tempPath;
    }

    /**
     * Embed QR code in a PDF document.
     */
    public function embedQrInPdf(string $pdfPath, string $qrImagePath, array $options = []): string
    {
        $position = $options['position'] ?? 'bottom-right';
        $page = $options['page'] ?? 'all'; // 'all', 'first', 'last', or page number
        $size = $options['size'] ?? 25; // mm
        $opacity = $options['opacity'] ?? 1.0;
        $margin = $options['margin'] ?? 10; // mm from edge

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($pdfPath);

            for ($i = 1; $i <= $pageCount; $i++) {
                $templateId = $pdf->importPage($i);
                $templateSize = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($templateSize['orientation'], [$templateSize['width'], $templateSize['height']]);
                $pdf->useTemplate($templateId, 0, 0, $templateSize['width'], $templateSize['height']);

                // Determine if we should add QR to this page
                $addQr = match ($page) {
                    'all' => true,
                    'first' => $i === 1,
                    'last' => $i === $pageCount,
                    default => is_numeric($page) && (int) $page === $i,
                };

                if ($addQr) {
                    $coords = $this->calculateQrPosition(
                        $position,
                        $templateSize['width'],
                        $templateSize['height'],
                        $size,
                        $margin
                    );

                    // Set alpha for opacity
                    if ($opacity < 1.0) {
                        $pdf->SetAlpha($opacity);
                    }

                    $pdf->Image($qrImagePath, $coords['x'], $coords['y'], $size, $size, 'PNG');

                    // Reset alpha
                    if ($opacity < 1.0) {
                        $pdf->SetAlpha(1);
                    }

                    // Add small label under QR if requested
                    if ($options['label'] ?? false) {
                        $pdf->SetFont('helvetica', '', 6);
                        $pdf->SetTextColor(128, 128, 128);
                        $pdf->SetXY($coords['x'], $coords['y'] + $size + 1);
                        $pdf->Cell($size, 3, $options['label'], 0, 0, 'C');
                    }
                }
            }

            // Save to output path
            $outputPath = $options['output_path'] ?? $this->generateOutputPath($pdfPath);
            $pdf->Output($outputPath, 'F');

            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Failed to embed QR code in PDF', [
                'pdf' => $pdfPath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Decode QR code from a PDF file.
     */
    public function decodeQrFromPdf(string $pdfPath): ?array
    {
        try {
            // Convert first page to image
            $tempDir = sys_get_temp_dir() . '/qr_decode_' . uniqid();
            mkdir($tempDir, 0755, true);

            $imagePrefix = $tempDir . '/page';
            $command = sprintf(
                'pdftoppm -png -r 300 -l 1 %s %s',
                escapeshellarg($pdfPath),
                escapeshellarg($imagePrefix)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Failed to convert PDF to image');
            }

            $images = glob($tempDir . '/page-*.png');
            if (empty($images)) {
                return null;
            }

            // Use zbarimg to decode QR codes
            $decoded = null;
            foreach ($images as $imagePath) {
                $decoded = $this->decodeQrFromImage($imagePath);
                if ($decoded) {
                    break;
                }
            }

            // Cleanup
            array_map('unlink', glob($tempDir . '/*'));
            @rmdir($tempDir);

            return $decoded;
        } catch (\Exception $e) {
            Log::warning('Failed to decode QR from PDF', [
                'pdf' => $pdfPath,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Decode QR code from an image file.
     */
    public function decodeQrFromImage(string $imagePath): ?array
    {
        // Try using zbarimg if available
        $command = sprintf('zbarimg -q --raw %s 2>/dev/null', escapeshellarg($imagePath));
        $output = shell_exec($command);

        if (empty($output)) {
            return null;
        }

        $output = trim($output);

        // Try to parse as JSON (our embedded data format)
        $decoded = json_decode($output, true);
        if ($decoded !== null) {
            return $decoded;
        }

        // If not JSON, it might be just a URL
        if (filter_var($output, FILTER_VALIDATE_URL)) {
            return [
                'url' => $output,
                'type' => 'url_only',
            ];
        }

        return [
            'raw' => $output,
            'type' => 'unknown',
        ];
    }

    /**
     * Calculate QR code position on page.
     */
    protected function calculateQrPosition(
        string $position,
        float $pageWidth,
        float $pageHeight,
        float $qrSize,
        float $margin
    ): array {
        return match ($position) {
            'top-left' => [
                'x' => $margin,
                'y' => $margin,
            ],
            'top-right' => [
                'x' => $pageWidth - $qrSize - $margin,
                'y' => $margin,
            ],
            'top-center' => [
                'x' => ($pageWidth - $qrSize) / 2,
                'y' => $margin,
            ],
            'bottom-left' => [
                'x' => $margin,
                'y' => $pageHeight - $qrSize - $margin,
            ],
            'bottom-right' => [
                'x' => $pageWidth - $qrSize - $margin,
                'y' => $pageHeight - $qrSize - $margin,
            ],
            'bottom-center' => [
                'x' => ($pageWidth - $qrSize) / 2,
                'y' => $pageHeight - $qrSize - $margin,
            ],
            'center' => [
                'x' => ($pageWidth - $qrSize) / 2,
                'y' => ($pageHeight - $qrSize) / 2,
            ],
            default => [
                'x' => $pageWidth - $qrSize - $margin,
                'y' => $pageHeight - $qrSize - $margin,
            ],
        };
    }

    /**
     * Generate output path for QR-embedded PDF.
     */
    protected function generateOutputPath(string $inputPath): string
    {
        $pathInfo = pathinfo($inputPath);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_qr.' . $pathInfo['extension'];
    }

    /**
     * Add QR watermark to a document with fingerprint.
     */
    public function addQrWatermark(
        string $pdfPath,
        DocumentFingerprint $fingerprint,
        array $options = []
    ): string {
        // Generate QR code
        $qrPath = $this->generateVerificationQr($fingerprint, [
            'size' => $options['qr_size'] ?? 200,
            'url_only' => $options['url_only'] ?? true,
        ]);

        try {
            // Embed in PDF
            $outputPath = $this->embedQrInPdf($pdfPath, $qrPath, [
                'position' => $options['position'] ?? 'bottom-right',
                'page' => $options['page'] ?? 'first',
                'size' => $options['size'] ?? 20,
                'opacity' => $options['opacity'] ?? 0.9,
                'margin' => $options['margin'] ?? 10,
                'label' => $options['label'] ?? 'Scan to verify',
                'output_path' => $options['output_path'] ?? null,
            ]);

            return $outputPath;
        } finally {
            // Cleanup temp QR image
            @unlink($qrPath);
        }
    }

    /**
     * Verify document using decoded QR data.
     */
    public function verifyFromQr(string $pdfPath): array
    {
        $qrData = $this->decodeQrFromPdf($pdfPath);

        if (!$qrData) {
            return [
                'valid' => false,
                'status' => 'no_qr',
                'message' => 'No QR code found in document.',
            ];
        }

        // If we have full embedded data
        if (isset($qrData['data']) && isset($qrData['sig'])) {
            return $this->fingerprintService->verifyQrData($qrData);
        }

        // If we only have URL
        if (isset($qrData['url'])) {
            // Extract token from URL
            if (preg_match('/\/verify\/([a-zA-Z0-9]+)$/', $qrData['url'], $matches)) {
                return $this->fingerprintService->verifyDocument($matches[1]);
            }
        }

        return [
            'valid' => false,
            'status' => 'invalid_qr',
            'message' => 'QR code does not contain valid verification data.',
            'qr_data' => $qrData,
        ];
    }
}
