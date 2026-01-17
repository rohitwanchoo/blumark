<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WatermarkJobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'original_filename' => $this->original_filename,
            'status' => $this->status,
            'error_message' => $this->error_message,
            'settings' => [
                'iso' => $this->settings['iso'] ?? null,
                'lender' => $this->settings['lender'] ?? null,
                'font_size' => $this->settings['font_size'] ?? null,
                'color' => $this->settings['color'] ?? null,
                'opacity' => $this->settings['opacity'] ?? null,
            ],
            'page_count' => $this->page_count,
            'file_size' => $this->file_size,
            'file_size_formatted' => $this->getFormattedFileSize(),
            'can_download' => $this->isDone() && $this->outputExists(),
            'processed_at' => $this->processed_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
