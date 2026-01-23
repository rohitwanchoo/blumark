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
            <h1 class="text-2xl font-bold text-gray-900">Enable Two-Factor Authentication</h1>
            <p class="text-gray-500 mt-1">Scan the QR code below with your authenticator app.</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-red-800"><?php echo e($errors->first()); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Step 1: Scan QR Code</h3>
                <p class="text-sm text-gray-500 mt-1">Use an authenticator app like Google Authenticator, Authy, or 1Password.</p>
            </div>

            <div class="p-6">
                <!-- QR Code -->
                <div class="flex flex-col items-center mb-8">
                    <div class="bg-white p-4 rounded-xl border-2 border-gray-200 mb-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo e(urlencode($qrCodeUrl)); ?>" alt="QR Code" class="w-48 h-48">
                    </div>
                    <p class="text-sm text-gray-500 text-center max-w-md">
                        Scan this QR code with your authenticator app, or enter the setup key manually.
                    </p>
                </div>

                <!-- Manual Entry Key -->
                <div class="bg-gray-50 rounded-xl p-4 mb-8">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Setup Key (Manual Entry)</p>
                    <div class="flex items-center justify-between">
                        <code class="text-lg font-mono font-semibold text-gray-900 tracking-wider"><?php echo e($secret); ?></code>
                        <button type="button" onclick="navigator.clipboard.writeText('<?php echo e($secret); ?>')" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Copy
                        </button>
                    </div>
                </div>

                <!-- Confirm Form -->
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Step 2: Verify Setup</h3>
                    <p class="text-sm text-gray-500 mb-4">Enter the 6-digit code from your authenticator app to confirm setup.</p>

                    <form method="POST" action="<?php echo e(route('two-factor.confirm')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-4">
                            <label for="code" class="block font-medium text-sm text-gray-700 mb-1">Verification Code</label>
                            <input id="code" type="text" name="code" required autofocus autocomplete="one-time-code"
                                   class="block w-full max-w-xs rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 px-4 py-3 border text-sm transition-colors font-mono text-lg tracking-widest"
                                   placeholder="000000"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   inputmode="numeric">
                            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-xl font-medium text-sm hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/25">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Confirm & Enable
                            </button>
                            <a href="<?php echo e(route('two-factor.show')); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
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
<?php /**PATH /var/www/html/watermarking/resources/views/auth/two-factor/enable.blade.php ENDPATH**/ ?>