<script setup>
defineProps({
    show: Boolean,
    title: { type: String, default: 'Xác nhận xóa' },
    message: { type: String, default: 'Bạn có chắc chắn muốn xóa mục này? Hành động này không thể hoàn tác.' },
    confirmText: { type: String, default: 'Vâng, xóa nó' },
    cancelText: { type: String, default: 'Hủy bỏ' },
    type: { type: String, default: 'danger' } // danger hoặc warning
});

defineEmits(['close', 'confirm']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" @click="$emit('close')"></div>

            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg animate-in fade-in zoom-in duration-300">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div :class="type === 'danger' ? 'bg-red-100' : 'bg-amber-100'" class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg v-if="type === 'danger'" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <svg v-else class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>

                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-bold leading-6 text-gray-900">{{ title }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">{{ message }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button 
                        type="button" 
                        @click="$emit('confirm')"
                        :class="type === 'danger' ? 'bg-red-600 hover:bg-red-700 shadow-red-100' : 'bg-amber-600 hover:bg-amber-700 shadow-amber-100'"
                        class="inline-flex w-full justify-center rounded-xl px-5 py-2.5 text-sm font-bold text-white shadow-lg transition-all sm:w-auto"
                    >
                        {{ confirmText }}
                    </button>
                    <button 
                        type="button" 
                        @click="$emit('close')"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                    >
                        {{ cancelText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
