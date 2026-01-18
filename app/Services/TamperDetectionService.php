<?php

namespace App\Services;

use App\Models\DocumentFingerprint;
use App\Models\WatermarkJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class TamperDetectionService
{
    protected DocumentFingerprintService $fingerprintService;

    public function __construct(DocumentFingerprintService $fingerprintService)
    {
        $this->fingerprintService = $fingerprintService;
    }

    /**
     * Analyze a document for tampering.
     */
    public function analyze(string $pdfPath, ?DocumentFingerprint $fingerprint = null): array
    {
        $results = [
            'tampered' => false,
            'confidence' => 1.0,
            'checks' => [],
            'warnings' => [],
            'errors' => [],
        ];

        // Run all checks
        $results['checks']['file_integrity'] = $this->checkFileIntegrity($pdfPath);
        $results['checks']['pdf_structure'] = $this->checkPdfStructure($pdfPath);
        $results['checks']['metadata'] = $this->checkMetadata($pdfPath);
        $results['checks']['modification_history'] = $this->checkModificationHistory($pdfPath);

        if ($fingerprint) {
            $results['checks']['fingerprint_match'] = $this->checkFingerprintMatch($pdfPath, $fingerprint);
            $results['checks']['watermark_integrity'] = $this->checkWatermarkIntegrity($pdfPath, $fingerprint);
        }

        // Aggregate results
        $failedChecks = 0;
        $warningChecks = 0;

        foreach ($results['checks'] as $checkName => $check) {
            if ($check['status'] === 'failed') {
                $failedChecks++;
                $results['warnings'][] = $check['message'] ?? "Check '{$checkName}' failed";
            } elseif ($check['status'] === 'warning') {
                $warningChecks++;
                $results['warnings'][] = $check['message'] ?? "Check '{$checkName}' raised warning";
            } elseif ($check['status'] === 'error') {
                $results['errors'][] = $check['message'] ?? "Check '{$checkName}' encountered error";
            }
        }

        // Determine if tampered
        $results['tampered'] = $failedChecks > 0;
        $results['confidence'] = max(0, 1 - ($failedChecks * 0.3) - ($warningChecks * 0.1));

        return $results;
    }

    /**
     * Check basic file integrity.
     */
    protected function checkFileIntegrity(string $pdfPath): array
    {
        if (!file_exists($pdfPath)) {
            return [
                'status' => 'error',
                'message' => 'File not found',
            ];
        }

        // Check PDF header
        $handle = fopen($pdfPath, 'rb');
        $header = fread($handle, 5);
        fclose($handle);

        if ($header !== '%PDF-') {
            return [
                'status' => 'failed',
                'message' => 'Invalid PDF header - file may be corrupted or not a PDF',
            ];
        }

        // Check file can be parsed
        $result = Process::timeout(30)->run(
            'pdfinfo ' . escapeshellarg($pdfPath) . ' 2>&1'
        );

        if (!$result->successful()) {
            return [
                'status' => 'warning',
                'message' => 'PDF parsing issues detected',
                'details' => $result->errorOutput(),
            ];
        }

        return [
            'status' => 'passed',
            'message' => 'File integrity verified',
        ];
    }

    /**
     * Check PDF structure for anomalies.
     */
    protected function checkPdfStructure(string $pdfPath): array
    {
        try {
            $content = file_get_contents($pdfPath);

            $issues = [];

            // Check for multiple %%EOF markers (sign of appended content)
            $eofCount = substr_count($content, '%%EOF');
            if ($eofCount > 1) {
                $issues[] = "Multiple EOF markers found ({$eofCount}) - may indicate appended content";
            }

            // Check for incremental updates (not necessarily bad but worth noting)
            if (preg_match_all('/\d+\s+\d+\s+obj/', $content, $matches)) {
                $objectCount = count($matches[0]);
                // High object count relative to file size might indicate embedded content
                $fileSize = filesize($pdfPath);
                $ratio = $objectCount / ($fileSize / 1024);
                if ($ratio > 10) {
                    $issues[] = "Unusual object density detected";
                }
            }

            // Check for JavaScript (potential security concern)
            if (stripos($content, '/JavaScript') !== false || stripos($content, '/JS') !== false) {
                $issues[] = "Document contains JavaScript";
            }

            // Check for embedded files
            if (stripos($content, '/EmbeddedFiles') !== false) {
                $issues[] = "Document contains embedded files";
            }

            if (empty($issues)) {
                return [
                    'status' => 'passed',
                    'message' => 'PDF structure appears normal',
                ];
            }

            return [
                'status' => count($issues) > 1 ? 'warning' : 'passed',
                'message' => implode('; ', $issues),
                'issues' => $issues,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to analyze PDF structure: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check document metadata.
     */
    protected function checkMetadata(string $pdfPath): array
    {
        $result = Process::timeout(30)->run(
            'pdfinfo ' . escapeshellarg($pdfPath)
        );

        if (!$result->successful()) {
            return [
                'status' => 'error',
                'message' => 'Could not read PDF metadata',
            ];
        }

        $output = $result->output();
        $metadata = [];

        // Parse pdfinfo output
        foreach (explode("\n", $output) as $line) {
            if (preg_match('/^([^:]+):\s*(.*)$/', $line, $matches)) {
                $metadata[trim($matches[1])] = trim($matches[2]);
            }
        }

        $issues = [];

        // Check for modification dates
        if (isset($metadata['ModDate']) && isset($metadata['CreationDate'])) {
            $created = strtotime($metadata['CreationDate']);
            $modified = strtotime($metadata['ModDate']);

            if ($modified && $created && $modified > $created) {
                $issues[] = "Document was modified after creation";
            }
        }

        // Check producer for known editing tools
        $editingTools = ['Adobe Acrobat', 'PDFsam', 'pdf-parser', 'iText', 'qpdf'];
        $producer = $metadata['Producer'] ?? '';
        foreach ($editingTools as $tool) {
            if (stripos($producer, $tool) !== false) {
                $issues[] = "Document processed by editing tool: {$tool}";
                break;
            }
        }

        return [
            'status' => empty($issues) ? 'passed' : 'warning',
            'message' => empty($issues) ? 'Metadata appears normal' : implode('; ', $issues),
            'metadata' => $metadata,
            'issues' => $issues,
        ];
    }

    /**
     * Check document modification history.
     */
    protected function checkModificationHistory(string $pdfPath): array
    {
        try {
            $content = file_get_contents($pdfPath);

            // Look for incremental saves
            $trailerCount = substr_count($content, 'trailer');

            if ($trailerCount > 1) {
                return [
                    'status' => 'warning',
                    'message' => "Document has {$trailerCount} revision(s) - has been incrementally saved",
                    'revisions' => $trailerCount,
                ];
            }

            return [
                'status' => 'passed',
                'message' => 'No incremental modifications detected',
                'revisions' => 1,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to check modification history: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if document matches stored fingerprint.
     */
    protected function checkFingerprintMatch(string $pdfPath, DocumentFingerprint $fingerprint): array
    {
        $currentHash = hash_file('sha256', $pdfPath);

        // Note: The fingerprint hash includes more than just file content
        // So we also check the unique marker

        $marker = $this->fingerprintService->extractUniqueMarker($pdfPath);

        if ($marker === null) {
            return [
                'status' => 'failed',
                'message' => 'Could not extract watermark marker from document',
            ];
        }

        if ($marker !== $fingerprint->unique_marker) {
            return [
                'status' => 'failed',
                'message' => 'Watermark marker does not match - document may be from different source',
                'expected_marker' => $fingerprint->unique_marker,
                'found_marker' => $marker,
            ];
        }

        return [
            'status' => 'passed',
            'message' => 'Document marker matches fingerprint',
            'marker' => $marker,
        ];
    }

    /**
     * Check watermark integrity.
     */
    protected function checkWatermarkIntegrity(string $pdfPath, DocumentFingerprint $fingerprint): array
    {
        $job = $fingerprint->watermarkJob;
        if (!$job) {
            return [
                'status' => 'error',
                'message' => 'Original watermark job not found',
            ];
        }

        $settings = $job->settings ?? [];
        $expectedText = $settings['text'] ?? null;

        if (!$expectedText) {
            return [
                'status' => 'passed',
                'message' => 'No text watermark to verify',
            ];
        }

        // Extract text from PDF to check for watermark
        $result = Process::timeout(60)->run(
            'pdftotext ' . escapeshellarg($pdfPath) . ' -'
        );

        if (!$result->successful()) {
            return [
                'status' => 'warning',
                'message' => 'Could not extract text to verify watermark',
            ];
        }

        $extractedText = $result->output();

        // Check if watermark text is present
        if (stripos($extractedText, $expectedText) !== false) {
            return [
                'status' => 'passed',
                'message' => 'Watermark text found in document',
            ];
        }

        // Watermark might not be extractable as text (e.g., image-based)
        // This is not necessarily a failure
        return [
            'status' => 'warning',
            'message' => 'Watermark text not found in extracted text - may be image-based or removed',
        ];
    }

    /**
     * Compare two PDF documents.
     */
    public function compareDocuments(string $originalPath, string $suspectPath): array
    {
        $results = [
            'identical' => false,
            'similarity' => 0,
            'differences' => [],
        ];

        // Compare file hashes
        $originalHash = hash_file('sha256', $originalPath);
        $suspectHash = hash_file('sha256', $suspectPath);

        if ($originalHash === $suspectHash) {
            $results['identical'] = true;
            $results['similarity'] = 1.0;
            return $results;
        }

        // Compare file sizes
        $originalSize = filesize($originalPath);
        $suspectSize = filesize($suspectPath);
        $sizeDiff = abs($originalSize - $suspectSize);
        $sizeRatio = min($originalSize, $suspectSize) / max($originalSize, $suspectSize);

        if ($sizeRatio < 0.9) {
            $results['differences'][] = [
                'type' => 'size',
                'message' => "Significant size difference: {$originalSize} vs {$suspectSize} bytes",
                'severity' => 'high',
            ];
        }

        // Compare page counts
        $originalPages = $this->getPageCount($originalPath);
        $suspectPages = $this->getPageCount($suspectPath);

        if ($originalPages !== $suspectPages) {
            $results['differences'][] = [
                'type' => 'page_count',
                'message' => "Page count differs: {$originalPages} vs {$suspectPages}",
                'severity' => 'high',
            ];
        }

        // Compare extracted text
        $originalText = $this->extractText($originalPath);
        $suspectText = $this->extractText($suspectPath);

        $textSimilarity = $this->calculateTextSimilarity($originalText, $suspectText);

        if ($textSimilarity < 0.95) {
            $results['differences'][] = [
                'type' => 'content',
                'message' => "Text content differs (similarity: " . round($textSimilarity * 100, 1) . "%)",
                'severity' => $textSimilarity < 0.8 ? 'high' : 'medium',
            ];
        }

        // Calculate overall similarity
        $results['similarity'] = ($sizeRatio + $textSimilarity) / 2;
        if ($originalPages === $suspectPages) {
            $results['similarity'] = ($results['similarity'] + 1) / 2;
        }

        return $results;
    }

    /**
     * Get page count of a PDF.
     */
    protected function getPageCount(string $pdfPath): int
    {
        $result = Process::timeout(30)->run(
            'pdfinfo ' . escapeshellarg($pdfPath) . ' | grep Pages'
        );

        if (preg_match('/Pages:\s*(\d+)/', $result->output(), $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    /**
     * Extract text from a PDF.
     */
    protected function extractText(string $pdfPath): string
    {
        $result = Process::timeout(60)->run(
            'pdftotext ' . escapeshellarg($pdfPath) . ' -'
        );

        return $result->successful() ? $result->output() : '';
    }

    /**
     * Calculate similarity between two texts.
     */
    protected function calculateTextSimilarity(string $text1, string $text2): float
    {
        if (empty($text1) && empty($text2)) {
            return 1.0;
        }

        if (empty($text1) || empty($text2)) {
            return 0.0;
        }

        // Normalize texts
        $text1 = preg_replace('/\s+/', ' ', strtolower(trim($text1)));
        $text2 = preg_replace('/\s+/', ' ', strtolower(trim($text2)));

        if ($text1 === $text2) {
            return 1.0;
        }

        // Use similar_text for approximate comparison
        similar_text($text1, $text2, $percent);

        return $percent / 100;
    }

    /**
     * Detect common watermark removal signatures.
     */
    public function detectRemovalSignatures(string $pdfPath): array
    {
        $signatures = [];
        $content = file_get_contents($pdfPath);

        // Check for tools commonly used to remove watermarks
        $tools = [
            'pdf-redact-tools' => '/pdf-redact/i',
            'PDF Eraser' => '/PDF\s*Eraser/i',
            'Infix PDF Editor' => '/Infix/i',
            'Foxit PhantomPDF' => '/PhantomPDF/i',
            'Nitro Pro' => '/Nitro/i',
        ];

        foreach ($tools as $tool => $pattern) {
            if (preg_match($pattern, $content)) {
                $signatures[] = [
                    'tool' => $tool,
                    'confidence' => 'medium',
                    'message' => "Document may have been edited with {$tool}",
                ];
            }
        }

        // Check for signs of content stream manipulation
        if (preg_match('/\/Contents\s*\[\s*\d+\s+\d+\s+R\s+\d+\s+\d+\s+R/', $content)) {
            // Multiple content streams might indicate manipulation
            $signatures[] = [
                'tool' => 'unknown',
                'confidence' => 'low',
                'message' => 'Document has multiple content streams (possible editing)',
            ];
        }

        return $signatures;
    }

    /**
     * Generate a tamper analysis report.
     */
    public function generateReport(string $pdfPath, ?DocumentFingerprint $fingerprint = null): array
    {
        $analysis = $this->analyze($pdfPath, $fingerprint);
        $removalSignatures = $this->detectRemovalSignatures($pdfPath);

        return [
            'summary' => [
                'file' => basename($pdfPath),
                'analyzed_at' => now()->toIso8601String(),
                'tampered' => $analysis['tampered'],
                'confidence' => round($analysis['confidence'] * 100, 1) . '%',
                'warning_count' => count($analysis['warnings']),
                'error_count' => count($analysis['errors']),
            ],
            'analysis' => $analysis,
            'removal_signatures' => $removalSignatures,
            'recommendation' => $this->getRecommendation($analysis, $removalSignatures),
        ];
    }

    /**
     * Get recommendation based on analysis.
     */
    protected function getRecommendation(array $analysis, array $signatures): string
    {
        if ($analysis['tampered']) {
            return 'Document shows signs of tampering. Recommend requesting original from verified source.';
        }

        if (!empty($signatures)) {
            return 'Document may have been edited. Verify authenticity with document owner.';
        }

        if ($analysis['confidence'] < 0.8) {
            return 'Some checks raised warnings. Additional verification recommended.';
        }

        return 'Document appears authentic. No immediate concerns detected.';
    }
}
