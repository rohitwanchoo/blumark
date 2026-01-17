<?php

namespace Tests\Unit;

use App\Services\PdfWatermarkService;
use Exception;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfWatermarkServiceTest extends TestCase
{
    protected PdfWatermarkService $service;
    protected string $fixturesPath;
    protected string $outputPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PdfWatermarkService();
        $this->fixturesPath = storage_path('app/test-fixtures');
        $this->outputPath = storage_path('app/test-outputs');

        // Create directories
        if (!is_dir($this->fixturesPath)) {
            mkdir($this->fixturesPath, 0755, true);
        }
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }

        // Create a simple test PDF using TCPDF
        $this->createTestPdf();
    }

    protected function tearDown(): void
    {
        // Clean up test files
        if (is_dir($this->outputPath)) {
            array_map('unlink', glob("{$this->outputPath}/*"));
        }

        parent::tearDown();
    }

    protected function createTestPdf(): void
    {
        $testPdfPath = $this->fixturesPath . '/test.pdf';

        if (file_exists($testPdfPath)) {
            return;
        }

        // Create a simple PDF using TCPDF
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Test');
        $pdf->SetAuthor('Test');
        $pdf->SetTitle('Test PDF');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'This is a test PDF document.', 0, 1);
        $pdf->AddPage();
        $pdf->Cell(0, 10, 'This is page 2.', 0, 1);
        $pdf->Output($testPdfPath, 'F');
    }

    public function test_watermark_text_creates_valid_output(): void
    {
        $inputPath = $this->fixturesPath . '/test.pdf';
        $outputPath = $this->outputPath . '/watermarked-text.pdf';

        $settings = [
            'text' => 'CONFIDENTIAL',
            'font_size' => 48,
            'opacity' => 50,
            'color' => '#888888',
            'rotation' => -45,
            'position' => 'diagonal',
        ];

        $result = $this->service->watermarkText($inputPath, $outputPath, $settings);

        $this->assertFileExists($outputPath);
        $this->assertGreaterThan(0, filesize($outputPath));
        $this->assertEquals(2, $result['page_count']);

        // Verify it's a valid PDF by checking the header
        $content = file_get_contents($outputPath);
        $this->assertStringStartsWith('%PDF', $content);
    }

    public function test_watermark_text_center_position(): void
    {
        $inputPath = $this->fixturesPath . '/test.pdf';
        $outputPath = $this->outputPath . '/watermarked-center.pdf';

        $settings = [
            'text' => 'CENTER',
            'font_size' => 72,
            'opacity' => 30,
            'color' => '#FF0000',
            'rotation' => 0,
            'position' => 'center',
        ];

        $result = $this->service->watermarkText($inputPath, $outputPath, $settings);

        $this->assertFileExists($outputPath);
        $this->assertEquals(2, $result['page_count']);
    }

    public function test_watermark_text_tiled_position(): void
    {
        $inputPath = $this->fixturesPath . '/test.pdf';
        $outputPath = $this->outputPath . '/watermarked-tiled.pdf';

        $settings = [
            'text' => 'DRAFT',
            'font_size' => 24,
            'opacity' => 20,
            'color' => '#0000FF',
            'rotation' => -30,
            'position' => 'tiled',
            'tile_density' => 4,
            'margin_x' => 10,
            'margin_y' => 10,
        ];

        $result = $this->service->watermarkText($inputPath, $outputPath, $settings);

        $this->assertFileExists($outputPath);
        $this->assertEquals(2, $result['page_count']);
    }

    public function test_watermark_text_throws_exception_for_nonexistent_file(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('does not exist');

        $this->service->watermarkText(
            '/nonexistent/path/file.pdf',
            $this->outputPath . '/output.pdf',
            ['text' => 'TEST']
        );
    }

    public function test_get_page_count_returns_correct_count(): void
    {
        $inputPath = $this->fixturesPath . '/test.pdf';

        $pageCount = $this->service->getPageCount($inputPath);

        $this->assertEquals(2, $pageCount);
    }

    public function test_watermark_image_creates_valid_output(): void
    {
        $inputPath = $this->fixturesPath . '/test.pdf';
        $outputPath = $this->outputPath . '/watermarked-image.pdf';
        $imagePath = $this->fixturesPath . '/watermark.png';

        // Create a simple test image
        $this->createTestImage($imagePath);

        $settings = [
            'scale' => 50,
            'opacity' => 40,
            'rotation' => 0,
            'position' => 'center',
        ];

        $result = $this->service->watermarkImage($inputPath, $outputPath, $imagePath, $settings);

        $this->assertFileExists($outputPath);
        $this->assertGreaterThan(0, filesize($outputPath));
        $this->assertEquals(2, $result['page_count']);
    }

    public function test_watermark_image_diagonal_position(): void
    {
        $inputPath = $this->fixturesPath . '/test.pdf';
        $outputPath = $this->outputPath . '/watermarked-image-diagonal.pdf';
        $imagePath = $this->fixturesPath . '/watermark.png';

        $this->createTestImage($imagePath);

        $settings = [
            'scale' => 30,
            'opacity' => 50,
            'rotation' => -45,
            'position' => 'diagonal',
        ];

        $result = $this->service->watermarkImage($inputPath, $outputPath, $imagePath, $settings);

        $this->assertFileExists($outputPath);
        $this->assertEquals(2, $result['page_count']);
    }

    public function test_watermark_image_throws_exception_for_invalid_image(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('does not exist');

        $inputPath = $this->fixturesPath . '/test.pdf';
        $outputPath = $this->outputPath . '/output.pdf';

        $this->service->watermarkImage(
            $inputPath,
            $outputPath,
            '/nonexistent/image.png',
            ['scale' => 50]
        );
    }

    public function test_hex_to_rgb_conversion(): void
    {
        // Use reflection to test protected method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('hexToRgb');
        $method->setAccessible(true);

        // Test standard hex
        $result = $method->invoke($this->service, '#FF0000');
        $this->assertEquals(['r' => 255, 'g' => 0, 'b' => 0], $result);

        // Test without hash
        $result = $method->invoke($this->service, '00FF00');
        $this->assertEquals(['r' => 0, 'g' => 255, 'b' => 0], $result);

        // Test short hex
        $result = $method->invoke($this->service, '#FFF');
        $this->assertEquals(['r' => 255, 'g' => 255, 'b' => 255], $result);
    }

    protected function createTestImage(string $path): void
    {
        if (file_exists($path)) {
            return;
        }

        // Create a simple PNG image
        $image = imagecreatetruecolor(200, 100);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagefill($image, 0, 0, $white);
        imagestring($image, 5, 50, 40, 'WATERMARK', $black);

        imagepng($image, $path);
        imagedestroy($image);
    }
}
