<x-app-layout>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="batchUpload()">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Batch Upload</h1>
        <p class="text-gray-600 mt-1">Upload multiple PDFs and watermark them all with the same settings.</p>
    </div>

    <form action="{{ route('batch.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- File Upload -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Files</h2>

            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-primary-400 transition-colors"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="handleDrop($event)"
                 :class="{ 'border-primary-500 bg-primary-50': isDragging }">

                <input type="file" name="files[]" id="files" multiple accept=".pdf"
                       class="hidden" @change="handleFiles($event)">

                <div class="space-y-2">
                    <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-gray-600">
                        <label for="files" class="text-primary-600 hover:text-primary-700 font-medium cursor-pointer">
                            Click to upload
                        </label>
                        or drag and drop
                    </p>
                    <p class="text-sm text-gray-500">PDF files only (max {{ config('watermark.max_upload_mb', 50) }}MB each, up to 50 files)</p>
                </div>
            </div>

            <!-- Selected Files List -->
            <div x-show="files.length > 0" class="mt-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700" x-text="files.length + ' file(s) selected'"></span>
                    <button type="button" @click="clearFiles()" class="text-sm text-red-600 hover:text-red-700">Clear all</button>
                </div>
                <div class="max-h-40 overflow-y-auto space-y-1">
                    <template x-for="(file, index) in files" :key="index">
                        <div class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded-lg text-sm">
                            <span class="truncate" x-text="file.name"></span>
                            <button type="button" @click="removeFile(index)" class="text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            @error('files')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('files.*')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Template Selection -->
        @if($templates->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Use Template</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <button type="button"
                        @click="selectedTemplate = null; resetForm()"
                        :class="selectedTemplate === null ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                        class="p-3 rounded-lg border border-gray-200 text-left transition-all">
                    <span class="text-sm font-medium text-gray-900">Custom</span>
                    <p class="text-xs text-gray-500">Enter manually</p>
                </button>
                @foreach($templates as $template)
                <button type="button"
                        @click="applyTemplate({{ $template->toJson() }})"
                        :class="selectedTemplate === {{ $template->id }} ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'"
                        class="p-3 rounded-lg border border-gray-200 text-left transition-all">
                    <span class="text-sm font-medium text-gray-900">{{ $template->name }}</span>
                </button>
                @endforeach
            </div>
            <input type="hidden" name="template_id" :value="selectedTemplate">
        </div>
        @endif

        <!-- Watermark Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Watermark Settings</h2>

            @if(empty(Auth::user()->company_name))
            <div class="mb-6 p-4 bg-orange-50 rounded-xl border border-orange-200">
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-orange-800">ISO Name Required</p>
                        <p class="text-sm text-orange-700 mt-1">Please set your Company Name in your profile to use as your ISO name on watermarks.</p>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center mt-2 text-sm font-medium text-orange-800 hover:text-orange-900">
                            Update Profile
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="space-y-4">
                <!-- ISO Name -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="iso" class="block text-sm font-medium text-gray-700">ISO Name (from profile) *</label>
                        <a href="{{ route('profile.edit') }}" class="text-xs text-primary-600 hover:text-primary-700 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Edit
                        </a>
                    </div>
                    <input type="text" name="iso" id="iso" x-model="iso" required readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                           placeholder="Set in your profile">
                    @error('iso')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lender Name -->
                <div>
                    <label for="lender" class="block text-sm font-medium text-gray-700 mb-2">Lender Name *</label>
                    <input type="text" name="lender" id="lender" x-model="lender" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Enter Lender Name">
                    @error('lender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Advanced Settings (Collapsible) -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <button type="button" @click="showAdvanced = !showAdvanced"
                        class="flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1 transition-transform" :class="{ 'rotate-90': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Advanced Settings
                </button>

                <div x-show="showAdvanced" x-transition class="mt-4 space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="font_size" class="block text-sm font-medium text-gray-700 mb-2">Font Size</label>
                            <input type="number" name="font_size" id="font_size" x-model="fontSize"
                                   min="8" max="48" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label for="opacity" class="block text-sm font-medium text-gray-700 mb-2">Opacity: <span x-text="opacity + '%'"></span></label>
                            <input type="range" name="opacity" id="opacity" x-model="opacity"
                                   min="1" max="100" class="w-full mt-3">
                        </div>
                    </div>
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Watermark Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="color" id="color" x-model="color"
                                   class="h-10 w-16 rounded-lg cursor-pointer border border-gray-300">
                            <span class="text-sm text-gray-600 font-mono bg-gray-50 px-3 py-2 rounded-lg border border-gray-300" x-text="color"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save as Template Option -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <label class="flex items-center">
                <input type="checkbox" name="save_template" x-model="saveAsTemplate"
                       class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <span class="ml-2 text-sm text-gray-700">Save these settings as a template</span>
            </label>

            <div x-show="saveAsTemplate" x-transition class="mt-3">
                <label for="template_name" class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                <input type="text" name="template_name" id="template_name"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                       placeholder="e.g., FastFund Capital">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                Cancel
            </a>
            <button type="submit" :disabled="files.length === 0 || !iso || !iso.trim() || !lender"
                    class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <span x-text="files.length > 0 ? 'Process ' + files.length + ' Files' : 'Select Files to Continue'"></span>
            </button>
        </div>
    </form>
</div>

<script>
function batchUpload() {
    const userCompanyName = @json($user->company_name ?? '');

    return {
        files: [],
        isDragging: false,
        selectedTemplate: null,
        iso: userCompanyName,
        lender: '',
        fontSize: {{ config('watermark.defaults.font_size', 15) }},
        color: '{{ config('watermark.defaults.color', '#878787') }}',
        opacity: {{ config('watermark.defaults.opacity', 10) }},
        showAdvanced: false,
        saveAsTemplate: false,

        handleFiles(event) {
            this.addFiles(event.target.files);
        },

        handleDrop(event) {
            this.isDragging = false;
            this.addFiles(event.dataTransfer.files);
        },

        addFiles(newFiles) {
            for (let file of newFiles) {
                if (file.type === 'application/pdf' && this.files.length < 50) {
                    this.files.push(file);
                }
            }
            this.updateFileInput();
        },

        removeFile(index) {
            this.files.splice(index, 1);
            this.updateFileInput();
        },

        clearFiles() {
            this.files = [];
            this.updateFileInput();
        },

        updateFileInput() {
            const input = document.getElementById('files');
            const dataTransfer = new DataTransfer();
            this.files.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        },

        applyTemplate(template) {
            this.selectedTemplate = template.id;
            this.iso = template.iso;
            this.lender = template.lender;
            this.fontSize = template.font_size;
            this.color = template.color;
            this.opacity = template.opacity;
        },

        resetForm() {
            const userCompanyName = @json($user->company_name ?? '');
            this.iso = userCompanyName;
            this.lender = '';
            this.fontSize = {{ config('watermark.defaults.font_size', 15) }};
            this.color = '{{ config('watermark.defaults.color', '#878787') }}';
            this.opacity = {{ config('watermark.defaults.opacity', 10) }};
        }
    }
}
</script>
</x-app-layout>
