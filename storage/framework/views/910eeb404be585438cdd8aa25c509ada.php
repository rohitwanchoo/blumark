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
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Submissions</h1>
                <p class="text-gray-500 mt-1">Manage bulk document submissions to lenders</p>
            </div>
            <a href="<?php echo e(route('distributions.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Submission
            </a>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <!-- Submissions List -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <?php if($distributions->isEmpty()): ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No submissions yet</h3>
                    <p class="text-gray-500 mb-4">Create your first submission to send watermarked documents to multiple lenders.</p>
                    <a href="<?php echo e(route('distributions.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create First Submission
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lenders</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Progress</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Created</th>
                                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $distributions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $distribution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-4">
                                        <div class="flex items-center min-w-0">
                                            <div class="flex-shrink-0 h-8 w-8 bg-red-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3 min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900 truncate"><?php echo e(Str::limit($distribution->name ?? $distribution->source_filename, 30)); ?></div>
                                                <div class="text-xs text-gray-500 truncate"><?php echo e(Str::limit($distribution->source_filename, 25)); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <?php
                                            $uniqueLenders = $distribution->items->pluck('lender_id')->unique()->count();
                                            $fileCount = count($distribution->getSourceFilesArray());
                                        ?>
                                        <div class="text-sm text-gray-900"><?php echo e($uniqueLenders); ?></div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo e($fileCount); ?> <?php echo e(Str::plural('file', $fileCount)); ?>

                                        </div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap hidden lg:table-cell">
                                        <div class="w-24">
                                            <div class="flex items-center">
                                                <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="h-2 rounded-full <?php echo e($distribution->failed_count > 0 ? 'bg-yellow-500' : 'bg-green-500'); ?>"
                                                         style="width: <?php echo e($distribution->getProgressPercentage()); ?>%"></div>
                                                </div>
                                                <span class="text-xs text-gray-600"><?php echo e($distribution->getProgressPercentage()); ?>%</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <?php if($distribution->isCompleted()): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Done
                                            </span>
                                        <?php elseif($distribution->isProcessing()): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span class="hidden sm:inline">Processing</span>
                                            </span>
                                        <?php elseif($distribution->isFailed()): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Failed
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                        <div><?php echo e($distribution->created_at->timezone(auth()->user()->timezone ?? 'UTC')->format('M d, Y')); ?></div>
                                        <div class="text-xs text-gray-400"><?php echo e($distribution->created_at->timezone(auth()->user()->timezone ?? 'UTC')->format('h:i A')); ?></div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?php echo e(route('distributions.show', $distribution)); ?>" class="text-primary-600 hover:text-primary-900">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <?php if($distributions->hasPages()): ?>
                    <div class="px-6 py-4 border-t border-gray-200">
                        <?php echo e($distributions->links()); ?>

                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
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
<?php /**PATH /var/www/html/watermarking/resources/views/distributions/index.blade.php ENDPATH**/ ?>