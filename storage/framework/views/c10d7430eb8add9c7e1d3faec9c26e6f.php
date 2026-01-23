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
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Recovery Codes</h1>
            <p class="text-gray-500 mt-1">Store these recovery codes in a secure place.</p>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-green-800"><?php echo e(session('success')); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if(isset($showSetup) && $showSetup): ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6">
                <div class="text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Two-Factor Authentication Not Enabled</h3>
                    <p class="text-gray-500 mb-6">You need to enable two-factor authentication first to view recovery codes.</p>
                    <a href="<?php echo e(route('two-factor.enable')); ?>" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-xl font-medium text-sm hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
                        Enable 2FA
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Important: Save These Codes</h3>
                            <p class="text-sm text-gray-500">Each code can only be used once. Store them securely.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <p class="text-gray-600 mb-6">
                        Recovery codes are used to access your account if you lose access to your authenticator device. Each code can only be used once. Keep these codes in a safe place, such as a password manager.
                    </p>

                    <!-- Recovery Codes Grid -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <div class="grid grid-cols-2 gap-3">
                            <?php $__currentLoopData = $recoveryCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <code class="bg-white px-4 py-2 rounded-lg font-mono text-sm text-gray-800 border border-gray-200">
                                    <?php echo e($code); ?>

                                </code>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" onclick="copyRecoveryCodes()" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copy Codes
                        </button>

                        <button type="button" onclick="downloadRecoveryCodes()" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium text-sm hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download
                        </button>

                        <form method="POST" action="<?php echo e(route('two-factor.recovery-codes.regenerate')); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-amber-100 text-amber-700 rounded-xl font-medium text-sm hover:bg-amber-200 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Regenerate Codes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Back Link -->
        <div class="mt-6">
            <a href="<?php echo e(route('two-factor.show')); ?>" class="text-sm text-primary-600 hover:text-primary-500 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to 2FA Settings
            </a>
        </div>
    </div>

    <?php if(!isset($showSetup) || !$showSetup): ?>
    <script>
        const recoveryCodes = <?php echo json_encode($recoveryCodes, 15, 512) ?>;

        function copyRecoveryCodes() {
            const text = recoveryCodes.join('\n');
            navigator.clipboard.writeText(text).then(() => {
                alert('Recovery codes copied to clipboard!');
            });
        }

        function downloadRecoveryCodes() {
            const text = '<?php echo e(config("app.name")); ?> Recovery Codes\n' +
                        '================================\n\n' +
                        recoveryCodes.join('\n') +
                        '\n\n================================\n' +
                        'Generated: ' + new Date().toISOString() + '\n' +
                        'Each code can only be used once.';

            const blob = new Blob([text], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = '<?php echo e(config("app.name")); ?>-recovery-codes.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
    <?php endif; ?>
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
<?php /**PATH /var/www/html/watermarking/resources/views/auth/two-factor/recovery-codes.blade.php ENDPATH**/ ?>