<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    news: Object // Dùng cho trường hợp Edit nếu cần
});

const isUploading = ref(false);
const imagePreview = ref(null);

const form = useForm({
    title: '',
    slug: '',
    summary: '',
    content: '',
    thumbnail: '',
    category: 'Tin tức',
    is_published: true,
});

// Hàm chuyển tiếng Việt có dấu thành slug không dấu chuyên nghiệp
const generateSlug = (value) => {
    return value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[đĐ]/g, 'd')
        .replace(/([^0-9a-z-\s])/g, '')
        .replace(/(\s+)/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '');
};

// Lắng nghe sự thay đổi của tiêu đề để tự nhảy slug
watch(() => form.title, (newTitle) => {
    form.slug = generateSlug(newTitle);
});

// Hàm upload ảnh bìa lên API
const handleImageUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    imagePreview.value = URL.createObjectURL(file);
    isUploading.value = true;
    
    const formData = new FormData();
    formData.append('image', file);

    try {
        const res = await axios.post('https://upload.vtbac.io.vn/upload', formData, {
            headers: { 
                'Content-Type': 'multipart/form-data',
                'x-api-key': 'bacdeptraivl' 
            }
        });
        const url = res.data?.url || res.data?.data?.url;
        if (url) form.thumbnail = url;
    } catch (err) {
        alert('Lỗi upload ảnh, vui lòng thử lại!');
    } finally {
        isUploading.value = false;
    }
};

const submit = () => {
    form.post(route('news.store'), {
        onSuccess: () => {
            // Có thể thêm thông báo thành công hoặc chuyển hướng
        }
    });
};
</script>

<template>
    <Head title="Thêm tin tức" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">✍️ Viết tin tức mới</h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-200">
                            <div class="space-y-5">
                                <div>
                                    <label class="block font-bold text-sm text-gray-700 mb-1.5">Tiêu đề bài viết</label>
                                    <input 
                                        v-model="form.title" 
                                        type="text" 
                                        placeholder="Ví dụ: Khai trương chi nhánh mới tại Hà Nội..." 
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5" 
                                        required 
                                    />
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700 mb-1.5">Đường dẫn tĩnh (Slug)</label>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-3 py-2.5 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs font-mono">
                                            /news/
                                        </span>
                                        <input 
                                            v-model="form.slug" 
                                            type="text" 
                                            class="flex-1 border-gray-300 rounded-r-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 text-xs font-mono bg-gray-50" 
                                            placeholder="tieu-de-bai-viet"
                                            required
                                        />
                                    </div>
                                    <p class="mt-1 text-[11px] text-gray-400 italic">* Slug sẽ tự động sinh ra khi bạn gõ tiêu đề bài viết.</p>
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700 mb-1.5">Mô tả ngắn (Tóm tắt)</label>
                                    <textarea v-model="form.summary" rows="3" placeholder="Tóm tắt nội dung để hiển thị trên danh sách tin..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 py-2.5"></textarea>
                                </div>

                                <div>
                                    <label class="block font-bold text-sm text-gray-700 mb-1.5">Nội dung chi tiết</label>
                                    <textarea v-model="form.content" rows="12" placeholder="Nội dung chính của bài viết..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 py-2.5" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-200">
                            <label class="block font-bold text-sm text-gray-700 mb-3">Ảnh bìa (Thumbnail)</label>
                            <div class="relative group aspect-video bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl overflow-hidden flex items-center justify-center transition hover:border-indigo-400">
                                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                                <div v-else class="text-gray-400 text-xs text-center px-4">
                                    <svg class="mx-auto h-12 w-12 mb-2 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    <span class="font-bold text-indigo-600">Click để chọn ảnh</span> hoặc kéo thả vào đây
                                </div>
                                <input type="file" @change="handleImageUpload" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" />
                                <div v-if="isUploading" class="absolute inset-0 bg-white/80 flex items-center justify-center">
                                    <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                </div>
                            </div>
                            <p class="mt-2 text-[11px] text-gray-500 text-center">Định dạng hỗ trợ: JPG, PNG, WEBP (Tối đa 2MB)</p>
                        </div>

                        <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-200 space-y-5">
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-1.5">Chuyên mục</label>
                                <select v-model="form.category" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 py-2.5">
                                    <option value="Tin tức">Tin tức</option>
                                    <option value="Sự kiện">Sự kiện</option>
                                    <option value="Khuyến mãi">Khuyến mãi</option>
                                </select>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <label for="published" class="text-sm font-bold text-gray-700 cursor-pointer">Công khai ngay</label>
                                <input v-model="form.is_published" type="checkbox" id="published" class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer" />
                            </div>

                            <button 
                                type="submit" 
                                :disabled="form.processing || isUploading" 
                                class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-0.5 transition active:scale-95 disabled:bg-gray-400 disabled:shadow-none"
                            >
                                <span v-if="form.processing">Đang xử lý dữ liệu...</span>
                                <span v-else-if="isUploading">Đang tải ảnh lên...</span>
                                <span v-else>Đăng bài viết</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>