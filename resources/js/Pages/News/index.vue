<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3"; // Gộp chung vào đây
import ConfirmModal from "@/Components/ConfirmModal.vue";
import { ref } from 'vue';

const props = defineProps({
  news: Object,
});

const showModal = ref(false);
const selectedId = ref(null);
const deleteForm = useForm({});

const confirmDelete = (id) => {
  selectedId.value = id;
  showModal.value = true;
};

const handleDelete = () => {
  deleteForm.delete(route("news.destroy", selectedId.value), {
    onSuccess: () => {
      showModal.value = false;
      selectedId.value = null;
    },
  });
};
</script>

<template>
  <Head title="Quản lý Tin tức" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Quản lý Tin tức</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-8 flex justify-end">
          <Link :href="route('news.create')" class="flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-lg font-bold">
            <span>+ Thêm tin mới</span>
          </Link>
        </div>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b bg-gray-50">
                <th class="p-3 text-sm font-semibold text-gray-600">ID</th>
                <th class="p-3 text-sm font-semibold text-gray-600">Hình ảnh</th>
                <th class="p-3 text-sm font-semibold text-gray-600">Tiêu đề</th>
                <th class="p-3 text-sm font-semibold text-gray-600">Ngày đăng</th>
                <th class="p-3 text-sm font-semibold text-gray-600 text-right">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in news.data" :key="item.id" class="border-b hover:bg-gray-50 transition">
                <td class="p-3 text-sm text-gray-700">{{ item.id }}</td>
                <td class="p-3">
                  <img v-if="item.thumbnail" :src="item.thumbnail" class="h-12 w-20 object-cover rounded shadow-sm" />
                </td>
                <td class="p-3 text-sm text-gray-700 font-medium">{{ item.title }}</td>
                <td class="p-3 text-sm text-gray-500">
                  {{ new Date(item.created_at).toLocaleDateString("vi-VN") }}
                </td>
                <td class="p-3 text-right space-x-3">
                  <div class="flex justify-end items-center gap-2">
                    <Link :href="route('news.show', item.id)" class="p-2 text-gray-400 hover:text-emerald-600 transition" title="Xem">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </Link>
                    <Link :href="route('news.edit', item.id)" class="p-2 text-gray-400 hover:text-indigo-600 transition" title="Sửa">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </Link>
                    <button @click="confirmDelete(item.id)" class="p-2 text-gray-400 hover:text-red-600 transition" title="Xóa">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
                </td>
              </tr>
            </tbody>
          </table>
          
          <div class="mt-6 flex justify-center space-x-1">
             <Link v-for="link in news.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
              class="px-3 py-1 border rounded text-sm" :class="{'bg-blue-600 text-white': link.active, 'text-gray-400': !link.url}" />
          </div>
        </div>
      </div>
    </div>

    <ConfirmModal
      :show="showModal"
      title="Xóa bài viết?"
      message="Bạn có chắc chắn muốn xóa bài viết này vĩnh viễn không?"
      @close="showModal = false"
      @confirm="handleDelete"
    />
  </AuthenticatedLayout>
</template>