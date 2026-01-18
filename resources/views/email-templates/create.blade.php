<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="{{ route('email-templates.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Email Templates
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create Email Template</h1>
            <p class="text-gray-500 mt-1">Create a reusable email template for sending documents to lenders</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('email-templates.store') }}" method="POST" x-data="emailTemplateForm()">
            @csrf

            <div class="space-y-6">
                <!-- Template Info -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Template Information</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Template Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   required
                                   placeholder="e.g., Professional Document Delivery"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        </div>

                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="is_default"
                                   value="1"
                                   {{ old('is_default') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-gray-700">Set as default template</span>
                        </label>
                    </div>
                </div>

                <!-- Placeholders Reference -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800 mb-2">Available Placeholders</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                                @foreach($placeholders as $placeholder => $description)
                                    <div>
                                        <code class="bg-blue-100 px-1 rounded text-xs">{{ $placeholder }}</code>
                                        <span class="text-blue-700 text-xs ml-1">{{ $description }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Content -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Email Content</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                                Subject Line <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="subject"
                                   id="subject"
                                   x-model="subject"
                                   required
                                   placeholder="e.g., Document from {sender_company}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="body" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Body <span class="text-red-500">*</span>
                            </label>
                            <textarea name="body"
                                      id="body"
                                      x-model="body"
                                      rows="10"
                                      required
                                      placeholder="Dear {lender_contact},&#10;&#10;Please find attached..."
                                      class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none font-mono text-sm">{{ old('body') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Plain text only. Placeholders will be replaced when sending.</p>
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Preview</h3>
                        <button type="button" @click="updatePreview()" class="text-sm text-primary-600 hover:text-primary-700">
                            Refresh Preview
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="mb-3">
                                <span class="text-xs font-medium text-gray-500">Subject:</span>
                                <p class="text-gray-900 font-medium" x-text="previewSubject || 'Enter a subject above...'"></p>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <span class="text-xs font-medium text-gray-500">Body:</span>
                                <pre class="text-gray-700 text-sm whitespace-pre-wrap font-sans mt-1" x-text="previewBody || 'Enter email body above...'"></pre>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Sample values: Lender = "Sample Lending Corp", Contact = "John Smith", Sender = "{{ Auth::user()->getFullName() }}", Company = "{{ Auth::user()->company_name ?? Auth::user()->name }}"
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('email-templates.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Create Template
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function emailTemplateForm() {
            return {
                subject: '{{ old('subject', 'Document from {sender_company}') }}',
                body: `{{ old('body', "Dear {lender_contact},\n\nPlease find the attached document \"{document_name}\" from {sender_company}.\n\nIf you have any questions, please don't hesitate to reach out.\n\nBest regards,\n{sender_name}\n{sender_company}") }}`,
                previewSubject: '',
                previewBody: '',
                sampleData: {
                    '{lender_name}': 'Sample Lending Corp',
                    '{lender_contact}': 'John Smith',
                    '{sender_name}': '{{ Auth::user()->getFullName() }}',
                    '{sender_company}': '{{ Auth::user()->company_name ?? Auth::user()->name }}',
                    '{document_name}': 'Q1 2026 Loan Package'
                },
                init() {
                    this.updatePreview();
                    this.$watch('subject', () => this.updatePreview());
                    this.$watch('body', () => this.updatePreview());
                },
                updatePreview() {
                    this.previewSubject = this.replacePlaceholders(this.subject);
                    this.previewBody = this.replacePlaceholders(this.body);
                },
                replacePlaceholders(text) {
                    let result = text;
                    for (const [placeholder, value] of Object.entries(this.sampleData)) {
                        result = result.split(placeholder).join(value);
                    }
                    return result;
                }
            }
        }
    </script>
</x-app-layout>
