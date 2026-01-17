<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreWatermarkJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxUploadMb = config('watermark.max_upload_mb', 50);
        $maxUploadKb = $maxUploadMb * 1024;

        return [
            'pdf_file' => [
                'required',
                'file',
                'mimes:pdf',
                "max:{$maxUploadKb}",
            ],
            'iso' => 'required|string|max:50',
            'lender' => 'required|string|max:50',
            'font_size' => 'sometimes|integer|min:8|max:48',
            'color' => ['sometimes', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'opacity' => 'sometimes|integer|min:1|max:100',
        ];
    }

    protected function prepareForValidation(): void
    {
        $defaults = config('watermark.defaults', []);

        $this->merge([
            'opacity' => $this->input('opacity', $defaults['opacity'] ?? 33),
            'font_size' => $this->input('font_size', $defaults['font_size'] ?? 15),
            'color' => $this->input('color', $defaults['color'] ?? '#878787'),
        ]);
    }

    public function getWatermarkSettings(): array
    {
        return [
            'type' => 'iso_lender',
            'iso' => $this->input('iso'),
            'lender' => $this->input('lender'),
            'font_size' => (int) $this->input('font_size'),
            'color' => $this->input('color'),
            'opacity' => (int) $this->input('opacity'),
            'flatten_pdf' => true,
        ];
    }
}
