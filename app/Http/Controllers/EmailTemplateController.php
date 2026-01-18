<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = Auth::user()->emailTemplates()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('email-templates.index', compact('templates'));
    }

    public function create()
    {
        $placeholders = EmailTemplate::$placeholders;

        return view('email-templates.create', compact('placeholders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'is_default' => 'boolean',
        ]);

        $user = Auth::user();

        // If this is set as default, or if user has no templates yet
        $isDefault = $validated['is_default'] ?? false;
        if (!$isDefault && $user->emailTemplates()->count() === 0) {
            $isDefault = true;
        }

        $template = $user->emailTemplates()->create([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'is_default' => false,
        ]);

        if ($isDefault) {
            $template->makeDefault();
        }

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    public function edit(EmailTemplate $template)
    {
        $this->authorize('update', $template);

        $placeholders = EmailTemplate::$placeholders;

        return view('email-templates.edit', compact('template', 'placeholders'));
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $this->authorize('update', $template);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'is_default' => 'boolean',
        ]);

        $template->update([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
        ]);

        if ($validated['is_default'] ?? false) {
            $template->makeDefault();
        }

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $template)
    {
        $this->authorize('delete', $template);

        $wasDefault = $template->is_default;
        $userId = $template->user_id;

        $template->delete();

        // If we deleted the default, make another template default
        if ($wasDefault) {
            $newDefault = EmailTemplate::where('user_id', $userId)->first();
            if ($newDefault) {
                $newDefault->makeDefault();
            }
        }

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    public function makeDefault(EmailTemplate $template)
    {
        $this->authorize('update', $template);

        $template->makeDefault();

        return back()->with('success', 'Template set as default.');
    }

    public function preview(Request $request)
    {
        $user = Auth::user();

        $subject = $request->input('subject', '');
        $body = $request->input('body', '');

        // Sample data for preview
        $data = [
            'lender_name' => 'Sample Lending Corp',
            'lender_contact' => 'John Smith',
            'sender_name' => $user->getFullName(),
            'sender_company' => $user->company_name ?? $user->name,
            'document_name' => 'Q1 2026 Loan Package',
        ];

        $template = new EmailTemplate([
            'subject' => $subject,
            'body' => $body,
        ]);

        return response()->json([
            'subject' => $template->renderSubject($data),
            'body' => $template->renderBody($data),
        ]);
    }
}
