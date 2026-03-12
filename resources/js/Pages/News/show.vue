<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    news: Object
});

// Hàm format ngày tháng cho đẹp
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <Head :title="news.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Xem chi tiết tin tức</h2>
                <div class="flex gap-3">
                   
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8 flex justify-end">
                <Link :href="route('news.edit', news.id)" class="flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-300 shadow-lg hover:shadow-indigo-200 hover:-translate-y-1 text-base font-bold">
                    Chỉnh sửa bài viết
                </Link>
            </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    
                    <div class="relative h-96 w-full">
                        <img :src="news.thumbnail || news.image" class="w-full h-full object-cover" alt="Cover Image" />
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded-full uppercase tracking-wider shadow-lg">
                                {{ news.category }}
                            </span>
                        </div>
                    </div>

                    <div class="p-8 md:p-12">
                        <div class="mb-8 border-b border-gray-100 pb-8">
                            <h1 class="text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                                {{ news.title }}
                            </h1>
                            <div class="flex items-center text-sm text-gray-500 gap-6">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Ngày đăng: {{ formatDate(news.created_at) }}
                                </div>
                                <div class="flex items-center">
                                    <span :class="news.is_published ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'" class="px-2 py-0.5 rounded-md font-bold text-[10px] uppercase">
                                        {{ news.is_published ? 'Đã xuất bản' : 'Bản nháp' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-if="news.summary" class="mb-8 p-4 bg-gray-50 border-l-4 border-indigo-500 italic text-gray-600 text-lg leading-relaxed">
                            "{{ news.summary }}"
                        </div>

                        <div class="prose prose-indigo max-w-none text-gray-700 leading-8 whitespace-pre-line">
                            {{ news.content }}
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-sm text-gray-400 font-mono">
                        <span>Slug: {{ news.slug }}</span>
                        <span>ID: #{{ news.id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Bạn có thể thêm font chữ chuyên cho đọc bài viết ở đây */
.prose {
    font-size: 1.125rem;
    letter-spacing: -0.011em;
}
</style>