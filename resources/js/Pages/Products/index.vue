<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3"; // Bỏ router nếu không dùng
import ConfirmModal from "@/Components/ConfirmModal.vue";
import { ref, watch } from "vue";
import debounce from "lodash/debounce";

const props = defineProps({
  products: Object,
  categories: Array,
  filters: Object,
});

const showModal = ref(false);
const selectedId = ref(null);
const deleteForm = useForm({});
// Khởi tạo các giá trị lọc
const search = ref(props.filters.search);
const category = ref(props.filters.category || "");
const highlight = ref(props.filters.highlight || "");
const filter = () => {
    router.get(route('products.index'), {
        search: search.value,
        category: category.value,
        highlight: highlight.value
    }, {
        preserveState: true, // Giữ lại trạng thái cuộn trang
        replace: true // Không tạo thêm history mới
    });
};
watch(search, debounce(() => {
    filter();
}, 500));

const confirmDelete = (id) => {
  selectedId.value = id;
  showModal.value = true;
};

const handleDelete = () => {
  deleteForm.delete(route("products.destroy", selectedId.value), {
    onSuccess: () => {
      showModal.value = false;
      selectedId.value = null;
    },
  });
};

// Hàm hỗ trợ parse JSON an toàn
const parseJson = (data) => {
  if (!data) return [];
  try {
    return typeof data === "string" ? JSON.parse(data) : data;
  } catch (e) {
    return [];
  }
};
</script>

<template>
  <Head title="Quản lý Sản phẩm" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        📦 Quản lý Sản phẩm Inox
      </h2>
    </template>

    <div class="py-12">
      <div class="mb-6 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px] relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input 
                    v-model="search"
                    type="text" 
                    placeholder="Tìm tên sản phẩm..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                />
            </div>

            <select 
                v-model="category"
                @change="filter"
                class="min-w-[150px] border border-gray-200 rounded-lg py-2 px-3 text-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
                <option value="">Tất cả loại</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.name">
                    {{ cat.name }}
                </option>
            </select>

            <select 
                v-model="highlight"
                @change="filter"
                class="min-w-[150px] border border-gray-200 rounded-lg py-2 px-3 text-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
                <option value="">Tất cả trạng thái</option>
                <option value="1">⭐ Nổi bật</option>
                <option value="0">⚪ Bình thường</option>
            </select>

            <button 
                @click="search = ''; category = ''; highlight = ''; filter()"
                class="p-2 text-gray-400 hover:text-indigo-600 transition-colors"
                title="Xóa lọc"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </button>
        </div>
    </div>
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-8 flex justify-between items-center">
          <p class="text-sm text-gray-500">
            Tổng cộng: <b>{{ products.total }}</b> sản phẩm
          </p>
          <Link
            :href="route('products.create')"
            class="flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 font-bold"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
            Thêm sản phẩm mới
          </Link>
        </div>

        <div
          class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden"
        >
          <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/50 border-b border-gray-100">
              <tr>
                <th
                  class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider"
                >
                  Thông tin sản phẩm
                </th>
                <th
                  class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider"
                >
                  Phân loại
                </th>
                <th
                  class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right"
                >
                  Giá niêm yết
                </th>
                <th
                  class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center"
                >
                  Chi tiết
                </th>
                <th
                  class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center"
                >
                  Sản phẩm nổi bật
                </th>
                <th
                  class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right"
                >
                  Thao tác
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="product in products.data"
                :key="product.id"
                class="hover:bg-indigo-50/30 transition-colors group"
              >
                <td class="p-4">
                  <div class="flex items-center">
                    <div class="relative h-16 w-16 shrink-0">
                      <img
                        :src="product.image_main"
                        class="h-full w-full rounded-xl object-cover border border-gray-100 shadow-sm"
                      />
                      <span
                        v-if="parseJson(product.images_sub).length > 0"
                        class="absolute -top-2 -right-2 bg-indigo-500 text-white text-[10px] px-1.5 py-0.5 rounded-full border-2 border-white font-bold"
                      >
                        +{{ parseJson(product.images_sub).length }}
                      </span>
                    </div>
                    <div class="ms-4">
                      <div
                        class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition"
                      >
                        {{ product.name }}
                      </div>
                      <div class="text-[10px] text-gray-400 font-mono">
                        ID: #{{ product.id }}
                      </div>
                    </div>
                  </div>
                </td>

                <td class="p-4">
                  <span
                    class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-bold uppercase tracking-tight"
                  >
                    {{ product.category }}
                  </span>
                </td>

                <td class="p-4 text-sm font-bold text-right">
                  <span v-if="product.price > 0" class="text-indigo-600">
                    {{ new Intl.NumberFormat("vi-VN").format(product.price) }}
                    <span class="text-[10px] underline">đ</span>
                  </span>
                  <span v-else class="text-gray-400 italic text-xs"
                    >Liên hệ</span
                  >
                </td>

                <td class="p-4 text-center">
                  <span
                    class="text-[10px] bg-emerald-50 text-emerald-600 px-2 py-1 rounded-md border border-emerald-100 font-bold"
                  >
                    {{ parseJson(product.specs).length }} thông số
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span
                    v-if="product.is_highlight"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-3 w-3"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                      />
                    </svg>
                    Nổi bật
                  </span>
                  <span v-else class="text-[10px] text-gray-300">Thường</span>
                </td>

                <td class="p-4 text-right">
                  <div class="flex justify-end items-center gap-2">
                    <Link
                      :href="route('products.show', product.id)"
                      class="p-2 text-gray-400 hover:text-emerald-600 transition"
                      title="Xem"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                      </svg>
                    </Link>
                    <Link
                      :href="route('products.edit', product.id)"
                      class="p-2 text-gray-400 hover:text-indigo-600 transition"
                      title="Sửa"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        />
                      </svg>
                    </Link>
                    <button
                      @click="confirmDelete(product.id)"
                      class="p-2 text-gray-400 hover:text-red-600 transition"
                      title="Xóa"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="products.data.length === 0">
                <td colspan="5" class="p-20 text-center">
                  <div
                    class="flex flex-col items-center justify-center text-gray-400"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-12 w-12 mb-4 opacity-20"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                      />
                    </svg>
                    <p class="font-medium">
                      Kho hàng trống. Hãy thêm sản phẩm ngay!
                    </p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div
            v-if="products.links.length > 3"
            class="p-6 border-t border-gray-50 flex justify-center"
          >
            <nav class="flex gap-2">
              <Link
                v-for="(link, k) in products.links"
                :key="k"
                :href="link.url || '#'"
                v-html="link.label"
                class="px-4 py-2 text-sm rounded-xl transition-all duration-200"
                :class="{
                  'bg-indigo-600 text-white shadow-md shadow-indigo-100 font-bold':
                    link.active,
                  'text-gray-300 pointer-events-none': !link.url,
                  'text-gray-500 hover:bg-gray-100': link.url && !link.active,
                }"
              />
            </nav>
          </div>
        </div>
      </div>
    </div>

    <ConfirmModal
      :show="showModal"
      title="Xóa sản phẩm?"
      message="Dữ liệu sản phẩm và các hình ảnh liên quan sẽ bị xóa vĩnh viễn. Bạn có chắc chắn không?"
      @close="showModal = false"
      @confirm="handleDelete"
    />
  </AuthenticatedLayout>
</template>
