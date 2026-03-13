<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  product: Object,
});

// State để đổi ảnh chính khi click vào ảnh phụ
const activeImage = ref(props.product.image_main);
console.log(typeof props.product.specs);
// Hàm format tiền tệ
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

// Hàm lấy icon dựa trên đuôi file hoặc loại tài liệu
const getFileLabel = (url) => {
  if (!url) return "";
  return url.split('.').pop().toUpperCase();
};
</script>

<template>
  <Head :title="'Sản phẩm: ' + product.name" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Chi tiết sản phẩm
        </h2>
       
      </div>
    </template>

    <div class="py-12 px-4">
      <div class="max-w-6xl mx-auto">
        <Link :href="route('products.index')" class="text-sm text-gray-500 hover:text-indigo-600 transition">
          ← Quay lại danh sách
        </Link>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 bg-white p-8 shadow-sm rounded-2xl border border-gray-100">
          
          <div class="space-y-4">
            <div class="aspect-square overflow-hidden rounded-2xl bg-gray-50 border border-gray-100 shadow-inner">
                <img :src="activeImage" class="h-full w-full object-contain transition-all duration-500" />
            </div>
            
            <div v-if="product.images_sub && product.images_sub.length > 0" class="flex gap-3 overflow-x-auto pb-2 custom-scrollbar">
                
                <div 
                    @click="activeImage = product.image_main"
                    :class="activeImage === product.image_main ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-gray-200'"
                    class="h-20 w-20 shrink-0 border-2 rounded-lg cursor-pointer overflow-hidden transition hover:border-indigo-400"
                >
                    <img :src="product.image_main" class="h-full w-full object-cover" />
                </div>
                
                <div 
                    v-for="(img, index) in product.images_sub" 
                    :key="index"
                    @click="activeImage = img"
                    :class="activeImage === img ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-gray-200'"
                    class="h-20 w-20 shrink-0 border-2 rounded-lg cursor-pointer overflow-hidden transition hover:border-indigo-400"
                >
                    <img :src="img" class="h-full w-full object-cover" />
                </div>
            </div>
        </div>
          <div class="flex flex-col">
            <div class="mb-6 pb-6 border-b border-gray-100">
              <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full mb-3 uppercase tracking-wider">
                {{ product.category }}
              </span>
              <h1 class="text-3xl font-extrabold text-gray-900 mb-2 leading-tight">{{ product.name }}</h1>
              <p class="text-2xl font-bold text-red-500">{{ formatPrice(product.price) }}</p>
            </div>

            <div class="mb-8">
              <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-3">Mô tả sản phẩm</h3>
              <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ product.description }}</p>
            </div>

            <div class="mt-auto pt-6 flex gap-3">
              <Link 
                :href="route('products.edit', product.id)" 
                class="flex-1 bg-indigo-600 text-white text-center py-4 rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100"
              >
                Chỉnh sửa thông tin
              </Link>
              <a 
                v-if="product.document" 
                :href="product.document" 
                target="_blank"
                class="flex items-center justify-center px-6 bg-white border-2 border-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition"
                title="Tải tài liệu kĩ thuật"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              </a>
            </div>
          </div>
        </div>

        <div class="mt-10 bg-white p-8 shadow-sm rounded-2xl border border-gray-100">
          <h2 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center gap-2">
             ⚙️ Thông số kỹ thuật chi tiết
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-2">
            <div 
              v-for="(spec, index) in product.specs" 
              :key="index"
              class="flex justify-between py-3 border-b border-gray-50 last:border-0"
            >
              <span class="text-gray-500 font-medium">{{ spec.key }}</span>
              <span class="text-gray-900 font-bold">{{ spec.value }}</span>
            </div>
            
            <div v-if="!product.specs || product.specs.length === 0" class="col-span-2 text-center py-4 text-gray-400 italic">
               Chưa có thông số kỹ thuật cho sản phẩm này.
            </div>
          </div>
        </div>

        <div v-if="product.document" class="mt-6 p-4 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-between">
          <div class="flex items-center gap-3">
              <div class="h-10 w-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                  <span class="text-[10px] font-black text-indigo-600">{{ getFileLabel(product.document) }}</span>
              </div>
              <div>
                  <p class="text-sm font-bold text-indigo-900">Tài liệu hướng dẫn & Kỹ thuật</p>
                  <p class="text-xs text-indigo-500">Bấm vào nút bên phải để xem trực tuyến hoặc tải về</p>
              </div>
          </div>
          <a :href="product.document" target="_blank" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
              Xem ngay
          </a>
      </div>
      
      <div v-else class="mt-6 p-4 bg-gray-50 border border-dashed border-gray-200 rounded-xl flex items-center gap-3 text-gray-400">
          <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
          </div>
          <span class="text-sm font-medium italic">Chưa có tài liệu hướng dẫn cho sản phẩm này</span>
      </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>