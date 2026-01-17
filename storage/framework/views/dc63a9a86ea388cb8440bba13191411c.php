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
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Job Details
            </h2>
            <a href="<?php echo e(route('jobs.index')); ?>" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; Back to Jobs
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8" x-data="jobStatus()" x-init="startPolling()">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Status Header -->
                <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <?php if($job->isPending() || $job->isProcessing()): ?>
                                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                            <?php elseif($job->isDone()): ?>
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            <?php else: ?>
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900" x-text="statusText">
                                <?php if($job->isPending()): ?>
                                    Waiting in queue...
                                <?php elseif($job->isProcessing()): ?>
                                    Processing your PDF...
                                <?php elseif($job->isDone()): ?>
                                    Watermark complete!
                                <?php else: ?>
                                    Processing failed
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500">
                                Created <?php echo e($job->created_at->diffForHumans()); ?>

                                <?php if($job->processed_at): ?>
                                    &bull; Processed <?php echo e($job->processed_at->diffForHumans()); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($job->getStatusBadgeClass()); ?>" x-text="status">
                        <?php echo e(ucfirst($job->status)); ?>

                    </span>
                </div>

                <!-- File Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Original File</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 break-all"><?php echo e($job->original_filename); ?></p>
                                    <p class="text-xs text-gray-500">
                                        <?php echo e($job->getFormattedFileSize()); ?>

                                        <?php if($job->page_count): ?>
                                            &bull; <?php echo e($job->page_count); ?> pages
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Watermark Settings</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <dl class="grid grid-cols-2 gap-2 text-sm">
                                <dt class="text-gray-500">Type:</dt>
                                <dd class="text-gray-900 font-medium"><?php echo e(ucfirst($job->settings['type'] ?? 'text')); ?></dd>

                                <?php if(($job->settings['type'] ?? 'text') === 'text'): ?>
                                    <dt class="text-gray-500">Text:</dt>
                                    <dd class="text-gray-900 font-medium truncate" title="<?php echo e($job->settings['text'] ?? ''); ?>"><?php echo e($job->settings['text'] ?? 'N/A'); ?></dd>

                                    <dt class="text-gray-500">Font Size:</dt>
                                    <dd class="text-gray-900 font-medium"><?php echo e($job->settings['font_size'] ?? 'N/A'); ?></dd>

                                    <dt class="text-gray-500">Color:</dt>
                                    <dd class="text-gray-900 font-medium flex items-center">
                                        <span class="w-4 h-4 rounded mr-2" style="background-color: <?php echo e($job->settings['color'] ?? '#888888'); ?>"></span>
                                        <?php echo e($job->settings['color'] ?? '#888888'); ?>

                                    </dd>
                                <?php else: ?>
                                    <dt class="text-gray-500">Scale:</dt>
                                    <dd class="text-gray-900 font-medium"><?php echo e($job->settings['scale'] ?? 50); ?>%</dd>
                                <?php endif; ?>

                                <dt class="text-gray-500">Position:</dt>
                                <dd class="text-gray-900 font-medium"><?php echo e(ucfirst($job->settings['position'] ?? 'diagonal')); ?></dd>

                                <dt class="text-gray-500">Opacity:</dt>
                                <dd class="text-gray-900 font-medium"><?php echo e($job->settings['opacity'] ?? 50); ?>%</dd>

                                <dt class="text-gray-500">Rotation:</dt>
                                <dd class="text-gray-900 font-medium"><?php echo e($job->settings['rotation'] ?? 45); ?>°</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <?php if($job->isFailed() && $job->error_message): ?>
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-red-800">Error Details</h4>
                                <p class="mt-1 text-sm text-red-700"><?php echo e($job->error_message); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div x-show="canDownload || <?php echo e($job->isDone() && $job->outputExists() ? 'true' : 'false'); ?>">
                        <a href="<?php echo e(route('jobs.download', $job)); ?>"
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Watermarked PDF
                        </a>
                        <a href="<?php echo e(route('jobs.preview', $job)); ?>" target="_blank"
                           class="ml-3 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview
                        </a>
                    </div>

                    <form method="POST" action="<?php echo e(route('jobs.destroy', $job)); ?>" onsubmit="return confirm('Are you sure you want to delete this job? This action cannot be undone.')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-white border border-red-300 rounded-md font-semibold text-sm text-red-700 uppercase tracking-widest hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function jobStatus() {
            return {
                status: '<?php echo e(ucfirst($job->status)); ?>',
                statusText: '<?php echo e($job->isPending() ? "Waiting in queue..." : ($job->isProcessing() ? "Processing your PDF..." : ($job->isDone() ? "Watermark complete!" : "Processing failed"))); ?>',
                canDownload: <?php echo e($job->isDone() && $job->outputExists() ? 'true' : 'false'); ?>,
                polling: null,

                startPolling() {
                    if ('<?php echo e($job->status); ?>' === 'pending' || '<?php echo e($job->status); ?>' === 'processing') {
                        this.polling = setInterval(() => this.checkStatus(), 3000);
                    }
                },

                async checkStatus() {
                    try {
                        const response = await fetch('<?php echo e(route("jobs.status", $job)); ?>');
                        const data = await response.json();

                        this.status = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        this.canDownload = data.can_download;

                        if (data.status === 'pending') {
                            this.statusText = 'Waiting in queue...';
                        } else if (data.status === 'processing') {
                            this.statusText = 'Processing your PDF...';
                        } else if (data.status === 'done') {
                            this.statusText = 'Watermark complete!';
                            clearInterval(this.polling);
                            location.reload();
                        } else if (data.status === 'failed') {
                            this.statusText = 'Processing failed';
                            clearInterval(this.polling);
                            location.reload();
                        }
                    } catch (error) {
                        console.error('Error checking status:', error);
                    }
                }
            };
        }
    </script>
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
<?php /**PATH /var/www/html/watermarking/resources/views/jobs/show.blade.php ENDPATH**/ ?>