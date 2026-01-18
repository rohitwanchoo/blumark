<?php

namespace App\Services;

use App\Models\WatermarkJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class RemovalDetectionService
{
    protected InvisibleWatermarkService $invisibleService;
    protected TamperDetectionService $tamperService;

    public function __construct(
        InvisibleWatermarkService $invisibleService,
        TamperDetectionService $tamperService
    ) {
        $this->invisibleService = $invisibleService;
        $this->tamperService = $tamperService;
    }

    /**
     * Analyze a PDF for signs of watermark removal.
     */
    public function analyze(string $pdfPath, ?WatermarkJob $originalJob = null): array
    {
        $result = [
            'removal_detected' => false,
            'confidence' => 0,
            'indicators' => [],
            'techniques_detected' => [],
            'recommendations' => [],
        ];

        // Check for various removal indicators
        $checks = [
            'editing_tools' => $this->checkEditingToolSignatures($pdfPath),
            'content_streams' => $this->checkContentStreamManipulation($pdfPath),
            'layer_removal' => $this->checkLayerRemoval($pdfPath),
            'metadata_scrubbing' => $this->checkMetadataScrubbing($pdfPath),
            'invisible_watermark' => $this->checkInvisibleWatermark($pdfPath, $originalJob),
            'structure_analysis' => $this->analyzeStructure($pdfPath),
        ];

        $removalScore = 0;
        $maxScore = 0;

        foreach ($checks as $checkName => $check) {
            $result['indicators'][$checkName] = $check;

            $maxScore += $check['weight'] ?? 1;
            if ($check['suspicious']) {
                $removalScore += ($check['weight'] ?? 1) * ($check['severity'] ?? 0.5);
                if (!empty($check['techniques'])) {
                    $result['techniques_detected'] = array_merge(
                        $result['techniques_detected'],
                        $check['techniques']
                    );
                }
            }
        }

        // Calculate overall confidence
        $result['confidence'] = $maxScore > 0 ? min(1, $removalScore / $maxScore) : 0;
        $result['removal_detected'] = $result['confidence'] > 0.5;

        // Remove duplicate techniques
        $result['techniques_detected'] = array_unique($result['techniques_detected']);

        // Add recommendations
        $result['recommendations'] = $this->generateRecommendations($result);

        return $result;
    }

    /**
     * Check for editing tool signatures in the PDF.
     */
    protected function checkEditingToolSignatures(string $pdfPath): array
    {
        $content = file_get_contents($pdfPath);

        $suspiciousTools = [
            'Adobe Acrobat' => [
                'pattern' => '/Adobe\s+Acrobat/i',
                'severity' => 0.3,
                'description' => 'Adobe Acrobat can be used for legitimate editing or watermark removal',
            ],
            'PDFsam' => [
                'pattern' => '/PDFsam/i',
                'severity' => 0.5,
                'description' => 'PDF splitting/merging tool sometimes used to remove watermarks',
            ],
            'pdf-parser' => [
                'pattern' => '/pdf-parser/i',
                'severity' => 0.7,
                'description' => 'Low-level PDF manipulation tool',
            ],
            'qpdf' => [
                'pattern' => '/qpdf/i',
                'severity' => 0.6,
                'description' => 'PDF transformation tool that can remove content',
            ],
            'mupdf' => [
                'pattern' => '/mupdf/i',
                'severity' => 0.4,
                'description' => 'PDF toolkit that can modify documents',
            ],
            'Ghostscript' => [
                'pattern' => '/Ghostscript/i',
                'severity' => 0.4,
                'description' => 'PDF processor that can be used to recreate PDFs',
            ],
            'iText' => [
                'pattern' => '/iText/i',
                'severity' => 0.5,
                'description' => 'PDF library that enables content modification',
            ],
            'PyPDF' => [
                'pattern' => '/PyPDF|pypdf/i',
                'severity' => 0.6,
                'description' => 'Python library for PDF manipulation',
            ],
        ];

        $detected = [];
        $maxSeverity = 0;
        $techniques = [];

        foreach ($suspiciousTools as $tool => $config) {
            if (preg_match($config['pattern'], $content)) {
                $detected[] = [
                    'tool' => $tool,
                    'severity' => $config['severity'],
                    'description' => $config['description'],
                ];
                $maxSeverity = max($maxSeverity, $config['severity']);
                $techniques[] = "Edited with {$tool}";
            }
        }

        return [
            'suspicious' => !empty($detected),
            'severity' => $maxSeverity,
            'weight' => 1.5,
            'tools_detected' => $detected,
            'techniques' => $techniques,
            'message' => empty($detected)
                ? 'No suspicious editing tools detected'
                : 'Found signatures of ' . count($detected) . ' editing tool(s)',
        ];
    }

    /**
     * Check for content stream manipulation.
     */
    protected function checkContentStreamManipulation(string $pdfPath): array
    {
        $content = file_get_contents($pdfPath);

        $indicators = [];
        $severity = 0;
        $techniques = [];

        // Check for multiple content streams per page (sign of editing)
        preg_match_all('/\/Contents\s*\[([^\]]+)\]/', $content, $matches);
        foreach ($matches[1] as $contentArray) {
            $refs = preg_match_all('/\d+\s+\d+\s+R/', $contentArray);
            if ($refs > 2) {
                $indicators[] = 'Multiple content streams detected';
                $severity = max($severity, 0.4);
                $techniques[] = 'Content stream layering';
            }
        }

        // Check for removed/redacted content markers
        if (preg_match('/\/Redact/i', $content)) {
            $indicators[] = 'Redaction markers found';
            $severity = max($severity, 0.7);
            $techniques[] = 'Content redaction';
        }

        // Check for unusual stream lengths
        preg_match_all('/\/Length\s+(\d+)/', $content, $lengthMatches);
        if (!empty($lengthMatches[1])) {
            $lengths = array_map('intval', $lengthMatches[1]);
            $avgLength = array_sum($lengths) / count($lengths);

            // Check for suspiciously small streams
            $smallStreams = count(array_filter($lengths, fn($l) => $l < 10));
            if ($smallStreams > count($lengths) * 0.3) {
                $indicators[] = 'Many unusually small content streams';
                $severity = max($severity, 0.3);
            }
        }

        return [
            'suspicious' => !empty($indicators),
            'severity' => $severity,
            'weight' => 1.2,
            'indicators' => $indicators,
            'techniques' => $techniques,
            'message' => empty($indicators)
                ? 'Content streams appear normal'
                : implode('; ', $indicators),
        ];
    }

    /**
     * Check for layer removal.
     */
    protected function checkLayerRemoval(string $pdfPath): array
    {
        $content = file_get_contents($pdfPath);

        $indicators = [];
        $severity = 0;
        $techniques = [];

        // Check for OCG (Optional Content Group) references that might indicate removed layers
        if (preg_match('/\/OCGs\s*\[([^\]]*)\]/', $content, $matches)) {
            $ocgRefs = preg_match_all('/\d+\s+\d+\s+R/', $matches[1]);

            // Check if there are OCG references but no actual OCG definitions
            preg_match_all('/\/Type\s*\/OCG/', $content, $ocgDefs);
            if ($ocgRefs > count($ocgDefs[0])) {
                $indicators[] = 'Missing layer definitions (possible layer removal)';
                $severity = max($severity, 0.6);
                $techniques[] = 'Layer removal';
            }
        }

        // Check for XObject (image/form) references that don't resolve
        preg_match_all('/\/XObject\s*<<([^>]+)>>/', $content, $xobjectMatches);
        foreach ($xobjectMatches[1] as $xobjectDict) {
            preg_match_all('/\/(\w+)\s+(\d+)\s+(\d+)\s+R/', $xobjectDict, $refs);
            foreach ($refs[2] as $objNum) {
                if (!preg_match("/{$objNum}\s+\d+\s+obj/", $content)) {
                    $indicators[] = 'Orphaned XObject reference (possible removal)';
                    $severity = max($severity, 0.5);
                    $techniques[] = 'XObject removal';
                    break 2;
                }
            }
        }

        return [
            'suspicious' => !empty($indicators),
            'severity' => $severity,
            'weight' => 1.3,
            'indicators' => $indicators,
            'techniques' => $techniques,
            'message' => empty($indicators)
                ? 'No signs of layer removal detected'
                : implode('; ', $indicators),
        ];
    }

    /**
     * Check for metadata scrubbing.
     */
    protected function checkMetadataScrubbing(string $pdfPath): array
    {
        $indicators = [];
        $severity = 0;
        $techniques = [];

        // Use pdfinfo to get metadata
        $result = Process::timeout(30)->run('pdfinfo ' . escapeshellarg($pdfPath));
        $output = $result->output();

        // Parse metadata
        $metadata = [];
        foreach (explode("\n", $output) as $line) {
            if (preg_match('/^([^:]+):\s*(.*)$/', $line, $matches)) {
                $metadata[trim($matches[1])] = trim($matches[2]);
            }
        }

        // Check for missing expected metadata
        $expectedFields = ['Creator', 'Producer', 'CreationDate'];
        $missingFields = [];
        foreach ($expectedFields as $field) {
            if (empty($metadata[$field])) {
                $missingFields[] = $field;
            }
        }

        if (count($missingFields) >= 2) {
            $indicators[] = 'Multiple metadata fields missing: ' . implode(', ', $missingFields);
            $severity = max($severity, 0.5);
            $techniques[] = 'Metadata scrubbing';
        }

        // Check for generic/sanitized metadata
        if (!empty($metadata['Producer']) && preg_match('/^(Unknown|None|-)$/i', $metadata['Producer'])) {
            $indicators[] = 'Producer field appears sanitized';
            $severity = max($severity, 0.4);
            $techniques[] = 'Metadata sanitization';
        }

        // Check for removed XMP metadata
        $content = file_get_contents($pdfPath);
        if (preg_match('/<x:xmpmeta/', $content)) {
            // Has XMP, check if it's stripped
            if (!preg_match('/<dc:creator/', $content) && !preg_match('/<xmp:CreateDate/', $content)) {
                $indicators[] = 'XMP metadata appears stripped';
                $severity = max($severity, 0.4);
            }
        }

        return [
            'suspicious' => !empty($indicators),
            'severity' => $severity,
            'weight' => 1.0,
            'indicators' => $indicators,
            'techniques' => $techniques,
            'metadata' => $metadata,
            'message' => empty($indicators)
                ? 'Metadata appears intact'
                : implode('; ', $indicators),
        ];
    }

    /**
     * Check invisible watermark status.
     */
    protected function checkInvisibleWatermark(string $pdfPath, ?WatermarkJob $originalJob = null): array
    {
        $indicators = [];
        $severity = 0;
        $techniques = [];

        // Try to extract invisible watermark
        $marker = $this->invisibleService->getMarker($pdfPath);

        if ($originalJob) {
            // We have the original job, compare markers
            $expectedMarker = null;
            if ($originalJob->fingerprints()->exists()) {
                $expectedMarker = $originalJob->fingerprints()->first()->unique_marker ?? null;
            }

            if ($expectedMarker && !$marker) {
                $indicators[] = 'Expected invisible watermark not found';
                $severity = 0.9;
                $techniques[] = 'Invisible watermark removal';
            } elseif ($expectedMarker && $marker !== $expectedMarker) {
                $indicators[] = 'Invisible watermark marker mismatch';
                $severity = 0.8;
                $techniques[] = 'Watermark tampering';
            }
        } else {
            // No original job, just check if marker exists
            if (!$marker) {
                // Not necessarily suspicious without original to compare
                $indicators[] = 'No invisible watermark detected';
                $severity = 0.2;
            }
        }

        return [
            'suspicious' => $severity > 0.5,
            'severity' => $severity,
            'weight' => 2.0, // High weight for invisible watermark
            'markers_found' => $marker ? [$marker] : [],
            'indicators' => $indicators,
            'techniques' => $techniques,
            'message' => empty($indicators)
                ? 'Invisible watermark present and valid'
                : implode('; ', $indicators),
        ];
    }

    /**
     * Analyze PDF structure for anomalies.
     */
    protected function analyzeStructure(string $pdfPath): array
    {
        $content = file_get_contents($pdfPath);
        $filesize = strlen($content);

        $indicators = [];
        $severity = 0;
        $techniques = [];

        // Check for incremental updates (sign of editing)
        $trailerCount = substr_count($content, 'trailer');
        if ($trailerCount > 1) {
            // Multiple trailers suggest incremental edits
            if ($trailerCount > 3) {
                $indicators[] = "Document has {$trailerCount} revisions (heavily edited)";
                $severity = max($severity, 0.5);
                $techniques[] = 'Incremental editing';
            }
        }

        // Check object/file size ratio
        preg_match_all('/\d+\s+\d+\s+obj/', $content, $objects);
        $objectCount = count($objects[0]);
        $ratio = $objectCount / ($filesize / 1024);

        if ($ratio > 15) {
            $indicators[] = 'Unusual object density (possible content removal)';
            $severity = max($severity, 0.3);
        }

        // Check for orphaned objects
        preg_match_all('/(\d+)\s+\d+\s+obj/', $content, $objNumbers);
        $declaredObjects = array_unique($objNumbers[1]);

        preg_match_all('/(\d+)\s+\d+\s+R/', $content, $refNumbers);
        $referencedObjects = array_unique($refNumbers[1]);

        $orphans = array_diff($declaredObjects, $referencedObjects);
        // Filter out root objects (trailer references)
        if (count($orphans) > 5) {
            $indicators[] = 'Multiple orphaned objects detected';
            $severity = max($severity, 0.4);
            $techniques[] = 'Object removal';
        }

        return [
            'suspicious' => !empty($indicators),
            'severity' => $severity,
            'weight' => 1.1,
            'object_count' => $objectCount,
            'revision_count' => $trailerCount,
            'indicators' => $indicators,
            'techniques' => $techniques,
            'message' => empty($indicators)
                ? 'Document structure appears normal'
                : implode('; ', $indicators),
        ];
    }

    /**
     * Generate recommendations based on analysis.
     */
    protected function generateRecommendations(array $result): array
    {
        $recommendations = [];

        if ($result['removal_detected']) {
            $recommendations[] = 'Request the original document from a trusted source';
            $recommendations[] = 'Verify document authenticity through alternative means';

            if (in_array('Invisible watermark removal', $result['techniques_detected'])) {
                $recommendations[] = 'Document appears to have had security features removed';
            }

            if (in_array('Content redaction', $result['techniques_detected'])) {
                $recommendations[] = 'Check for content that may have been removed or hidden';
            }
        } elseif ($result['confidence'] > 0.3) {
            $recommendations[] = 'Document shows some signs of editing - verify if changes are authorized';
        } else {
            $recommendations[] = 'Document appears unmodified - standard verification passed';
        }

        return $recommendations;
    }

    /**
     * Quick check if watermark was likely removed.
     */
    public function wasWatermarkRemoved(string $pdfPath, ?WatermarkJob $originalJob = null): bool
    {
        $analysis = $this->analyze($pdfPath, $originalJob);
        return $analysis['removal_detected'];
    }

    /**
     * Get a summary of detected removal techniques.
     */
    public function getRemovalSummary(string $pdfPath): array
    {
        $analysis = $this->analyze($pdfPath);

        return [
            'likely_removed' => $analysis['removal_detected'],
            'confidence' => round($analysis['confidence'] * 100) . '%',
            'techniques' => $analysis['techniques_detected'],
            'recommendation' => $analysis['recommendations'][0] ?? 'No specific recommendation',
        ];
    }
}
