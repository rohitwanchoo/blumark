<?php

namespace App\Console\Commands;

use App\Models\DocumentFingerprint;
use Illuminate\Console\Command;

class BackfillOutputFileHash extends Command
{
    protected $signature = 'fingerprints:backfill-hash';

    protected $description = 'Backfill output_file_hash for existing fingerprints';

    public function handle(): int
    {
        $fingerprints = DocumentFingerprint::whereNull('output_file_hash')
            ->with('watermarkJob')
            ->get();

        $this->info("Found {$fingerprints->count()} fingerprints without output_file_hash");

        $updated = 0;
        $skipped = 0;

        foreach ($fingerprints as $fingerprint) {
            $job = $fingerprint->watermarkJob;

            if (!$job || !$job->output_path) {
                $this->warn("Skipping fingerprint {$fingerprint->id}: No job or output path");
                $skipped++;
                continue;
            }

            $outputPath = storage_path('app/' . $job->output_path);

            if (!file_exists($outputPath)) {
                $this->warn("Skipping fingerprint {$fingerprint->id}: Output file not found at {$outputPath}");
                $skipped++;
                continue;
            }

            $hash = hash_file('sha256', $outputPath);
            $fingerprint->update(['output_file_hash' => $hash]);
            $updated++;

            $this->line("Updated fingerprint {$fingerprint->id} with hash: " . substr($hash, 0, 16) . "...");
        }

        $this->info("Backfill complete: {$updated} updated, {$skipped} skipped");

        return Command::SUCCESS;
    }
}
