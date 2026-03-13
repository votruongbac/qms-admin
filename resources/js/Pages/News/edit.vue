<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref, watch, onBeforeUnmount } from "vue";
import axios from "axios";

// Tiptap Imports - Lưu ý dùng dấu { } để tránh lỗi export named
import { useEditor, EditorContent } from "@tiptap/vue-3";
import { StarterKit } from "@tiptap/starter-kit";
import { Image } from "@tiptap/extension-image";
import { Color } from "@tiptap/extension-color";
import { TextStyle } from "@tiptap/extension-text-style";

const props = defineProps({
  news: Object,
});

const isUploadingThumbnail = ref(false);
const thumbnailPreview = ref(props.news.thumbnail || props.news.image);

const form = useForm({
  title: props.news.title ?? "",
  slug: props.news.slug ?? "",
  summary: props.news.summary ?? "",
  content: props.news.content ?? "",
  thumbnail: props.news.thumbnail ?? "",
  category: props.news.category ?? "Tin tức",
  is_published: props.news.is_published == 1,
});

/**
 * CẤU HÌNH TIPTAP EDITOR
 */
const editor = useEditor({
  content: props.news.content, // Đổ dữ liệu HTML cũ vào đây
  extensions: [
    StarterKit,
    TextStyle,
    Color,
    Image.configure({
      HTMLAttributes: {
        class:
          "rounded-xl shadow-lg max-w-full mx-auto my-6 border-4 border-white",
      },
    }),
  ],
  onUpdate: ({ editor }) => {
    form.content = editor.getHTML();
  },
});

// Hàm upload ảnh trực tiếp trong nội dung
const triggerContentImage = () => {
  const input = document.createElement("input");
  input.type = "file";
  input.accept = "image/*";
  input.onchange = async (e) => {
    const file = e.target.files[0];
    if (file) {
      const formData = new FormData();
      formData.append("image", file);
      try {
        const res = await axios.post(
          "https://upload.vtbac.io.vn/upload",
          formData,
          {
            headers: {
              "x-api-key": "bacdeptraivl",
              "Content-Type": "multipart/form-data",
            },
          }
        );
        const url = res.data?.url || res.data?.data?.url;
        if (url) editor.value.chain().focus().setImage({ src: url }).run();
      } catch (err) {
        alert("Lỗi upload ảnh!");
      }
    }
  };
  input.click();
};

/**
 * XỬ LÝ SLUG VÀ THUMBNAIL
 */
const generateSlug = (value) => {
  return value
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[đĐ]/g, "d")
    .replace(/([^0-9a-z-\s])/g, "")
    .replace(/(\s+)/g, "-")
    .replace(/-+/g, "-")
    .replace(/^-+|-+$/g, "");
};

// Chỉ tự động đổi slug nếu người dùng muốn (tùy chọn)
// watch(() => form.title, (newTitle) => { form.slug = generateSlug(newTitle); });

const handleThumbnailUpload = async (e) => {
  const file = e.target.files[0];
  if (!file) return;
  thumbnailPreview.value = URL.createObjectURL(file);
  isUploadingThumbnail.value = true;
  const formData = new FormData();
  formData.append("image", file);
  try {
    const res = await axios.post(
      "https://upload.vtbac.io.vn/upload",
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
          "x-api-key": "bacdeptraivl",
        },
      }
    );
    form.thumbnail = res.data?.url || res.data?.data?.url;
  } finally {
    isUploadingThumbnail.value = false;
  }
};

const submit = () => {
  // Sử dụng phương thức PUT để cập nhật
  form.put(route("news.update", props.news.id));
};

onBeforeUnmount(() => {
  editor.value?.destroy();
});
</script>

