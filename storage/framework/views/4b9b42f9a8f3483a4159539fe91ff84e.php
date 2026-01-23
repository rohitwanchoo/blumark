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
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Batch Jobs</h1>
            <p class="text-gray-600 mt-1">View and manage your batch uploads.</p>
        </div>
        <a href="<?php echo e(route('batch.create')); ?>"
           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Batch
        </a>
    </div>

    <?php if($batches->isEmpty()): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No batch jobs yet</h3>
        <p class="text-gray-600 mb-6">Upload multiple PDFs at once to speed up your workflow.</p>
        <a href="<?php echo e(route('batch.create')); ?>"
           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
            Create Your First Batch
        </a>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Files</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-900"><?php echo e($batch->name ?? 'Batch #' . $batch->id); ?></p>
                            <p class="text-sm text-gray-500"><?php echo e($batch->iso); ?> → <?php echo e($batch->lender); ?></p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-900"><?php echo e($batch->watermark_jobs_count); ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?php if($batch->status === 'completed'): ?> bg-green-100 text-green-800
                            <?php elseif($batch->status === 'processing'): ?> bg-blue-100 text-blue-800
                            <?php elseif($batch->status === 'failed'): ?> bg-red-100 text-red-800
                            <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?>">
                            <?php echo e(ucfirst($batch->status)); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <?php echo e($batch->created_at->diffForHumans()); ?>

                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="<?php echo e(route('batch.show', $batch)); ?>" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            View
                        </a>
                        <?php if($batch->status === 'completed'): ?>
                        <a href="<?php echo e(route('batch.download', $batch)); ?>" class="text-green-600 hover:text-green-700 text-sm font-medium">
                            Download
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <?php echo e($batches->links()); ?>

    </div>
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
<?php /**PATH /var/www/html/watermarking/resources/views/batch/index.blade.php ENDPATH**/ ?>