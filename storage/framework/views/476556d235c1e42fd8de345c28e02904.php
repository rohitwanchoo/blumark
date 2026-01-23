<?php if (isset($component)) { $__componentOriginal4619374cef299e94fd7263111d0abc69 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4619374cef299e94fd7263111d0abc69 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <a href="<?php echo e(route('distributions.index')); ?>" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Submissions
            </a>
            <h1 class="text-2xl font-bold text-gray-900">New Submission</h1>
            <p class="text-gray-500 mt-1">Send watermarked documents to multiple lenders</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if(!$hasIsoName): ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">ISO/Company Name Required</h3>
                <p class="text-gray-500 mb-4">Please set your ISO/Company name in your profile before creating submissions. This name will appear on all watermarked documents.</p>
                <a href="<?php echo e(route('profile.edit')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Update Profile
                </a>
            </div>
        <?php elseif($lenders->isEmpty()): ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No lenders available</h3>
                <p class="text-gray-500 mb-4">You need to add lenders before creating a submission.</p>
                <a href="<?php echo e(route('lenders.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Your First Lender
                </a>
            </div>
        <?php else: ?>
            <form action="<?php echo e(route('distributions.store')); ?>" method="POST" enctype="multipart/form-data" x-data="distributionForm()" class="space-y-6">
                <?php echo csrf_field(); ?>

                <!-- PDF Upload -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Documents</h3>
                        <p class="text-sm text-gray-500 mt-1">Upload one or more PDF files to Send to Lenders</p>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="<?php echo e(old('name')); ?>"
                                   placeholder="e.g., Q1 2026 Loan Package"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PDF Files <span class="text-red-500">*</span></label>
                            <div class="border-2 border-dashed rounded-xl p-8 text-center transition-all duration-200 cursor-pointer"
                                 :class="files.length > 0 ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-primary-300 hover:bg-gray-50'"
                                 @click="$refs.fileInput.click()"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @drop.prevent="handleDrop($event)">
                                <input type="file"
                                       name="files[]"
                                       x-ref="fileInput"
                                       accept=".pdf"
                                       multiple
                                       class="hidden"
                                       @change="handleFileSelect($event)">
                                <template x-if="files.length === 0">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700">Drop PDF files here or click to browse</p>
                                        <p class="text-xs text-gray-500 mt-1">Upload multiple PDFs - Maximum <?php echo e(config('watermark.max_upload_mb')); ?>MB per file</p>
                                    </div>
                                </template>
                            </div>

                            <!-- File List -->
                            <template x-if="files.length > 0">
                                <div class="mt-4 space-y-2">
                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                        <span x-text="files.length + ' file(s) selected'"></span>
                                        <button type="button" @click="clearAllFiles()" class="text-red-600 hover:text-red-800">
                                            Clear All
                                        </button>
                                    </div>
                                    <template x-for="(file, index) in files" :key="index">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 bg-red-100 rounded flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900" x-text="file.name"></p>
                                                    <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                                                </div>
                                            </div>
                                            <button type="button" @click="removeFile(index)" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Select Lenders -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Select Lenders</h3>
                        <p class="text-sm text-gray-500 mt-1">Choose which lenders should receive these documents</p>
                    </div>
                    <div class="p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox"
                                       @change="toggleAll($event)"
                                       :checked="selectedLenders.length === <?php echo e($lenders->count()); ?>"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-700">Select All</span>
                            </label>
                            <span class="text-sm text-gray-500" x-text="selectedLenders.length + ' selected'"></span>
                        </div>

                        <div class="border border-gray-200 rounded-lg divide-y divide-gray-200 max-h-96 overflow-y-auto">
                            <?php $__currentLoopData = $lenders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center p-4 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox"
                                           name="lender_ids[]"
                                           value="<?php echo e($lender->id); ?>"
                                           x-model="selectedLenders"
                                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($lender->company_name); ?></p>
                                        <p class="text-sm text-gray-500">
                                            <?php echo e($lender->email); ?>

                                            <?php if($lender->first_name || $lender->last_name): ?>
                                                - <?php echo e($lender->full_name); ?>

                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                <!-- Watermark Settings -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Watermark Settings</h3>
                        <p class="text-sm text-gray-500 mt-1">Customize how the watermark appears</p>
                    </div>
                    <div class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Each document will be watermarked with your company name as the ISO and the lender's company name as the Lender.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label for="font_size" class="block text-sm font-medium text-gray-700 mb-1">Font Size</label>
                                <input type="number"
                                       name="font_size"
                                       id="font_size"
                                       value="<?php echo e(old('font_size', config('watermark.defaults.font_size', 15))); ?>"
                                       min="8"
                                       max="72"
                                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                            </div>
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color"
                                           name="color"
                                           id="color"
                                           value="<?php echo e(old('color', config('watermark.defaults.color', '#878787'))); ?>"
                                           class="h-10 w-14 rounded border-gray-300 cursor-pointer">
                                    <input type="text"
                                           x-ref="colorText"
                                           value="<?php echo e(old('color', config('watermark.defaults.color', '#878787'))); ?>"
                                           class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 focus:border-primary-500 focus:ring-primary-500 text-sm"
                                           @input="$refs.colorText.previousElementSibling.value = $refs.colorText.value">
                                </div>
                            </div>
                            <div>
                                <label for="opacity" class="block text-sm font-medium text-gray-700 mb-1">Opacity (%)</label>
                                <input type="number"
                                       name="opacity"
                                       id="opacity"
                                       value="<?php echo e(old('opacity', config('watermark.defaults.opacity', 10))); ?>"
                                       min="1"
                                       max="100"
                                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-primary-500 focus:ring-primary-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Template -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Email Template</h3>
                        <p class="text-sm text-gray-500 mt-1">Choose a template for emails sent to lenders</p>
                    </div>
                    <div class="p-6">
                        <?php if($emailTemplates->isEmpty()): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center text-gray-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm">No email templates created yet</span>
                                </div>
                                <a href="<?php echo e(route('email-templates.create')); ?>" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                                    Create Template
                                </a>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Default system email will be used if no template is selected.</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="email_template_id" value="" checked class="text-primary-600 focus:ring-primary-500">
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Default System Email</p>
                                        <p class="text-xs text-gray-500">Uses the standard email format</p>
                                    </div>
                                </label>
                                <?php $__currentLoopData = $emailTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="email_template_id" value="<?php echo e($template->id); ?>" class="text-primary-600 focus:ring-primary-500">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-gray-900"><?php echo e($template->name); ?></p>
                                                <?php if($template->is_default): ?>
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Default</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-xs text-gray-500">Subject: <?php echo e(Str::limit($template->subject, 50)); ?></p>
                                        </div>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="mt-3 text-right">
                                <a href="<?php echo e(route('email-templates.index')); ?>" class="text-sm text-primary-600 hover:text-primary-800">
                                    Manage Templates
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SMTP Settings -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Email Server (SMTP)</h3>
                        <p class="text-sm text-gray-500 mt-1">Choose which SMTP server to use for sending emails</p>
                    </div>
                    <div class="p-6">
                        <?php if($smtpSettings->isEmpty()): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center text-gray-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm">No SMTP settings configured yet</span>
                                </div>
                                <a href="<?php echo e(route('smtp-settings.index')); ?>" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                                    Configure SMTP
                                </a>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Default system mail server will be used if no SMTP is selected.</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="smtp_setting_id" value="" checked class="text-primary-600 focus:ring-primary-500">
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Default System Mail</p>
                                        <p class="text-xs text-gray-500">Uses the default mail server configured in the system</p>
                                    </div>
                                </label>
                                <?php $__currentLoopData = $smtpSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $smtp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio"
                                               name="smtp_setting_id"
                                               value="<?php echo e($smtp->id); ?>"
                                               <?php echo e($smtp->is_active ? 'checked' : ''); ?>

                                               class="text-primary-600 focus:ring-primary-500">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-gray-900"><?php echo e($smtp->name); ?></p>
                                                <?php if($smtp->is_active): ?>
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                                                <?php endif; ?>
                                                <?php if($smtp->provider): ?>
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"><?php echo e(ucfirst($smtp->provider)); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-xs text-gray-500"><?php echo e($smtp->host); ?>:<?php echo e($smtp->port); ?> - From: <?php echo e($smtp->from_email); ?></p>
                                        </div>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="mt-3 text-right">
                                <a href="<?php echo e(route('smtp-settings.index')); ?>" class="text-sm text-primary-600 hover:text-primary-800">
                                    Manage SMTP Settings
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Summary -->
                <template x-if="files.length > 0 && selectedLenders.length > 0">
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p class="ml-3 text-sm text-purple-700">
                                <span class="font-semibold" x-text="files.length"></span>
                                <span x-text="files.length === 1 ? 'document' : 'documents'"></span> will be sent to
                                <span class="font-semibold" x-text="selectedLenders.length"></span>
                                <span x-text="selectedLenders.length === 1 ? 'lender' : 'lenders'"></span>
                            </p>
                        </div>
                    </div>
                </template>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="<?php echo e(route('distributions.index')); ?>" class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium">
                        Cancel
                    </a>
                    <button type="submit"
                            :disabled="files.length === 0 || selectedLenders.length === 0"
                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors">
                        <span x-show="files.length > 0 && selectedLenders.length > 0">Create Submission</span>
                        <span x-show="files.length === 0 || selectedLenders.length === 0">Select Files & Lenders</span>
                    </button>
                </div>
            </form>

            <?php
                $lenderIds = $lenders->pluck('id')->map(fn($id) => (string)$id)->values()->toArray();
            ?>
            <script>
                function distributionForm() {
                    return {
                        files: [],
                        selectedLenders: [],
                        isDragging: false,
                        handleFileSelect(event) {
                            const newFiles = Array.from(event.target.files).filter(f => f.type === 'application/pdf');
                            this.files = [...this.files, ...newFiles];
                            this.updateFileInput();
                        },
                        handleDrop(event) {
                            this.isDragging = false;
                            const newFiles = Array.from(event.dataTransfer.files).filter(f => f.type === 'application/pdf');
                            this.files = [...this.files, ...newFiles];
                            this.updateFileInput();
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                            this.updateFileInput();
                        },
                        clearAllFiles() {
                            this.files = [];
                            this.$refs.fileInput.value = '';
                        },
                        updateFileInput() {
                            // Create a new DataTransfer to update the file input
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            this.$refs.fileInput.files = dt.files;
                        },
                        formatFileSize(bytes) {
                            if (bytes === 0) return '0 Bytes';
                            const k = 1024;
                            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                            const i = Math.floor(Math.log(bytes) / Math.log(k));
                            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                        },
                        toggleAll(event) {
                            if (event.target.checked) {
                                this.selectedLenders = <?php echo json_encode($lenderIds, 15, 512) ?>;
                            } else {
                                this.selectedLenders = [];
                            }
                        }
                    }
                }
            </script>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $attributes = $__attributesOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__attributesOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $component = $__componentOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__componentOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/watermarking/resources/views/distributions/create.blade.php ENDPATH**/ ?>