<template>
  <Head title="Chỉnh sửa tin tức" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          📝 Chỉnh sửa bài viết
        </h2>
        <Link
          :href="route('news.show', news.id)"
          class="text-sm text-indigo-600 hover:underline"
          >Xem bản hiện tại</Link
        >
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form
          @submit.prevent="submit"
          class="grid grid-cols-1 lg:grid-cols-4 gap-8"
        >
          <div class="lg:col-span-3 space-y-6">
            <div
              class="bg-white p-8 shadow-sm rounded-2xl border border-gray-200"
            >
              <div class="space-y-6">
                <div>
                  <label
                    class="text-xs font-bold text-gray-400 uppercase mb-2 block"
                    >Tiêu đề bài viết</label
                  >
                  <input
                    v-model="form.title"
                    type="text"
                    class="w-full text-2xl font-bold border-gray-200 rounded-xl focus:ring-indigo-500"
                    required
                  />
                  <div
                    class="mt-2 text-sm text-gray-400 font-mono flex items-center gap-2"
                  >
                    <span>Slug:</span>
                    <input
                      v-model="form.slug"
                      class="bg-gray-50 border-none p-1 rounded text-xs w-full focus:ring-0"
                    />
                  </div>
                </div>

                <div>
                  <label
                    class="text-xs font-bold text-gray-400 uppercase mb-2 block"
                    >Tóm tắt nội dung</label
                  >
                  <textarea
                    v-model="form.summary"
                    rows="2"
                    class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 italic text-gray-600"
                  ></textarea>
                </div>

                <div
                  class="border border-gray-200 rounded-2xl overflow-hidden shadow-inner"
                >
                  <div
                    v-if="editor"
                    class="bg-gray-50 border-b border-gray-200 p-2 flex flex-wrap gap-1 sticky top-0 z-10"
                  >
                    <button
                      @click.prevent="editor.chain().focus().toggleBold().run()"
                      :class="{
                        'bg-indigo-600 text-white': editor.isActive('bold'),
                      }"
                      class="toolbar-btn"
                    >
                      B
                    </button>
                    <button
                      @click.prevent="
                        editor.chain().focus().toggleItalic().run()
                      "
                      :class="{
                        'bg-indigo-600 text-white': editor.isActive('italic'),
                      }"
                      class="toolbar-btn italic"
                    >
                      I
                    </button>
                    <button
                      @click.prevent="
                        editor.chain().focus().toggleHeading({ level: 2 }).run()
                      "
                      :class="{
                        'bg-indigo-600 text-white': editor.isActive('heading', {
                          level: 2,
                        }),
                      }"
                      class="toolbar-btn"
                    >
                      H2
                    </button>
                    <button
                      @click.prevent="triggerContentImage"
                      class="toolbar-btn"
                    >
                      🖼️ Ảnh
                    </button>
                    <input
                      type="color"
                      @input="
                        editor
                          .chain()
                          .focus()
                          .setColor($event.target.value)
                          .run()
                      "
                      class="w-8 h-8 p-1 cursor-pointer bg-transparent"
                    />
                  </div>
                  <div class="p-6 min-h-[500px]">
                    <editor-content :editor="editor" class="tiptap-content" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div
              class="bg-white p-6 shadow-sm rounded-2xl border border-gray-200"
            >
              <label class="block font-bold text-sm text-gray-700 mb-4"
                >Ảnh bìa bài viết</label
              >
              <div
                class="relative group aspect-video bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl overflow-hidden flex items-center justify-center"
              >
                <img
                  v-if="thumbnailPreview"
                  :src="thumbnailPreview"
                  class="w-full h-full object-cover"
                />
                <input
                  type="file"
                  @change="handleThumbnailUpload"
                  class="absolute inset-0 opacity-0 cursor-pointer"
                  accept="image/*"
                />
                <div
                  v-if="isUploadingThumbnail"
                  class="absolute inset-0 bg-white/80 flex items-center justify-center"
                >
                  <div
                    class="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"
                  ></div>
                </div>
                <div
                  class="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition"
                >
                  Thay đổi ảnh
                </div>
              </div>
            </div>

            <div
              class="bg-white p-6 shadow-sm rounded-2xl border border-gray-200 space-y-5"
            >
              <div>
                <label class="block font-bold text-sm text-gray-700 mb-2"
                  >Chuyên mục</label
                >
                <select
                  v-model="form.category"
                  class="w-full border-gray-200 rounded-xl py-3 focus:ring-indigo-500"
                >
                  <option value="Tin tức">Tin tức</option>
                  <option value="Sự kiện">Sự kiện</option>
                  <option value="Khuyến mãi">Khuyến mãi</option>
                </select>
              </div>

              <div
                class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100"
              >
                <span class="text-sm font-bold text-gray-700">Công khai</span>
                <input
                  v-model="form.is_published"
                  type="checkbox"
                  class="w-5 h-5 rounded text-indigo-600"
                />
              </div>

              <button
                type="submit"
                :disabled="form.processing || isUploadingThumbnail"
                class="w-full py-4 px-6 bg-emerald-600 text-white rounded-2xl font-extrabold shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 disabled:bg-gray-300 disabled:shadow-none disabled:transform-none flex justify-center items-center gap-2"
              >
                <svg
                  v-if="form.processing"
                  class="animate-spin h-5 w-5 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>

                <span>{{
                  form.processing
                    ? "Đang cập nhật dữ liệu..."
                    : "Cập nhật bài viết"
                }}</span>
              </button>

              <Link
                :href="route('news.index')"
                class="block text-center text-sm text-gray-400 hover:text-gray-600"
                >Hủy bỏ</Link
              >
            </div>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style>
.toolbar-btn {
  @apply px-3 py-1.5 rounded-lg text-sm font-bold text-gray-600 hover:bg-gray-200 transition-all;
}
.tiptap-content .ProseMirror {
  outline: none;
  font-size: 1.125rem;
  line-height: 1.75;
  color: #374151;
}
</style>
