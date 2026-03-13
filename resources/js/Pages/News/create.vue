<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

// Tiptap Imports
import { useEditor, EditorContent } from '@tiptap/vue-3';
import { StarterKit } from '@tiptap/starter-kit';
import { Image } from '@tiptap/extension-image';
import { Color } from '@tiptap/extension-color';
import { TextStyle } from '@tiptap/extension-text-style';

const props = defineProps({ news: Object });

const isUploadingThumbnail = ref(false);
const thumbnailPreview = ref(null);

const form = useForm({
    title: '',
    slug: '',
    summary: '',
    content: '', // Đây là nơi lưu mã HTML từ Editor
    thumbnail: '',
    category: 'Tin tức',
    is_published: true,
});

/**
 * CẤU HÌNH TIPTAP EDITOR
 */
const editor = useEditor({
    content: '',
    extensions: [
        StarterKit,
        TextStyle,
        Color,
        Image.configure({
            HTMLAttributes: {
                class: 'rounded-xl shadow-lg max-w-full mx-auto my-6 border-4 border-white',
            },
        }),
    ],
    // Cập nhật giá trị vào form mỗi khi nội dung thay đổi
    onUpdate: ({ editor }) => {
        form.content = editor.getHTML();
    },
});

// Hàm xử lý upload ảnh khi kéo thả hoặc chọn ảnh trong bài viết
const handleContentImageUpload = async (file) => {
    const formData = new FormData();
    formData.append('image', file);
    try {
        const res = await axios.post('https://upload.vtbac.io.vn/upload', formData, {
            headers: { 'x-api-key': 'bacdeptraivl', 'Content-Type': 'multipart/form-data' }
        });
        const url = res.data?.url || res.data?.data?.url;
        if (url) {
            editor.value.chain().focus().setImage({ src: url }).run();
        }
    } catch (err) {
        alert('Không thể upload ảnh vào bài viết!');
    }
};

// Hàm phụ để bấm nút chèn ảnh (ngoài kéo thả)
const triggerContentImage = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) handleContentImageUpload(file);
    };
    input.click();
};

/**
 * XỬ LÝ SLUG VÀ THUMBNAIL (GIỮ LẠI TỪ CŨ)
 */
const generateSlug = (value) => {
    return value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[đĐ]/g, 'd')
        .replace(/([^0-9a-z-\s])/g, '').replace(/(\s+)/g, '-').replace(/-+/g, '-').replace(/^-+|-+$/g, '');
};

watch(() => form.title, (newTitle) => { form.slug = generateSlug(newTitle); });

const handleThumbnailUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    thumbnailPreview.value = URL.createObjectURL(file);
    isUploadingThumbnail.value = true;
    const formData = new FormData();
    formData.append('image', file);
    try {
        const res = await axios.post('https://upload.vtbac.io.vn/upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data', 'x-api-key': 'bacdeptraivl' }
        });
        form.thumbnail = res.data?.url || res.data?.data?.url;
    } finally {
        isUploadingThumbnail.value = false;
    }
};

const submit = () => { form.post(route('news.store')); };

onBeforeUnmount(() => { editor.value?.destroy(); });
</script>

