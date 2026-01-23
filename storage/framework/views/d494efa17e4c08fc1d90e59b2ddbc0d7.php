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
<div class="px-4 sm:px-6 lg:px-8" x-data="templatesManager()">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Saved Templates</h1>
            <p class="text-gray-600 mt-1">Save and reuse your frequent ISO/Lender combinations.</p>
        </div>
        <button @click="openModal()"
                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Template
        </button>
    </div>

    <?php if($templates->isEmpty()): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No templates yet</h3>
        <p class="text-gray-600 mb-6">Create templates to speed up your watermarking workflow.</p>
        <button @click="openModal()"
                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
            Create Your First Template
        </button>
    </div>
    <?php else: ?>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate"><?php echo e($template->name); ?></h3>
                    <?php if($template->is_default): ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-700 mt-1">
                        Default
                    </span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center space-x-1 ml-2">
                    <button @click="openModal(<?php echo e($template->toJson()); ?>)"
                            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <form action="<?php echo e(route('templates.destroy', $template)); ?>" method="POST" class="inline"
                          onsubmit="return confirm('Delete this template?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <div class="space-y-2 text-sm">
                <div class="flex items-center">
                    <span class="text-gray-500 w-16">ISO:</span>
                    <span class="text-gray-900 font-medium truncate"><?php echo e($template->iso); ?></span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-500 w-16">Lender:</span>
                    <span class="text-gray-900 font-medium truncate"><?php echo e($template->lender); ?></span>
                </div>
                <?php if($template->lender_email): ?>
                <div class="flex items-center">
                    <span class="text-gray-500 w-16">Email:</span>
                    <span class="text-gray-600 truncate"><?php echo e($template->lender_email); ?></span>
                </div>
                <?php endif; ?>
                <div class="flex items-center">
                    <span class="text-gray-500 w-16">Position:</span>
                    <span class="text-gray-900 truncate"><?php echo e(ucwords(str_replace('-', ' ', $template->position ?? 'diagonal'))); ?></span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Used <?php echo e($template->usage_count); ?> times</span>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-1">
                        <span class="w-4 h-4 rounded" style="background-color: <?php echo e($template->color); ?>"></span>
                        <span><?php echo e($template->opacity); ?>%</span>
                    </div>
                    <span><?php echo e($template->rotation ?? 45); ?>°</span>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <!-- Template Modal -->
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="closeModal()"></div>

            <div class="relative bg-white rounded-2xl shadow-xl max-w-6xl w-full p-6"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">
                <h2 class="text-xl font-bold text-gray-900 mb-4" x-text="editingId ? 'Edit Template' : 'New Template'"></h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Form Section (Left) -->
                    <div class="w-full">
                        <form :action="editingId ? '/templates/' + editingId : '<?php echo e(route('templates.store')); ?>'"
                              method="POST" @submit="handleSubmit">
                            <?php echo csrf_field(); ?>
                            <template x-if="editingId">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Template Name *</label>
                            <input type="text" name="name" x-model="form.name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                                   placeholder="e.g., FastFund Capital">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ISO Name *</label>
                                <input type="text" name="iso" x-model="form.iso" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lender Name *</label>
                                <input type="text" name="lender" x-model="form.lender" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lender Email (optional)</label>
                            <input type="email" name="lender_email" x-model="form.lender_email"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                                   placeholder="For quick email delivery">
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Font Size</label>
                                <input type="number" name="font_size" x-model="form.font_size"
                                       min="8" max="48" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                <input type="color" name="color" x-model="form.color"
                                       class="w-full h-10 px-1 py-1 border border-gray-300 rounded-lg cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Opacity</label>
                                <input type="number" name="opacity" x-model="form.opacity"
                                       min="1" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                <select name="position" x-model="form.position"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                                    <option value="diagonal">Diagonal (Center)</option>
                                    <option value="scattered">Scattered (Multiple Random)</option>
                                    <option value="top-left">Top Left</option>
                                    <option value="top-center">Top Center</option>
                                    <option value="top-right">Top Right</option>
                                    <option value="center">Center</option>
                                    <option value="bottom-left">Bottom Left</option>
                                    <option value="bottom-center">Bottom Center</option>
                                    <option value="bottom-right">Bottom Right</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rotation (degrees)</label>
                                <input type="number" name="rotation" x-model="form.rotation"
                                       min="0" max="360" step="45"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>

                                <label class="flex items-center">
                                    <input type="checkbox" name="is_default" x-model="form.is_default"
                                           class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                    <span class="ml-2 text-sm text-gray-700">Set as default template</span>
                                </label>

                                <div class="mt-6 flex justify-end space-x-3">
                                    <button type="button" @click="closeModal()"
                                            class="px-4 py-2 text-gray-700 hover:text-gray-900">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg">
                                        <span x-text="editingId ? 'Update' : 'Create'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Preview Section (Right) -->
                    <div class="w-full">
                        <div class="sticky top-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Live Preview</h3>
                            <div class="relative bg-white border-2 border-gray-300 rounded-lg shadow-sm" style="height: 500px; width: 100%; overflow: hidden;">
                                <!-- Background to represent PDF page -->
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100"></div>

                                <!-- Single watermark preview (for non-scattered positions) -->
                                <template x-if="form.position !== 'scattered'">
                                    <div class="absolute"
                                         :style="{
                                             ...getPreviewPosition(form.position),
                                             transform: `rotate(${form.rotation}deg)`,
                                             color: form.color,
                                             opacity: form.opacity / 100,
                                             fontSize: '12px',
                                             fontWeight: 'bold',
                                             whiteSpace: 'nowrap',
                                             transformOrigin: 'center'
                                         }">
                                        ISO: <span x-text="form.iso || 'Sample ISO'"></span> | Lender: <span x-text="form.lender || 'Sample Lender'"></span>
                                    </div>
                                </template>

                                <!-- Scattered watermarks preview -->
                                <template x-if="form.position === 'scattered'">
                                    <div>
                                        <!-- Top Left -->
                                        <div class="absolute"
                                             :style="{
                                                 left: '33.33%',
                                                 top: '10%',
                                                 transform: `translate(-50%, -50%) rotate(${form.rotation}deg)`,
                                                 color: form.color,
                                                 opacity: form.opacity / 100,
                                                 fontSize: '9px',
                                                 fontWeight: 'bold',
                                                 whiteSpace: 'nowrap',
                                                 transformOrigin: 'center'
                                             }">
                                            ISO: <span x-text="form.iso || 'ISO'"></span> | <span x-text="form.lender || 'Lender'"></span>
                                        </div>

                                        <!-- Top Right -->
                                        <div class="absolute"
                                             :style="{
                                                 left: '66.67%',
                                                 top: '10%',
                                                 transform: `translate(-50%, -50%) rotate(${form.rotation}deg)`,
                                                 color: form.color,
                                                 opacity: form.opacity / 100,
                                                 fontSize: '9px',
                                                 fontWeight: 'bold',
                                                 whiteSpace: 'nowrap',
                                                 transformOrigin: 'center'
                                             }">
                                            ISO: <span x-text="form.iso || 'ISO'"></span> | <span x-text="form.lender || 'Lender'"></span>
                                        </div>

                                        <!-- Middle Left -->
                                        <div class="absolute"
                                             :style="{
                                                 left: '20%',
                                                 top: '50%',
                                                 transform: `translate(-50%, -50%) rotate(${form.rotation}deg)`,
                                                 color: form.color,
                                                 opacity: form.opacity / 100,
                                                 fontSize: '9px',
                                                 fontWeight: 'bold',
                                                 whiteSpace: 'nowrap',
                                                 transformOrigin: 'center'
                                             }">
                                            ISO: <span x-text="form.iso || 'ISO'"></span> | <span x-text="form.lender || 'Lender'"></span>
                                        </div>

                                        <!-- Middle Center -->
                                        <div class="absolute"
                                             :style="{
                                                 left: '50%',
                                                 top: '50%',
                                                 transform: `translate(-50%, -50%) rotate(${form.rotation}deg)`,
                                                 color: form.color,
                                                 opacity: form.opacity / 100,
                                                 fontSize: '9px',
                                                 fontWeight: 'bold',
                                                 whiteSpace: 'nowrap',
                                                 transformOrigin: 'center'
                                             }">
                                            ISO: <span x-text="form.iso || 'ISO'"></span> | <span x-text="form.lender || 'Lender'"></span>
                                        </div>

                                        <!-- Middle Right -->
                                        <div class="absolute"
                                             :style="{
                                                 left: '80%',
                                                 top: '50%',
                                                 transform: `translate(-50%, -50%) rotate(${form.rotation}deg)`,
                                                 color: form.color,
                                                 opacity: form.opacity / 100,
                                                 fontSize: '9px',
                                                 fontWeight: 'bold',
                                                 whiteSpace: 'nowrap',
                                                 transformOrigin: 'center'
                                             }">
                                            ISO: <span x-text="form.iso || 'ISO'"></span> | <span x-text="form.lender || 'Lender'"></span>
                                        </div>

                                        <!-- Bottom Left -->
                                        <div class="absolute"
                                             :style="{
                                                 left: '20%',
                                                 top: '90%',
                                                 transform: `translate(-50%, -50%) rotate(${form.rotation}deg)`,
                                                 color: form.color,
                                                 opacity: form.opacity / 100,
                                                 fontSize: '9px',
                                                 fontWeight: 'bold',
                                                 whiteSpace: 'nowrap',
                                                 transformOrigin: 'center'
                                             }">
                                            ISO: <span x-text="form.iso || 'ISO'"></span> | <span x-text="form.lender || 'Lender'"></span>
                                        </div>

                                        <!-- Bottom Center -->
                                        <div class="absolute"
                                             :style="{
                                                 left: '50%',
                                                 top: '90%',
                                                 transform: `translate(-50%, -50%) rotate(${form.rotation}deg)`,
                                                 color: form.color,
                                                 opacity: form.opacity / 100,
                                                 fontSize: '9px',
                                                 fontWeight: 'bold',
                                                 whiteSpace: 'nowrap',
                                                 transformOrigin: 'center'
                                             }">
                                            ISO: <span x-text="form.iso || 'ISO'"></span> | <span x-text="form.lender || 'Lender'"></span>
                                        </div>

                                        <!-- Bottom Right -->
                                        <div class="absolute"
                                             :style="{
                                                 left: '80%',
                                                 top: '90%',
                                                 transform: `translate(-50%, -50%) rotate(${form.rotation}deg)`,
                                                 color: form.color,
                                                 opacity: form.opacity / 100,
                                                 fontSize: '9px',
                                                 fontWeight: 'bold',
                                                 whiteSpace: 'nowrap',
                                                 transformOrigin: 'center'
                                             }">
                                            ISO: <span x-text="form.iso || 'ISO'"></span> | <span x-text="form.lender || 'Lender'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function templatesManager() {
    return {
        showModal: false,
        editingId: null,
        form: {
            name: '',
            iso: '',
            lender: '',
            lender_email: '',
            font_size: <?php echo e(config('watermark.defaults.font_size', 15)); ?>,
            color: '<?php echo e(config('watermark.defaults.color', '#878787')); ?>',
            opacity: <?php echo e(config('watermark.defaults.opacity', 10)); ?>,
            position: '<?php echo e(config('watermark.defaults.position', 'diagonal')); ?>',
            rotation: <?php echo e(config('watermark.defaults.rotation', 45)); ?>,
            is_default: false
        },

        openModal(template = null) {
            if (template) {
                this.editingId = template.id;
                this.form = {
                    name: template.name,
                    iso: template.iso,
                    lender: template.lender,
                    lender_email: template.lender_email || '',
                    font_size: template.font_size,
                    color: template.color,
                    opacity: template.opacity,
                    position: template.position || 'diagonal',
                    rotation: template.rotation || 45,
                    is_default: template.is_default
                };
            } else {
                this.editingId = null;
                this.form = {
                    name: '',
                    iso: '',
                    lender: '',
                    lender_email: '',
                    font_size: <?php echo e(config('watermark.defaults.font_size', 15)); ?>,
                    color: '<?php echo e(config('watermark.defaults.color', '#878787')); ?>',
                    opacity: <?php echo e(config('watermark.defaults.opacity', 10)); ?>,
                    position: '<?php echo e(config('watermark.defaults.position', 'diagonal')); ?>',
                    rotation: <?php echo e(config('watermark.defaults.rotation', 45)); ?>,
                    is_default: false
                };
            }
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingId = null;
        },

        handleSubmit(e) {
            // Form will submit normally
        },

        getPreviewPosition(position) {
            const padding = 10; // pixels from edge

            switch(position) {
                case 'top-left':
                    return { left: padding + 'px', top: padding + 'px' };
                case 'top-center':
                    return { left: '50%', top: padding + 'px', transform: 'translateX(-50%)' };
                case 'top-right':
                    return { right: padding + 'px', top: padding + 'px' };
                case 'center':
                    return { left: '50%', top: '50%', transform: 'translate(-50%, -50%)' };
                case 'bottom-left':
                    return { left: padding + 'px', bottom: padding + 'px' };
                case 'bottom-center':
                    return { left: '50%', bottom: padding + 'px', transform: 'translateX(-50%)' };
                case 'bottom-right':
                    return { right: padding + 'px', bottom: padding + 'px' };
                case 'diagonal':
                default:
                    return { left: '50%', top: '50%', transform: 'translate(-50%, -50%)' };
            }
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
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
<?php /**PATH /var/www/html/watermarking/resources/views/templates/index.blade.php ENDPATH**/ ?>