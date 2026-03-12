<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

const props = defineProps({
  news: Object, // Laravel Paginate trả về một Object chứa data và links
});

// Hàm xóa tin tức
const deleteNews = (id) => {
  if (confirm("Bạn có chắc chắn muốn xóa bài viết này không?")) {
    router.delete(route("news.destroy", id));
  }
};
</script>

<template>
  <Head title="Quản lý Tin tức" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Quản lý Tin tức
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-8 flex justify-end">
          <Link
            :href="route('news.create')"
            class="flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-300 shadow-lg hover:shadow-indigo-200 hover:-translate-y-1 text-base font-bold"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
              />
            </svg>
            <span> Thêm tin mới</span>
          </Link>
        </div>
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b bg-gray-50">
                <th class="p-3 text-sm font-semibold text-gray-600">ID</th>
                <th class="p-3 text-sm font-semibold text-gray-600">
                  Hình ảnh
                </th>
                <th class="p-3 text-sm font-semibold text-gray-600">Tiêu đề</th>
                <th class="p-3 text-sm font-semibold text-gray-600">
                  Ngày đăng
                </th>
                <th class="p-3 text-sm font-semibold text-gray-600 text-right">
                  Thao tác
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="item in news.data"
                :key="item.id"
                class="border-b hover:bg-gray-50 transition"
              >
                <td class="p-3 text-sm text-gray-700">{{ item.id }}</td>
                <td class="p-3">
                  <img
                    v-if="item.image"
                    :src="item.thumbnail"
                    class="h-12 w-20 object-cover rounded shadow-sm"
                  />
                  <span v-else class="text-gray-400 text-xs italic"
                    >Không có ảnh</span
                  >
                </td>
                <td class="p-3 text-sm text-gray-700 font-medium">
                  {{ item.title }}
                </td>
                <td class="p-3 text-sm text-gray-500">
                  {{ new Date(item.created_at).toLocaleDateString("vi-VN") }}
                </td>
                <td class="p-3 text-right space-x-2">
                  <Link
                    :href="route('news.show', item.id)"
                    class="text-green-500 hover:green-red-900 font-medium"
                    title="Xem chi tiết"
                  >
                    Xem
                  </Link>
                  <Link
                    :href="route('news.edit', item.id)"
                    class="text-indigo-600 hover:text-indigo-900 font-medium"
                    >Sửa</Link
                  >
                  <button
                    @click="deleteNews(item.id)"
                    class="text-red-600 hover:text-red-900 font-medium"
                  >
                    Xóa
                  </button>
                </td>
              </tr>
              <tr v-if="news.data.length === 0">
                <td colspan="5" class="p-6 text-center text-gray-500">
                  Chưa có bài viết nào.
                </td>
              </tr>
            </tbody>
          </table>

          <div class="mt-6 flex justify-center space-x-1">
            <Link
              v-for="link in news.links"
              :key="link.label"
              :href="link.url || '#'"
              v-html="link.label"
              class="px-3 py-1 border rounded text-sm"
              :class="{
                'bg-blue-600 text-white': link.active,
                'text-gray-500': !link.url,
              }"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