<template>
    <Head title="Thêm tin tức" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">✍️ Soạn thảo bài viết mới</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    
                    <div class="lg:col-span-3 space-y-6">
                        <div class="bg-white p-8 shadow-sm rounded-2xl border border-gray-200">
                            <div class="space-y-6">
                                <div>
                                    <input v-model="form.title" type="text" placeholder="Nhập tiêu đề bài viết tại đây..." 
                                        class="w-full text-3xl font-extrabold border-none focus:ring-0 p-0 placeholder-gray-300" required />
                                    <div class="mt-2 flex items-center text-sm text-gray-400 font-mono">
                                        <span class="bg-gray-100 px-2 py-1 rounded">/news/{{ form.slug }}</span>
                                    </div>
                                </div>

                                <hr class="border-gray-100">
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 block">Tiêu đề bài viết</label>
                                    <textarea v-model="form.title" rows="2" placeholder="Tiêu đề bài viết..." 
                                        class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 py-3 italic text-gray-600"></textarea>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 block">Mô tả ngắn</label>
                                    <textarea v-model="form.summary" rows="2" placeholder="Tóm tắt ngắn gọn nội dung..." 
                                        class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 py-3 italic text-gray-600"></textarea>
                                </div>

                                <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-inner bg-white">
                                    <div v-if="editor" class="bg-gray-50 border-b border-gray-200 p-2 flex flex-wrap gap-1 sticky top-0 z-10">
                                        <button @click.prevent="editor.chain().focus().toggleBold().run()" :class="{ 'bg-indigo-600 text-white': editor.isActive('bold') }" class="toolbar-btn">B</button>
                                        <button @click.prevent="editor.chain().focus().toggleItalic().run()" :class="{ 'bg-indigo-600 text-white': editor.isActive('italic') }" class="toolbar-btn italic">I</button>
                                        <button @click.prevent="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'bg-indigo-600 text-white': editor.isActive('heading', { level: 2 }) }" class="toolbar-btn">H2</button>
                                        <button @click.prevent="editor.chain().focus().toggleBulletList().run()" :class="{ 'bg-indigo-600 text-white': editor.isActive('bulletList') }" class="toolbar-btn">List</button>
                                        <div class="w-px h-6 bg-gray-300 mx-1 self-center"></div>
                                        <button @click.prevent="triggerContentImage" class="toolbar-btn">🖼️ Chèn ảnh</button>
                                        <input type="color" @input="editor.chain().focus().setColor($event.target.value).run()" 
                                            class="w-8 h-8 p-1 cursor-pointer bg-transparent" title="Màu chữ" />
                                    </div>

                                    <div class="p-6 min-h-[500px]">
                                        <editor-content :editor="editor" class="tiptap-content" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white p-6 shadow-sm rounded-2xl border border-gray-200">
                            <label class="block font-bold text-sm text-gray-700 mb-4">Ảnh bìa bài viết</label>
                            <div class="relative group aspect-[4/3] bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl overflow-hidden flex items-center justify-center transition hover:border-indigo-400">
                                <img v-if="thumbnailPreview" :src="thumbnailPreview" class="w-full h-full object-cover" />
                                <div v-else class="text-center px-4">
                                    <div class="bg-white p-3 rounded-full shadow-sm mx-auto w-fit mb-3">
                                        <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"/></svg>
                                    </div>
                                    <span class="text-xs font-bold text-gray-400">Tải ảnh bìa</span>
                                </div>
                                <input type="file" @change="handleThumbnailUpload" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" />
                                <div v-if="isUploadingThumbnail" class="absolute inset-0 bg-white/80 flex items-center justify-center">
                                    <div class="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 shadow-sm rounded-2xl border border-gray-200 space-y-5">
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Chuyên mục</label>
                                <select v-model="form.category" class="w-full border-gray-200 rounded-xl py-3 focus:ring-indigo-500">
                                    <option value="Tin tức">Tin tức</option>
                                    <option value="Sự kiện">Sự kiện</option>
                                    <option value="Khuyến mãi">Khuyến mãi</option>
                                </select>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-sm font-bold text-gray-700">Công khai</span>
                                <input v-model="form.is_published" type="checkbox" class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500" />
                            </div>

                            <button type="submit" :disabled="form.processing || isUploadingThumbnail" 
                                class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 transition disabled:bg-gray-300">
                                {{ form.processing ? 'Đang lưu...' : 'Đăng bài viết' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* CSS cho Toolbar */
.toolbar-btn {
    @apply px-3 py-1.5 rounded-lg text-sm font-bold text-gray-600 hover:bg-gray-200 transition-all;
}

/* CSS cho Tiptap Editor */
.tiptap-content .ProseMirror {
    outline: none;
    font-size: 1.125rem;
    line-height: 1.75;
    color: #374151;
}

.tiptap-content .ProseMirror p.is-editor-empty:first-child::before {
    content: "Bắt đầu viết nội dung bài viết và kéo thả ảnh vào đây...";
    float: left;
    color: #d1d5db;
    pointer-events: none;
    height: 0;
}

/* Kiểu dáng cho các thẻ trong editor */
.tiptap-content .ProseMirror h2 { font-size: 1.875rem; font-weight: 800; margin-top: 1.5rem; margin-bottom: 1rem; }
.tiptap-content .ProseMirror ul { list-style-type: disc; padding-left: 1.5rem; }
</style>