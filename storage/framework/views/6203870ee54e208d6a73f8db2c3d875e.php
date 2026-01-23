<?php if (isset($component)) { $__componentOriginale0f1cdd055772eb1d4a99981c240763e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale0f1cdd055772eb1d4a99981c240763e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
     <?php $__env->endSlot(); ?>

    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_users'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Jobs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Jobs</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_jobs'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Credits -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Credits Purchased</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($stats['total_credits_purchased'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Est. Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">$<?php echo e(number_format($stats['total_revenue'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jobs by Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Jobs by Status</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-3xl font-bold text-yellow-600"><?php echo e($jobsByStatus['pending']); ?></p>
                    <p class="text-sm text-yellow-700">Pending</p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-3xl font-bold text-blue-600"><?php echo e($jobsByStatus['processing']); ?></p>
                    <p class="text-sm text-blue-700">Processing</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-3xl font-bold text-green-600"><?php echo e($jobsByStatus['done']); ?></p>
                    <p class="text-sm text-green-700">Completed</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-3xl font-bold text-red-600"><?php echo e($jobsByStatus['failed']); ?></p>
                    <p class="text-sm text-red-700">Failed</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Users</h2>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="text-sm text-indigo-600 hover:text-indigo-500">View all</a>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold">
                                <?php echo e(substr($user->name, 0, 1)); ?>

                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($user->email); ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($user->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : ($user->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800')); ?>">
                                <?php echo e($user->getRoleLabel()); ?>

                            </span>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e($user->created_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-6 py-8 text-center text-gray-500">
                        No users yet.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Jobs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Jobs</h2>
                    <a href="<?php echo e(route('admin.jobs.index')); ?>" class="text-sm text-indigo-600 hover:text-indigo-500">View all</a>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $recentJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center min-w-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="ml-3 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate"><?php echo e($job->original_filename); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($job->user->name ?? 'Unknown'); ?></p>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($job->getStatusBadgeClass()); ?>">
                                <?php echo e(ucfirst($job->status)); ?>

                            </span>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e($job->created_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-6 py-8 text-center text-gray-500">
                        No jobs yet.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Admin Activity</h2>
                <a href="<?php echo e(route('admin.activity')); ?>" class="text-sm text-indigo-600 hover:text-indigo-500">View all</a>
            </div>
            <div class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center min-w-0">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold flex-shrink-0">
                            <?php echo e(substr($log->admin->name ?? '?', 0, 1)); ?>

                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="text-sm font-medium text-gray-900"><?php echo e($log->admin->name ?? 'Unknown'); ?></p>
                            <p class="text-xs text-gray-500 truncate"><?php echo e($log->description); ?></p>
                        </div>
                    </div>
                    <div class="text-right ml-4 flex-shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($log->getActionBadgeClass()); ?>">
                            <?php echo e($log->getActionLabel()); ?>

                        </span>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e($log->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-8 text-center text-gray-500">
                    No activity yet.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale0f1cdd055772eb1d4a99981c240763e)): ?>
<?php $attributes = $__attributesOriginale0f1cdd055772eb1d4a99981c240763e; ?>
<?php unset($__attributesOriginale0f1cdd055772eb1d4a99981c240763e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale0f1cdd055772eb1d4a99981c240763e)): ?>
<?php $component = $__componentOriginale0f1cdd055772eb1d4a99981c240763e; ?>
<?php unset($__componentOriginale0f1cdd055772eb1d4a99981c240763e); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/watermarking/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>