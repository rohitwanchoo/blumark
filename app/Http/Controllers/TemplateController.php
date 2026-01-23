<?php

namespace App\Http\Controllers;

use App\Models\WatermarkTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index()
    {
        $templates = Auth::user()
            ->watermarkTemplates()
            ->orderByDesc('usage_count')
            ->orderByDesc('updated_at')
            ->get();

        return view('templates.index', compact('templates'));
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'iso' => 'required|string|max:100',
            'lender' => 'required|string|max:100',
            'lender_email' => 'nullable|email|max:255',
            'font_size' => 'required|integer|min:8|max:48',
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'opacity' => 'required|integer|min:1|max:100',
            'position' => 'required|string|in:diagonal,scattered,top-left,top-right,top-center,bottom-left,bottom-right,bottom-center,center',
            'rotation' => 'required|integer|min:0|max:360',
            'is_default' => 'boolean',
        ]);

        $user = Auth::user();

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            $user->watermarkTemplates()->update(['is_default' => false]);
        }

        $template = $user->watermarkTemplates()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'template' => $template,
                'message' => 'Template saved successfully.',
            ]);
        }

        return redirect()->route('templates.index')
            ->with('success', 'Template saved successfully.');
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, WatermarkTemplate $template)
    {
        $this->authorize('update', $template);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'iso' => 'required|string|max:100',
            'lender' => 'required|string|max:100',
            'lender_email' => 'nullable|email|max:255',
            'font_size' => 'required|integer|min:8|max:48',
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'opacity' => 'required|integer|min:1|max:100',
            'position' => 'required|string|in:diagonal,scattered,top-left,top-right,top-center,bottom-left,bottom-right,bottom-center,center',
            'rotation' => 'required|integer|min:0|max:360',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Auth::user()->watermarkTemplates()
                ->where('id', '!=', $template->id)
                ->update(['is_default' => false]);
        }

        $template->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'template' => $template->fresh(),
                'message' => 'Template updated successfully.',
            ]);
        }

        return redirect()->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(WatermarkTemplate $template)
    {
        $this->authorize('delete', $template);

        $template->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully.',
            ]);
        }

        return redirect()->route('templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    /**
     * Get templates as JSON for AJAX requests.
     */
    public function list()
    {
        $templates = Auth::user()
            ->watermarkTemplates()
            ->orderByDesc('usage_count')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($templates);
    }

    /**
     * Quick save template from dashboard.
     */
    public function quickSave(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'iso' => 'required|string|max:100',
            'lender' => 'required|string|max:100',
            'lender_email' => 'nullable|email|max:255',
            'font_size' => 'integer|min:8|max:48',
            'color' => ['regex:/^#[0-9A-Fa-f]{6}$/'],
            'opacity' => 'integer|min:1|max:100',
            'position' => 'string|in:diagonal,scattered,top-left,top-right,top-center,bottom-left,bottom-right,bottom-center,center',
            'rotation' => 'integer|min:0|max:360',
        ]);

        // Set defaults if not provided
        $validated['font_size'] = $validated['font_size'] ?? config('watermark.defaults.font_size', 15);
        $validated['color'] = $validated['color'] ?? config('watermark.defaults.color', '#878787');
        $validated['opacity'] = $validated['opacity'] ?? config('watermark.defaults.opacity', 20);
        $validated['position'] = $validated['position'] ?? config('watermark.defaults.position', 'diagonal');
        $validated['rotation'] = $validated['rotation'] ?? config('watermark.defaults.rotation', 45);

        $template = Auth::user()->watermarkTemplates()->create($validated);

        return response()->json([
            'success' => true,
            'template' => $template,
            'message' => 'Template saved!',
        ]);
    }
}
