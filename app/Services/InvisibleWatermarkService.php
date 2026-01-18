<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use setasign\Fpdi\Tcpdf\Fpdi;

class InvisibleWatermarkService
{
    /**
     * Embed invisible watermark data in a PDF.
     */
    public function embedInvisible(string $pdfPath, array $data, array $options = []): string
    {
        $outputPath = $options['output_path'] ?? $this->generateOutputPath($pdfPath);

        try {
            // Use multiple techniques for redundancy
            $tempPath = $pdfPath;

            // 1. Embed in PDF metadata
            if ($options['embed_metadata'] ?? true) {
                $tempPath = $this->embedInMetadata($tempPath, $data);
            }

            // 2. Embed invisible text layer
            if ($options['embed_invisible_text'] ?? true) {
                $tempPath = $this->embedInvisibleText($tempPath, $data, $outputPath);
            } else {
                copy($tempPath, $outputPath);
            }

            // 3. Embed in document structure
            if ($options['embed_structure'] ?? true) {
                $this->embedInStructure($outputPath, $data);
            }

            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Failed to embed invisible watermark', [
                'pdf' => $pdfPath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Extract invisible watermark data from a PDF.
     */
    public function extractInvisible(string $pdfPath): ?array
    {
        $data = null;

        // Try metadata extraction first
        $data = $this->extractFromMetadata($pdfPath);
        if ($data) {
            return $data;
        }

        // Try structure extraction
        $data = $this->extractFromStructure($pdfPath);
        if ($data) {
            return $data;
        }

        // Try invisible text extraction
        $data = $this->extractInvisibleText($pdfPath);
        if ($data) {
            return $data;
        }

        return null;
    }

    /**
     * Verify invisible watermark matches expected data.
     */
    public function verifyInvisible(string $pdfPath, string $expectedHash): bool
    {
        $extracted = $this->extractInvisible($pdfPath);

        if (!$extracted || !isset($extracted['hash'])) {
            return false;
        }

        return hash_equals($expectedHash, $extracted['hash']);
    }

    /**
     * Embed data in PDF metadata using EXIF/XMP.
     */
    protected function embedInMetadata(string $pdfPath, array $data): string
    {
        $outputPath = sys_get_temp_dir() . '/meta_' . uniqid() . '.pdf';

        // Encrypt the data
        $encryptedData = Crypt::encryptString(json_encode($data));

        // Use exiftool to embed metadata
        $metadata = [
            'Subject' => 'Protected Document',
            'Keywords' => 'watermarked,verified',
            'Producer' => 'WM-' . ($data['marker'] ?? substr(md5(json_encode($data)), 0, 12)),
            'Custom' => base64_encode($encryptedData),
        ];

        // Create an XMP sidecar or modify directly
        $command = sprintf(
            'exiftool -overwrite_original -Subject=%s -Keywords=%s -Producer=%s %s -o %s 2>/dev/null',
            escapeshellarg($metadata['Subject']),
            escapeshellarg($metadata['Keywords']),
            escapeshellarg($metadata['Producer']),
            escapeshellarg($pdfPath),
            escapeshellarg($outputPath)
        );

        $result = Process::timeout(60)->run($command);

        // If exiftool not available, copy and modify directly
        if (!$result->successful() || !file_exists($outputPath)) {
            copy($pdfPath, $outputPath);
            $this->modifyPdfMetadataDirect($outputPath, $metadata);
        }

        return $outputPath;
    }

    /**
     * Directly modify PDF metadata in the file.
     */
    protected function modifyPdfMetadataDirect(string $pdfPath, array $metadata): void
    {
        $content = file_get_contents($pdfPath);

        // Look for existing info dictionary or create one
        $infoPattern = '/\/Info\s+(\d+)\s+\d+\s+R/';

        if (!preg_match($infoPattern, $content)) {
            // We'll rely on the structure embedding instead
            return;
        }

        // The producer field is our primary marker
        if (isset($metadata['Producer']) && preg_match('/\/Producer\s*\([^)]*\)/', $content)) {
            $content = preg_replace(
                '/\/Producer\s*\([^)]*\)/',
                '/Producer (' . $metadata['Producer'] . ')',
                $content
            );
            file_put_contents($pdfPath, $content);
        }
    }

    /**
     * Embed invisible text in the PDF.
     */
    protected function embedInvisibleText(string $inputPath, array $data, string $outputPath): string
    {
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($inputPath);

        // Prepare invisible text content
        $invisibleText = $this->generateInvisibleTextContent($data);

        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

            // Add invisible text on first page
            if ($i === 1) {
                // White text on white background (invisible)
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('helvetica', '', 1); // Very small font
                $pdf->SetXY(0, 0);

                // Add zero-width or very small text
                $pdf->Cell(1, 1, $invisibleText, 0, 0, 'L', false);

                // Also add as "invisible" layer using transparency
                $pdf->SetAlpha(0.01);
                $pdf->SetFont('helvetica', '', 4);
                $pdf->SetXY(5, $size['height'] - 5);
                $pdf->Write(1, 'WM:' . base64_encode(json_encode(['m' => $data['marker'] ?? ''])));
                $pdf->SetAlpha(1);
            }
        }

        $pdf->Output($outputPath, 'F');
        return $outputPath;
    }

    /**
     * Generate invisible text content with encoded data.
     */
    protected function generateInvisibleTextContent(array $data): string
    {
        // Use zero-width characters to encode data
        $binary = $this->dataToBinary($data);
        $encoded = '';

        // Zero-width space (U+200B), Zero-width non-joiner (U+200C), Zero-width joiner (U+200D)
        $zero = "\u{200B}"; // 0
        $one = "\u{200C}";  // 1

        foreach (str_split($binary) as $bit) {
            $encoded .= $bit === '0' ? $zero : $one;
        }

        return $encoded;
    }

    /**
     * Convert data to binary string.
     */
    protected function dataToBinary(array $data): string
    {
        $json = json_encode(['m' => $data['marker'] ?? '', 'h' => substr($data['hash'] ?? '', 0, 8)]);
        $binary = '';

        foreach (str_split($json) as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        return $binary;
    }

    /**
     * Embed data in PDF document structure.
     */
    protected function embedInStructure(string $pdfPath, array $data): void
    {
        $content = file_get_contents($pdfPath);

        // Create a custom dictionary entry
        $marker = $data['marker'] ?? substr(md5(json_encode($data)), 0, 12);
        $customEntry = "/WatermarkMarker (" . strtoupper($marker) . ")";

        // Find the document catalog and add our entry
        if (preg_match('/(\d+\s+\d+\s+obj\s*<<[^>]*\/Type\s*\/Catalog)/', $content, $matches)) {
            $content = str_replace(
                $matches[1],
                $matches[1] . "\n" . $customEntry,
                $content
            );
            file_put_contents($pdfPath, $content);
        }
    }

    /**
     * Extract watermark from metadata.
     */
    protected function extractFromMetadata(string $pdfPath): ?array
    {
        // Try exiftool first
        $command = sprintf('exiftool -Producer -j %s 2>/dev/null', escapeshellarg($pdfPath));
        $result = Process::timeout(30)->run($command);

        if ($result->successful()) {
            $output = json_decode($result->output(), true);
            if (!empty($output[0]['Producer']) && str_starts_with($output[0]['Producer'], 'WM-')) {
                return [
                    'marker' => substr($output[0]['Producer'], 3),
                    'source' => 'metadata',
                ];
            }
        }

        // Direct PDF parsing
        $content = file_get_contents($pdfPath);
        if (preg_match('/\/Producer\s*\(WM-([A-Z0-9]+)\)/', $content, $matches)) {
            return [
                'marker' => $matches[1],
                'source' => 'metadata_direct',
            ];
        }

        return null;
    }

    /**
     * Extract watermark from PDF structure.
     */
    protected function extractFromStructure(string $pdfPath): ?array
    {
        $content = file_get_contents($pdfPath);

        if (preg_match('/\/WatermarkMarker\s*\(([A-Z0-9]+)\)/', $content, $matches)) {
            return [
                'marker' => $matches[1],
                'source' => 'structure',
            ];
        }

        return null;
    }

    /**
     * Extract invisible text watermark.
     */
    protected function extractInvisibleText(string $pdfPath): ?array
    {
        // Use pdftotext to extract all text including hidden
        $command = sprintf('pdftotext -raw %s - 2>/dev/null', escapeshellarg($pdfPath));
        $result = Process::timeout(30)->run($command);

        if (!$result->successful()) {
            return null;
        }

        $text = $result->output();

        // Look for our marker pattern
        if (preg_match('/WM:([A-Za-z0-9+\/=]+)/', $text, $matches)) {
            $decoded = json_decode(base64_decode($matches[1]), true);
            if ($decoded && isset($decoded['m'])) {
                return [
                    'marker' => $decoded['m'],
                    'source' => 'invisible_text',
                ];
            }
        }

        // Try to decode zero-width characters
        $marker = $this->decodeZeroWidthChars($text);
        if ($marker) {
            return [
                'marker' => $marker,
                'source' => 'zero_width',
            ];
        }

        return null;
    }

    /**
     * Decode zero-width character encoding.
     */
    protected function decodeZeroWidthChars(string $text): ?string
    {
        $zero = "\u{200B}";
        $one = "\u{200C}";

        // Check if we have zero-width chars
        if (strpos($text, $zero) === false && strpos($text, $one) === false) {
            return null;
        }

        $binary = '';
        foreach (mb_str_split($text) as $char) {
            if ($char === $zero) {
                $binary .= '0';
            } elseif ($char === $one) {
                $binary .= '1';
            }
        }

        if (strlen($binary) < 8) {
            return null;
        }

        // Convert binary back to text
        $decoded = '';
        foreach (str_split($binary, 8) as $byte) {
            if (strlen($byte) === 8) {
                $decoded .= chr(bindec($byte));
            }
        }

        $json = json_decode($decoded, true);
        if ($json && isset($json['m'])) {
            return $json['m'];
        }

        return null;
    }

    /**
     * Generate output path.
     */
    protected function generateOutputPath(string $inputPath): string
    {
        $pathInfo = pathinfo($inputPath);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_invisible.' . $pathInfo['extension'];
    }

    /**
     * Check if a PDF has invisible watermark.
     */
    public function hasInvisibleWatermark(string $pdfPath): bool
    {
        return $this->extractInvisible($pdfPath) !== null;
    }

    /**
     * Get the marker from invisible watermark.
     */
    public function getMarker(string $pdfPath): ?string
    {
        $data = $this->extractInvisible($pdfPath);
        return $data['marker'] ?? null;
    }

    /**
     * Create comprehensive invisible watermark with all techniques.
     */
    public function createComprehensive(string $pdfPath, string $marker, string $hash, array $options = []): string
    {
        return $this->embedInvisible($pdfPath, [
            'marker' => $marker,
            'hash' => $hash,
            'timestamp' => time(),
        ], $options);
    }
}
