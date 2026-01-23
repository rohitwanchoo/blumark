<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWatermarkJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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
            'font_size' => 'required|integer|min:8|max:48',
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'opacity' => 'required|integer|min:1|max:100',
            'position' => 'nullable|string|in:diagonal,scattered,top-left,top-right,top-center,bottom-left,bottom-right,bottom-center,center',
            'rotation' => 'nullable|integer|min:0|max:360',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxUploadMb = config('watermark.max_upload_mb', 50);

        return [
            'pdf_file.required' => 'Please select a PDF file to upload.',
            'pdf_file.mimes' => 'Only PDF files are allowed.',
            'pdf_file.max' => "The PDF file must not exceed {$maxUploadMb}MB.",
            'iso.required' => 'Please enter the ISO value.',
            'iso.max' => 'ISO must not exceed 50 characters.',
            'lender.required' => 'Please enter the Lender value.',
            'lender.max' => 'Lender must not exceed 50 characters.',
            'opacity.min' => 'Opacity must be at least 1%.',
            'opacity.max' => 'Opacity must not exceed 100%.',
            'font_size.min' => 'Font size must be at least 8.',
            'font_size.max' => 'Font size must not exceed 48.',
            'color.regex' => 'Please enter a valid hex color code (e.g., #888888).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values if not provided
        $defaults = config('watermark.defaults', []);

        $this->merge([
            'opacity' => $this->input('opacity', $defaults['opacity'] ?? 10),
            'font_size' => $this->input('font_size', $defaults['font_size'] ?? 15),
            'color' => $this->input('color', $defaults['color'] ?? '#878787'),
            'position' => $this->input('position', $defaults['position'] ?? 'diagonal'),
            'rotation' => $this->input('rotation', $defaults['rotation'] ?? 45),
        ]);
    }

    /**
     * Get the watermark settings array for storage.
     */
    public function getWatermarkSettings(): array
    {
        return [
            'type' => 'iso_lender',
            'iso' => $this->input('iso'),
            'lender' => $this->input('lender'),
            'font_size' => (int) $this->input('font_size'),
            'color' => $this->input('color'),
            'opacity' => (int) $this->input('opacity'),
            'position' => $this->input('position', 'diagonal'),
            'rotation' => (int) $this->input('rotation', 45),
            'flatten_pdf' => true, // Always enabled for security
        ];
    }
}
