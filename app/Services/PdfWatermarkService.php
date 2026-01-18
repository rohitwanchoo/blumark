<?php

namespace App\Services;

use App\Models\DocumentFingerprint;
use Exception;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfWatermarkService
{
    protected ?InvisibleWatermarkService $invisibleService = null;
    protected ?DocumentFingerprintService $fingerprintService = null;

    /**
     * Set the invisible watermark service for multi-layer watermarking.
     */
    public function setInvisibleService(InvisibleWatermarkService $service): self
    {
        $this->invisibleService = $service;
        return $this;
    }

    /**
     * Set the fingerprint service for multi-layer watermarking.
     */
    public function setFingerprintService(DocumentFingerprintService $service): self
    {
        $this->fingerprintService = $service;
        return $this;
    }

    /**
     * Apply multi-layer watermark with comprehensive protection.
     *
     * Layers:
     * 1. Background layer - Subtle repeating pattern
     * 2. Main visible watermark - Primary watermark text
     * 3. Invisible metadata layer - Encoded tracking data
     * 4. Edge/margin watermarks - Additional protection
     *
     * @param string $inputPath Full path to the input PDF
     * @param string $outputPath Full path for the output PDF
     * @param array $settings Watermark settings
     * @param DocumentFingerprint|null $fingerprint Optional fingerprint for tracking
     * @return array Result with page_count and layers applied
     */
    public function applyMultiLayerWatermark(
        string $inputPath,
        string $outputPath,
        array $settings,
        ?DocumentFingerprint $fingerprint = null
    ): array {
        $this->validateInputFile($inputPath);

        $tempDir = sys_get_temp_dir() . '/multilayer_' . uniqid();
        mkdir($tempDir, 0755, true);

        $layersApplied = [];

        try {
            // Convert PDF to images for processing
            $cmd = sprintf(
                'pdftoppm -png -r 200 %s %s/page 2>&1',
                escapeshellarg($inputPath),
                escapeshellarg($tempDir)
            );
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new Exception('Failed to convert PDF to images');
            }

            $images = glob($tempDir . '/page-*.png');
            natsort($images);
            $images = array_values($images);

            if (empty($images)) {
                throw new Exception('No pages were extracted from the PDF.');
            }

            $pageCount = count($images);
            $this->validatePageCount($pageCount);

            // Create new PDF with multi-layer watermarks
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('PDF Watermark Platform');
            $pdf->SetAuthor('PDF Watermark Platform');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false);

            // Add fingerprint marker to PDF producer
            if ($fingerprint) {
                $pdf->SetCreator('WM-' . $fingerprint->unique_marker);
            }

            foreach ($images as $index => $imagePath) {
                $imageSize = getimagesize($imagePath);
                if (!$imageSize) {
                    continue;
                }

                $pageWidth = ($imageSize[0] / 200) * 25.4;
                $pageHeight = ($imageSize[1] / 200) * 25.4;
                $orientation = $pageWidth > $pageHeight ? 'L' : 'P';

                $pdf->AddPage($orientation, [$pageWidth, $pageHeight]);
                $pdf->Image($imagePath, 0, 0, $pageWidth, $pageHeight, 'PNG');

                // Layer 1: Background pattern (if enabled)
                if ($settings['enable_background_layer'] ?? false) {
                    $this->applyBackgroundLayer($pdf, $settings, $pageWidth, $pageHeight);
                    $layersApplied['background'] = true;
                }

                // Layer 2: Main visible watermark
                $this->applyWatermarkToPage($pdf, $settings, $pageWidth, $pageHeight);
                $layersApplied['main'] = true;

                // Layer 3: Edge/margin watermarks (if enabled)
                if ($settings['enable_edge_watermarks'] ?? false) {
                    $this->applyEdgeWatermarks($pdf, $settings, $pageWidth, $pageHeight, $fingerprint);
                    $layersApplied['edge'] = true;
                }

                // Layer 4: OCR-resistant elements (if enabled)
                if ($settings['ocr_resistant'] ?? false) {
                    $this->applyOcrResistantElements($pdf, $settings, $pageWidth, $pageHeight);
                    $layersApplied['ocr_resistant'] = true;
                }
            }

            // Save the multi-layer watermarked PDF
            $pdf->Output($outputPath, 'F');

            // Layer 5: Invisible metadata (applied after PDF generation)
            if (($settings['enable_invisible_layer'] ?? true) && $this->invisibleService && $fingerprint) {
                $this->invisibleService->embedInvisible($outputPath, [
                    'marker' => $fingerprint->unique_marker,
                    'hash' => $fingerprint->fingerprint_hash,
                ], ['output_path' => $outputPath]);
                $layersApplied['invisible'] = true;
            }

            return [
                'page_count' => $pageCount,
                'layers_applied' => $layersApplied,
            ];

        } finally {
            // Cleanup temp files
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                @unlink($file);
            }
            @rmdir($tempDir);
        }
    }

    /**
     * Apply subtle background pattern layer.
     */
    protected function applyBackgroundLayer(\TCPDF $pdf, array $settings, float $pageWidth, float $pageHeight): void
    {
        $patternText = $settings['background_pattern_text'] ?? 'CONFIDENTIAL';
        $patternOpacity = ($settings['background_opacity'] ?? 5) / 100;
        $color = $this->hexToRgb($settings['background_color'] ?? '#cccccc');

        $pdf->SetAlpha($patternOpacity);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor($color['r'], $color['g'], $color['b']);

        // Create a subtle repeating pattern
        $spacingX = 40;
        $spacingY = 25;

        for ($y = 10; $y < $pageHeight; $y += $spacingY) {
            for ($x = 10; $x < $pageWidth; $x += $spacingX) {
                $pdf->StartTransform();
                $pdf->Rotate(30, $x, $y);
                $pdf->Text($x, $y, $patternText);
                $pdf->StopTransform();
            }
        }

        $pdf->SetAlpha(1);
    }

    /**
     * Apply edge/margin watermarks for additional protection.
     */
    protected function applyEdgeWatermarks(
        \TCPDF $pdf,
        array $settings,
        float $pageWidth,
        float $pageHeight,
        ?DocumentFingerprint $fingerprint = null
    ): void {
        $edgeText = $fingerprint
            ? "ID: {$fingerprint->unique_marker}"
            : ($settings['edge_text'] ?? 'PROTECTED DOCUMENT');

        $opacity = ($settings['edge_opacity'] ?? 15) / 100;
        $color = $this->hexToRgb($settings['edge_color'] ?? '#999999');

        $pdf->SetAlpha($opacity);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetTextColor($color['r'], $color['g'], $color['b']);

        // Top edge
        $pdf->Text(5, 3, $edgeText);
        $pdf->Text($pageWidth - $pdf->GetStringWidth($edgeText) - 5, 3, $edgeText);

        // Bottom edge
        $pdf->Text(5, $pageHeight - 5, $edgeText);
        $pdf->Text($pageWidth - $pdf->GetStringWidth($edgeText) - 5, $pageHeight - 5, $edgeText);

        // Left edge (rotated)
        $pdf->StartTransform();
        $pdf->Rotate(90, 3, $pageHeight / 2);
        $pdf->Text(3, $pageHeight / 2, $edgeText);
        $pdf->StopTransform();

        // Right edge (rotated)
        $pdf->StartTransform();
        $pdf->Rotate(-90, $pageWidth - 3, $pageHeight / 2);
        $pdf->Text($pageWidth - 3, $pageHeight / 2, $edgeText);
        $pdf->StopTransform();

        $pdf->SetAlpha(1);
    }

    /**
     * Apply OCR-resistant elements to confuse text extraction.
     */
    protected function applyOcrResistantElements(\TCPDF $pdf, array $settings, float $pageWidth, float $pageHeight): void
    {
        $opacity = ($settings['ocr_resistant_opacity'] ?? 3) / 100;

        $pdf->SetAlpha($opacity);

        // Add noise characters at random positions
        $noiseChars = ['|', '/', '\\', '-', '_', '.', ','];
        $pdf->SetFont('helvetica', '', 2);
        $pdf->SetTextColor(200, 200, 200);

        for ($i = 0; $i < 50; $i++) {
            $x = mt_rand(0, (int) $pageWidth);
            $y = mt_rand(0, (int) $pageHeight);
            $char = $noiseChars[array_rand($noiseChars)];
            $pdf->Text($x, $y, $char);
        }

        // Add invisible decoy text
        $pdf->SetTextColor(255, 255, 255); // White on white
        $pdf->SetFont('helvetica', '', 1);
        $decoyText = 'WATERMARK_DECOY_' . bin2hex(random_bytes(4));
        $pdf->Text(0, 0, $decoyText);

        $pdf->SetAlpha(1);
    }

    /**
     * Apply watermark with OCR-resistant font rendering.
     */
    public function watermarkOcrResistant(string $inputPath, string $outputPath, array $settings): array
    {
        // Enable OCR-resistant mode
        $settings['ocr_resistant'] = true;

        // Fragment the watermark text with special rendering
        $settings['fragment_text'] = true;

        return $this->watermarkIsoLender($inputPath, $outputPath, $settings);
    }

    /**
     * Apply ISO/Lender watermark in a 9-position grid layout.
     *
     * @param string $inputPath Full path to the input PDF
     * @param string $outputPath Full path for the output PDF
     * @param array $settings Watermark settings (iso, lender, font_size, color, opacity)
     * @return array Result with page_count
     * @throws Exception
     */
    public function watermarkIsoLender(string $inputPath, string $outputPath, array $settings): array
    {
        $this->validateInputFile($inputPath);

        // Use image-based rendering to avoid FPDI template clipping issues
        $tempDir = sys_get_temp_dir() . '/watermark_' . uniqid();
        mkdir($tempDir, 0755, true);

        try {
            // Convert PDF to PNG images using pdftoppm (200 DPI for quality)
            $cmd = sprintf(
                'pdftoppm -png -r 200 %s %s/page 2>&1',
                escapeshellarg($inputPath),
                escapeshellarg($tempDir)
            );
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new Exception('Failed to convert PDF to images: ' . implode("\n", $output));
            }

            // Get all generated images sorted by page number
            $images = glob($tempDir . '/page-*.png');
            natsort($images);
            $images = array_values($images);

            if (empty($images)) {
                throw new Exception('No pages were extracted from the PDF.');
            }

            $pageCount = count($images);
            $this->validatePageCount($pageCount);

            // Apply watermark directly to images (makes it unextractable as text)
            foreach ($images as $imagePath) {
                $this->applyWatermarkToImage($imagePath, $settings);
            }

            // Create new PDF from watermarked images
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('PDF Watermark Platform');
            $pdf->SetAuthor('PDF Watermark Platform');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false);

            foreach ($images as $imagePath) {
                // Get image dimensions
                $imageSize = getimagesize($imagePath);
                if (!$imageSize) {
                    continue;
                }

                $imgWidth = $imageSize[0];
                $imgHeight = $imageSize[1];

                // Calculate page size in mm (pdftoppm uses 200 DPI)
                // 200 DPI = 200 pixels per inch, 1 inch = 25.4mm
                $pageWidth = ($imgWidth / 200) * 25.4;
                $pageHeight = ($imgHeight / 200) * 25.4;

                // Determine orientation
                $orientation = $pageWidth > $pageHeight ? 'L' : 'P';

                $pdf->AddPage($orientation, [$pageWidth, $pageHeight]);

                // Place the watermarked image (watermark is now part of image, not text layer)
                $pdf->Image($imagePath, 0, 0, $pageWidth, $pageHeight, 'PNG');
            }

            // Save the watermarked PDF
            $pdf->Output($outputPath, 'F');

            return ['page_count' => $pageCount];

        } finally {
            // Cleanup temp files
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                @unlink($file);
            }
            @rmdir($tempDir);
        }
    }

    /**
     * Apply watermark directly to an image file using GD.
     * This renders the watermark as pixels, making it invisible to text extraction.
     */
    protected function applyWatermarkToImage(string $imagePath, array $settings): void
    {
        $iso = $settings['iso'] ?? '';
        $lender = $settings['lender'] ?? '';
        $watermarkText = "ISO: {$iso} | Lender: {$lender}";

        $opacity = ($settings['opacity'] ?? 33) / 100;
        $color = $this->hexToRgb($settings['color'] ?? '#878787');

        // Load the image
        $image = imagecreatefrompng($imagePath);
        if (!$image) {
            return;
        }

        $imgWidth = imagesx($image);
        $imgHeight = imagesy($image);

        // Enable alpha blending
        imagealphablending($image, true);
        imagesavealpha($image, true);

        // Create watermark color with transparency
        // GD uses 0-127 for alpha (0 = opaque, 127 = transparent)
        $alpha = (int) (127 - ($opacity * 127));
        $textColor = imagecolorallocatealpha($image, $color['r'], $color['g'], $color['b'], $alpha);

        // Calculate font size based on image dimensions
        // Use a TrueType font if available, otherwise use built-in font
        $fontFile = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
        if (!file_exists($fontFile)) {
            $fontFile = '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf';
        }

        if (file_exists($fontFile)) {
            // Use TrueType font for better quality
            $fontSize = min($imgWidth, $imgHeight) * 0.05; // 5% of smaller dimension
            $fontSize = max(20, min($fontSize, 100)); // Clamp between 20-100

            // Get text bounding box
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $watermarkText);
            $textWidth = abs($bbox[2] - $bbox[0]);
            $textHeight = abs($bbox[7] - $bbox[1]);

            // Calculate center position
            $centerX = $imgWidth / 2;
            $centerY = $imgHeight / 2;

            // Create a temporary image for the rotated text
            $diagonal = sqrt($textWidth * $textWidth + $textHeight * $textHeight) * 1.5;
            $tempImg = imagecreatetruecolor((int) $diagonal, (int) $diagonal);
            imagealphablending($tempImg, true);
            imagesavealpha($tempImg, true);
            $transparent = imagecolorallocatealpha($tempImg, 0, 0, 0, 127);
            imagefill($tempImg, 0, 0, $transparent);

            // Draw text on temp image (centered)
            $tempCenterX = $diagonal / 2 - $textWidth / 2;
            $tempCenterY = $diagonal / 2 + $textHeight / 2;
            imagettftext($tempImg, $fontSize, 0, (int) $tempCenterX, (int) $tempCenterY, $textColor, $fontFile, $watermarkText);

            // Rotate the temp image
            $rotated = imagerotate($tempImg, 45, $transparent);
            imagedestroy($tempImg);

            if ($rotated) {
                $rotWidth = imagesx($rotated);
                $rotHeight = imagesy($rotated);

                // Copy rotated watermark to main image (centered)
                $destX = (int) (($imgWidth - $rotWidth) / 2);
                $destY = (int) (($imgHeight - $rotHeight) / 2);

                imagecopy($image, $rotated, $destX, $destY, 0, 0, $rotWidth, $rotHeight);
                imagedestroy($rotated);
            }
        } else {
            // Fallback to built-in font (less pretty but works)
            $font = 5; // Largest built-in font
            $charWidth = imagefontwidth($font);
            $charHeight = imagefontheight($font);
            $textWidth = strlen($watermarkText) * $charWidth;

            $x = (int) (($imgWidth - $textWidth) / 2);
            $y = (int) ($imgHeight / 2);

            imagestring($image, $font, $x, $y, $watermarkText, $textColor);
        }

        // Save the watermarked image
        imagepng($image, $imagePath);
        imagedestroy($image);
    }

    /**
     * Apply watermark to a single page using TCPDF (no FPDI templates).
     * The watermark spans the full page diagonal from lower-left to upper-right.
     */
    protected function applyWatermarkToPage(\TCPDF $pdf, array $settings, float $pageWidth, float $pageHeight): void
    {
        $iso = $settings['iso'] ?? '';
        $lender = $settings['lender'] ?? '';
        $watermarkText = "ISO: {$iso} | Lender: {$lender}";

        $opacity = ($settings['opacity'] ?? 33) / 100;
        $color = $this->hexToRgb($settings['color'] ?? '#878787');

        // To keep text centered on the page, we need the text width to be less than
        // the page width so that when positioned at (centerX - textWidth/2), the X
        // coordinate stays positive (within page bounds)
        // Max text width = pageWidth - 20mm margin = allows centered text to fit
        $maxTextWidth = $pageWidth - 20;

        $baseFontSize = 24;
        $pdf->SetFont('helvetica', 'B', $baseFontSize);
        $baseTextWidth = $pdf->GetStringWidth($watermarkText);

        $fontSize = ($maxTextWidth / $baseTextWidth) * $baseFontSize;
        $fontSize = max(16, min($fontSize, 48));

        // Center of page
        $centerX = $pageWidth / 2;
        $centerY = $pageHeight / 2;

        // Set styling
        $pdf->SetAlpha($opacity);
        $pdf->SetFont('helvetica', 'B', $fontSize);
        $pdf->SetTextColor($color['r'], $color['g'], $color['b']);

        $textWidth = $pdf->GetStringWidth($watermarkText);
        $textHeight = $fontSize * 0.35; // Approximate text height in mm

        // Position text so its CENTER is exactly at page CENTER
        // This ensures when rotated around the text center, it stays visually centered
        $textX = $centerX - ($textWidth / 2);
        $textY = $centerY - ($textHeight / 2);

        // Apply diagonal watermark using TCPDF's rotation
        $pdf->StartTransform();

        // Rotate 45° around the PAGE CENTER (not text corner)
        // The text center is at (centerX, centerY), so rotating around page center
        // keeps the text centered on the page
        $pdf->Rotate(45, $centerX, $centerY);

        $pdf->Text($textX, $textY, $watermarkText);
        $pdf->StopTransform();

        $pdf->SetAlpha(1);
    }

    /**
     * Preprocess PDF using Ghostscript to ensure compatibility with FPDI.
     * This decompresses and re-encodes the PDF to work around FPDI limitations.
     *
     * @param string $inputPath Path to the original PDF
     * @return string Path to the processed PDF (may be same as input if no processing needed)
     */
    protected function preprocessPdf(string $inputPath): string
    {
        $tempPath = sys_get_temp_dir() . '/pdf_preprocess_' . uniqid() . '.pdf';

        // Use full path to Ghostscript to ensure it works in web server context
        $gsPath = '/usr/bin/gs';

        // Use Ghostscript to decompress and re-encode the PDF
        $command = sprintf(
            '%s -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=%s %s 2>&1',
            escapeshellarg($gsPath),
            escapeshellarg($tempPath),
            escapeshellarg($inputPath)
        );

        exec($command, $output, $returnCode);

        // Log for debugging
        \Log::debug('PDF preprocessing', [
            'input' => $inputPath,
            'output' => $tempPath,
            'return_code' => $returnCode,
            'output' => implode("\n", $output),
            'file_exists' => file_exists($tempPath),
            'file_size' => file_exists($tempPath) ? filesize($tempPath) : 0,
        ]);

        if ($returnCode !== 0 || !file_exists($tempPath) || filesize($tempPath) === 0) {
            // If Ghostscript fails, log and try using the original file
            \Log::warning('Ghostscript preprocessing failed', [
                'input' => $inputPath,
                'return_code' => $returnCode,
                'output' => implode("\n", $output),
            ]);
            @unlink($tempPath);
            return $inputPath;
        }

        return $tempPath;
    }

    /**
     * Flatten a PDF by converting each page to an image and back to PDF.
     * This makes text unselectable and watermarks permanent.
     *
     * @param string $pdfPath Path to the PDF to flatten (will be overwritten)
     * @throws Exception
     */
    protected function flattenPdf(string $pdfPath): void
    {
        $tempDir = sys_get_temp_dir() . '/pdf_flatten_' . uniqid();
        mkdir($tempDir, 0755, true);

        try {
            // Convert PDF to images using pdftoppm (300 DPI for quality)
            $command = sprintf(
                'pdftoppm -png -r 200 %s %s/page',
                escapeshellarg($pdfPath),
                escapeshellarg($tempDir)
            );

            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode !== 0) {
                throw new Exception("Failed to convert PDF to images: " . implode("\n", $output));
            }

            // Get all generated images
            $images = glob($tempDir . '/page-*.png');
            sort($images, SORT_NATURAL);

            if (empty($images)) {
                throw new Exception("No images generated from PDF");
            }

            // Create new PDF from images
            $pdf = new \TCPDF('P', 'pt', 'A4', true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false);

            foreach ($images as $imagePath) {
                // Get image dimensions
                $imageSize = getimagesize($imagePath);
                if (!$imageSize) {
                    continue;
                }

                $imgWidth = $imageSize[0];
                $imgHeight = $imageSize[1];

                // Calculate page size based on image aspect ratio (72 DPI conversion)
                // pdftoppm uses 200 DPI, so convert to points (72 DPI)
                $pageWidth = ($imgWidth / 200) * 72;
                $pageHeight = ($imgHeight / 200) * 72;

                // Determine orientation
                $orientation = $pageWidth > $pageHeight ? 'L' : 'P';

                $pdf->AddPage($orientation, [$pageWidth, $pageHeight]);
                $pdf->Image($imagePath, 0, 0, $pageWidth, $pageHeight, 'PNG');
            }

            // Save the flattened PDF
            $pdf->Output($pdfPath, 'F');

        } finally {
            // Cleanup temp files
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                @unlink($file);
            }
            @rmdir($tempDir);
        }
    }

    /**
     * Apply watermark directly to the page content stream.
     * This method bypasses FPDI template clipping issues by writing to page content directly.
     * Note: pageWidth and pageHeight are in mm (FPDI default unit).
     */
    protected function applyWatermarkDirect(Fpdi $pdf, array $settings, float $pageWidth, float $pageHeight): void
    {
        $iso = $settings['iso'] ?? '';
        $lender = $settings['lender'] ?? '';
        $watermarkText = "ISO: {$iso} | Lender: {$lender}";

        $opacity = ($settings['opacity'] ?? 33) / 100;
        $color = $this->hexToRgb($settings['color'] ?? '#878787');

        // Calculate font size to fit within page with good margins
        // Use 55% of the smaller dimension for comfortable fit when rotated
        $maxTextWidth = min($pageWidth, $pageHeight) * 0.55;

        // Start with a base font size and calculate text width
        $baseFontSize = 24;
        $pdf->SetFont('helvetica', 'B', $baseFontSize);
        $baseTextWidth = $pdf->GetStringWidth($watermarkText);

        // Calculate the font size to achieve target width
        $fontSize = ($maxTextWidth / $baseTextWidth) * $baseFontSize;

        // Clamp font size to reasonable bounds
        $fontSize = max(14, min($fontSize, 36));

        // Center of page
        $centerX = $pageWidth / 2;
        $centerY = $pageHeight / 2;

        // Convert mm to points for PDF operations (1mm = 2.83465 points)
        $k = $pdf->getScaleFactor();

        // Set graphics state for transparency
        $pdf->SetAlpha($opacity);

        // Set font and color
        $pdf->SetFont('helvetica', 'B', $fontSize);
        $pdf->SetTextColor($color['r'], $color['g'], $color['b']);

        // Get the actual text width after setting font
        $textWidth = $pdf->GetStringWidth($watermarkText);

        // Use TCPDF's transformation but ensure we're not inside a clipped region
        // by resetting the graphics state first
        $pdf->StartTransform();

        // Rotate around page center
        $pdf->Rotate(45, $centerX, $centerY);

        // Draw the text centered
        $textX = $centerX - ($textWidth / 2);
        $textY = $centerY;

        $pdf->Text($textX, $textY, $watermarkText);

        $pdf->StopTransform();

        // Reset alpha
        $pdf->SetAlpha(1);
    }

    /**
     * Apply a single diagonal text watermark across the center of each page.
     * Text is sized to fit diagonally across the page (left to right, bottom to top).
     * Note: pageWidth and pageHeight are in mm (FPDI default unit).
     * @deprecated Use applyWatermarkDirect instead
     */
    protected function applyGridWatermark(Fpdi $pdf, array $settings, float $pageWidth, float $pageHeight): void
    {
        $this->applyWatermarkDirect($pdf, $settings, $pageWidth, $pageHeight);
    }

    /**
     * Apply a text watermark to a PDF.
     *
     * @param string $inputPath Full path to the input PDF
     * @param string $outputPath Full path for the output PDF
     * @param array $settings Watermark settings
     * @return array Result with page_count
     * @throws Exception
     */
    public function watermarkText(string $inputPath, string $outputPath, array $settings): array
    {
        $this->validateInputFile($inputPath);

        $pdf = $this->createPdfInstance();

        try {
            $pageCount = $pdf->setSourceFile($inputPath);
        } catch (Exception $e) {
            throw new Exception("Failed to read PDF file: " . $e->getMessage());
        }

        $this->validatePageCount($pageCount);

        // Process each page
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

            $this->applyTextWatermark($pdf, $settings, $size['width'], $size['height']);
        }

        $this->savePdf($pdf, $outputPath);

        return ['page_count' => $pageCount];
    }

    /**
     * Apply an image watermark to a PDF.
     *
     * @param string $inputPath Full path to the input PDF
     * @param string $outputPath Full path for the output PDF
     * @param string $imagePath Full path to the watermark image
     * @param array $settings Watermark settings
     * @return array Result with page_count
     * @throws Exception
     */
    public function watermarkImage(string $inputPath, string $outputPath, string $imagePath, array $settings): array
    {
        $this->validateInputFile($inputPath);
        $this->validateImageFile($imagePath);

        $pdf = $this->createPdfInstance();

        try {
            $pageCount = $pdf->setSourceFile($inputPath);
        } catch (Exception $e) {
            throw new Exception("Failed to read PDF file: " . $e->getMessage());
        }

        $this->validatePageCount($pageCount);

        // Process each page
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

            $this->applyImageWatermark($pdf, $imagePath, $settings, $size['width'], $size['height']);
        }

        $this->savePdf($pdf, $outputPath);

        return ['page_count' => $pageCount];
    }

    /**
     * Create and configure the FPDI/TCPDF instance.
     */
    protected function createPdfInstance(): Fpdi
    {
        $pdf = new Fpdi();

        // Set document information
        $pdf->SetCreator('PDF Watermark Platform');
        $pdf->SetAuthor('PDF Watermark Platform');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins to 0
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);

        return $pdf;
    }

    /**
     * Apply text watermark to the current page.
     */
    protected function applyTextWatermark(Fpdi $pdf, array $settings, float $pageWidth, float $pageHeight): void
    {
        $text = $settings['text'] ?? 'WATERMARK';
        $fontSize = $settings['font_size'] ?? 48;
        $opacity = ($settings['opacity'] ?? 50) / 100;
        $color = $this->hexToRgb($settings['color'] ?? '#888888');
        $rotation = $settings['rotation'] ?? -45;
        $position = $settings['position'] ?? 'diagonal';

        // Set font and color with alpha
        $pdf->SetFont('helvetica', 'B', $fontSize);
        $pdf->SetTextColor($color['r'], $color['g'], $color['b']);
        $pdf->SetAlpha($opacity);

        switch ($position) {
            case 'center':
                $this->applyTextCenter($pdf, $text, $fontSize, $rotation, $pageWidth, $pageHeight);
                break;

            case 'tiled':
                $this->applyTextTiled($pdf, $text, $fontSize, $rotation, $pageWidth, $pageHeight, $settings);
                break;

            case 'diagonal':
            default:
                $this->applyTextDiagonal($pdf, $text, $fontSize, $rotation, $pageWidth, $pageHeight);
                break;
        }

        // Reset alpha
        $pdf->SetAlpha(1);
    }

    /**
     * Apply centered text watermark.
     */
    protected function applyTextCenter(Fpdi $pdf, string $text, int $fontSize, float $rotation, float $pageWidth, float $pageHeight): void
    {
        $textWidth = $pdf->GetStringWidth($text);
        $x = ($pageWidth - $textWidth) / 2;
        $y = $pageHeight / 2;

        // Start transformation
        $pdf->StartTransform();
        $pdf->Rotate($rotation, $x + $textWidth / 2, $y);
        $pdf->Text($x, $y, $text);
        $pdf->StopTransform();
    }

    /**
     * Apply diagonal text watermark.
     */
    protected function applyTextDiagonal(Fpdi $pdf, string $text, int $fontSize, float $rotation, float $pageWidth, float $pageHeight): void
    {
        $textWidth = $pdf->GetStringWidth($text);

        // Position text in center of page but rotated diagonally
        $centerX = $pageWidth / 2;
        $centerY = $pageHeight / 2;

        $pdf->StartTransform();
        $pdf->Rotate($rotation, $centerX, $centerY);
        $pdf->Text($centerX - $textWidth / 2, $centerY, $text);
        $pdf->StopTransform();
    }

    /**
     * Apply tiled text watermark.
     */
    protected function applyTextTiled(Fpdi $pdf, string $text, int $fontSize, float $rotation, float $pageWidth, float $pageHeight, array $settings): void
    {
        $textWidth = $pdf->GetStringWidth($text);
        $textHeight = $fontSize * 0.35; // Approximate text height in mm

        // Calculate spacing based on density
        $density = $settings['tile_density'] ?? 3;
        $spacingX = max($textWidth * 1.5, $pageWidth / max(1, $density));
        $spacingY = max($textHeight * 4, $pageHeight / max(1, $density));

        // Apply margin/offset
        $marginX = $settings['margin_x'] ?? 10;
        $marginY = $settings['margin_y'] ?? 10;

        // Create tiled pattern
        for ($y = $marginY; $y < $pageHeight; $y += $spacingY) {
            for ($x = $marginX; $x < $pageWidth; $x += $spacingX) {
                $pdf->StartTransform();
                $pdf->Rotate($rotation, $x + $textWidth / 2, $y);
                $pdf->Text($x, $y, $text);
                $pdf->StopTransform();
            }
        }
    }

    /**
     * Apply image watermark to the current page.
     */
    protected function applyImageWatermark(Fpdi $pdf, string $imagePath, array $settings, float $pageWidth, float $pageHeight): void
    {
        $opacity = ($settings['opacity'] ?? 50) / 100;
        $scale = ($settings['scale'] ?? 50) / 100;
        $position = $settings['position'] ?? 'center';
        $rotation = $settings['rotation'] ?? 0;

        // Get image dimensions
        $imageInfo = @getimagesize($imagePath);
        if (!$imageInfo) {
            return;
        }

        // Calculate scaled dimensions (assuming 72 DPI for conversion to mm)
        $imgWidthPx = $imageInfo[0];
        $imgHeightPx = $imageInfo[1];
        $imgWidthMm = ($imgWidthPx / 72) * 25.4 * $scale;
        $imgHeightMm = ($imgHeightPx / 72) * 25.4 * $scale;

        // Limit image size to page dimensions
        if ($imgWidthMm > $pageWidth * 0.8) {
            $ratio = ($pageWidth * 0.8) / $imgWidthMm;
            $imgWidthMm *= $ratio;
            $imgHeightMm *= $ratio;
        }
        if ($imgHeightMm > $pageHeight * 0.8) {
            $ratio = ($pageHeight * 0.8) / $imgHeightMm;
            $imgWidthMm *= $ratio;
            $imgHeightMm *= $ratio;
        }

        $pdf->SetAlpha($opacity);

        switch ($position) {
            case 'center':
                $this->applyImageCenter($pdf, $imagePath, $imgWidthMm, $imgHeightMm, $rotation, $pageWidth, $pageHeight);
                break;

            case 'tiled':
                $this->applyImageTiled($pdf, $imagePath, $imgWidthMm, $imgHeightMm, $rotation, $pageWidth, $pageHeight, $settings);
                break;

            case 'diagonal':
            default:
                $this->applyImageDiagonal($pdf, $imagePath, $imgWidthMm, $imgHeightMm, $rotation, $pageWidth, $pageHeight);
                break;
        }

        $pdf->SetAlpha(1);
    }

    /**
     * Apply centered image watermark.
     */
    protected function applyImageCenter(Fpdi $pdf, string $imagePath, float $imgWidth, float $imgHeight, float $rotation, float $pageWidth, float $pageHeight): void
    {
        $x = ($pageWidth - $imgWidth) / 2;
        $y = ($pageHeight - $imgHeight) / 2;

        if ($rotation != 0) {
            $pdf->StartTransform();
            $pdf->Rotate($rotation, $x + $imgWidth / 2, $y + $imgHeight / 2);
        }

        $pdf->Image($imagePath, $x, $y, $imgWidth, $imgHeight);

        if ($rotation != 0) {
            $pdf->StopTransform();
        }
    }

    /**
     * Apply diagonal image watermark (centered with rotation).
     */
    protected function applyImageDiagonal(Fpdi $pdf, string $imagePath, float $imgWidth, float $imgHeight, float $rotation, float $pageWidth, float $pageHeight): void
    {
        $x = ($pageWidth - $imgWidth) / 2;
        $y = ($pageHeight - $imgHeight) / 2;

        $actualRotation = $rotation != 0 ? $rotation : -45;

        $pdf->StartTransform();
        $pdf->Rotate($actualRotation, $x + $imgWidth / 2, $y + $imgHeight / 2);
        $pdf->Image($imagePath, $x, $y, $imgWidth, $imgHeight);
        $pdf->StopTransform();
    }

    /**
     * Apply tiled image watermark.
     */
    protected function applyImageTiled(Fpdi $pdf, string $imagePath, float $imgWidth, float $imgHeight, float $rotation, float $pageWidth, float $pageHeight, array $settings): void
    {
        $density = $settings['tile_density'] ?? 3;
        $spacingX = max($imgWidth * 1.5, $pageWidth / max(1, $density));
        $spacingY = max($imgHeight * 1.5, $pageHeight / max(1, $density));

        $marginX = $settings['margin_x'] ?? 10;
        $marginY = $settings['margin_y'] ?? 10;

        for ($y = $marginY; $y < $pageHeight; $y += $spacingY) {
            for ($x = $marginX; $x < $pageWidth; $x += $spacingX) {
                if ($rotation != 0) {
                    $pdf->StartTransform();
                    $pdf->Rotate($rotation, $x + $imgWidth / 2, $y + $imgHeight / 2);
                }

                $pdf->Image($imagePath, $x, $y, $imgWidth, $imgHeight);

                if ($rotation != 0) {
                    $pdf->StopTransform();
                }
            }
        }
    }

    /**
     * Save the PDF to the output path.
     */
    protected function savePdf(Fpdi $pdf, string $outputPath): void
    {
        // Ensure the directory exists
        $directory = dirname($outputPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->Output($outputPath, 'F');

        if (!file_exists($outputPath)) {
            throw new Exception("Failed to save the watermarked PDF.");
        }
    }

    /**
     * Validate the input file exists and is readable.
     */
    protected function validateInputFile(string $inputPath): void
    {
        if (!file_exists($inputPath)) {
            throw new Exception("Input PDF file does not exist: {$inputPath}");
        }

        if (!is_readable($inputPath)) {
            throw new Exception("Input PDF file is not readable: {$inputPath}");
        }
    }

    /**
     * Validate the image file exists and is readable.
     */
    protected function validateImageFile(string $imagePath): void
    {
        if (!file_exists($imagePath)) {
            throw new Exception("Watermark image file does not exist: {$imagePath}");
        }

        if (!is_readable($imagePath)) {
            throw new Exception("Watermark image file is not readable: {$imagePath}");
        }

        $allowedTypes = [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF];
        $imageType = @exif_imagetype($imagePath);

        if (!in_array($imageType, $allowedTypes)) {
            throw new Exception("Invalid image type. Allowed types: PNG, JPEG, GIF");
        }
    }

    /**
     * Validate the page count is within limits.
     */
    protected function validatePageCount(int $pageCount): void
    {
        $maxPages = config('watermark.processing.max_pages', 500);

        if ($pageCount > $maxPages) {
            throw new Exception("PDF exceeds maximum page limit of {$maxPages} pages.");
        }

        if ($pageCount < 1) {
            throw new Exception("PDF has no pages to process.");
        }
    }

    /**
     * Convert hex color to RGB array.
     */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Get the page count of a PDF without processing it.
     */
    public function getPageCount(string $inputPath): int
    {
        $this->validateInputFile($inputPath);

        $pdf = new Fpdi();

        try {
            return $pdf->setSourceFile($inputPath);
        } catch (Exception $e) {
            throw new Exception("Failed to read PDF file: " . $e->getMessage());
        }
    }
}
