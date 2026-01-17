<?php

namespace App\Services;

use Exception;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfWatermarkService
{
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

            // Create new PDF with watermarks using TCPDF (no FPDI = no clipping)
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

                // Place the image as background
                $pdf->Image($imagePath, 0, 0, $pageWidth, $pageHeight, 'PNG');

                // Apply watermark on top (no FPDI template = no clipping)
                $this->applyWatermarkToPage($pdf, $settings, $pageWidth, $pageHeight);
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
     * Apply watermark to a single page using TCPDF (no FPDI templates).
     */
    protected function applyWatermarkToPage(\TCPDF $pdf, array $settings, float $pageWidth, float $pageHeight): void
    {
        $iso = $settings['iso'] ?? '';
        $lender = $settings['lender'] ?? '';
        $watermarkText = "ISO: {$iso} | Lender: {$lender}";

        $opacity = ($settings['opacity'] ?? 33) / 100;
        $color = $this->hexToRgb($settings['color'] ?? '#878787');

        // Calculate font size to fit within page with comfortable margins
        // Use 40% of smaller dimension to ensure text stays well within page bounds
        $maxTextWidth = min($pageWidth, $pageHeight) * 0.40;

        $baseFontSize = 24;
        $pdf->SetFont('helvetica', 'B', $baseFontSize);
        $baseTextWidth = $pdf->GetStringWidth($watermarkText);

        $fontSize = ($maxTextWidth / $baseTextWidth) * $baseFontSize;
        $fontSize = max(12, min($fontSize, 32));

        // Center of page
        $centerX = $pageWidth / 2;
        $centerY = $pageHeight / 2;

        // Set styling
        $pdf->SetAlpha($opacity);
        $pdf->SetFont('helvetica', 'B', $fontSize);
        $pdf->SetTextColor($color['r'], $color['g'], $color['b']);

        $textWidth = $pdf->GetStringWidth($watermarkText);
        $textHeight = $fontSize * 0.35; // Approximate text height in mm

        // Apply diagonal watermark using TCPDF's rotation around text center
        // Calculate the position so text CENTER is at page CENTER after rotation
        $pdf->StartTransform();

        // Rotate around the page center
        $pdf->Rotate(45, $centerX, $centerY);

        // Draw text so its center aligns with the rotation point
        // Text() draws from baseline, so we offset by half width and adjust for baseline
        $textX = $centerX - ($textWidth / 2);
        $textY = $centerY - ($textHeight / 2);

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
