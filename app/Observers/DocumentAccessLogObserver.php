<?php

namespace App\Observers;

use App\Models\DocumentAccessLog;
use App\Services\SuspiciousActivityDetector;
use Illuminate\Support\Facades\Log;

class DocumentAccessLogObserver
{
    public function __construct(
        protected SuspiciousActivityDetector $detector
    ) {}

    /**
     * Handle the DocumentAccessLog "created" event.
     */
    public function created(DocumentAccessLog $log): void
    {
        // Queue the analysis to not slow down the request
        // Using afterResponse() to run after the HTTP response is sent
        dispatch(function () use ($log) {
            try {
                $this->detector->analyzeAccess($log);
            } catch (\Exception $e) {
                Log::error("Failed to analyze access log for suspicious activity", [
                    'log_id' => $log->id,
                    'error' => $e->getMessage(),
                ]);
            }
        })->afterResponse();
    }
}
