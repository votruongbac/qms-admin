<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";

const props = defineProps({ product: Object });
const isUploading = ref(false); 

const categories = [
    { id: 1, name: 'Inox 304' },
    { id: 2, name: 'Inox 201' },
    { id: 3, name: 'Phụ kiện tủ bếp' },
    { id: 4, name: 'Thiết bị gia dụng' },
];

// Khởi tạo form với dữ liệu cũ từ props.product
const form = useForm({
  name: props.product?.name ?? "",
  price: props.product?.price ?? 0,
  category: props.product?.category ?? "",
  image_main: props.product?.image_main ?? "",
  // Vì đã có $casts nên images_sub và specs đã là mảng, dùng được luôn
  images_sub: props.product?.images_sub ?? [], 
  description: props.product?.description ?? "",
  specs: props.product?.specs ?? [{ key: "", value: "" }],
  document: props.product?.document ?? "",
  is_highlight: props.product?.is_highlight ?? ""
});

/** Logic Upload (Dùng chung như trang Create) **/
const uploadFile = async (file) => {
  const formData = new FormData();
  formData.append("image", file);
  try {
    const res = await axios.post("https://upload.vtbac.io.vn/upload", formData, {
        headers: { "Content-Type": "multipart/form-data", "x-api-key": "bacdeptraivl" },
    });
    return res.data?.url || res.data?.data?.url;
  } catch (err) {
    alert("Lỗi upload file!");
    return null;
  }
};

const handleMainImageUpload = async (e) => {
  const file = e.target.files[0];
  if (!file) return;
  isUploading.value = true;
  const url = await uploadFile(file);
  if (url) form.image_main = url;
  isUploading.value = false;
};

const handleSubImagesUpload = async (e) => {
  const files = Array.from(e.target.files);
  isUploading.value = true;
  for (const file of files) {
    const url = await uploadFile(file);
    if (url) form.images_sub.push(url);
  }
  isUploading.value = false;
  e.target.value = "";
};

const handleDocUpload = async (e) => {
  const file = e.target.files[0];
  if (!file) return;
  isUploading.value = true;
  const url = await uploadFile(file);
  if (url) form.document = url;
  isUploading.value = false;
};

// Các hàm bổ trợ
const addSpec = () => form.specs.push({ key: "", value: "" });
const removeSpec = (index) => form.specs.splice(index, 1);
const removeImageSub = (index) => form.images_sub.splice(index, 1);

const submit = () => {
  if (isUploading.value) return alert("Vui lòng đợi upload xong!");
  
  // Sử dụng PUT để cập nhật
  form.put(route("products.update", props.product.id), {
    onSuccess: () => alert("Cập nhật sản phẩm thành công!"),
  });
};
</script>

<template>
  <Head :title="'Sửa: ' + product.name" />
  <AuthenticatedLayout>
    <template #header>
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">📝 Chỉnh sửa: {{ product.name }}</h2>
           
        </div>
    </template>

    <div class="py-12 px-4">
      <Link :href="route('products.show', product.id)" class="text-indigo-600 hover:underline text-sm font-bold">Xem trang chi tiết</Link>
      <div class="max-w-5xl mx-auto bg-white p-8 shadow-sm rounded-2xl border border-gray-100">
        <form @submit.prevent="submit" class="space-y-8">
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block font-bold text-sm text-gray-700 mb-2">Tên sản phẩm</label>
              <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-xl focus:ring-indigo-500" required />
            </div>
            <div>
              <label class="block font-bold text-sm text-gray-700 mb-2">Giá (VND)</label>
              <input v-model="form.price" type="number" class="w-full border-gray-300 rounded-xl focus:ring-indigo-500" />
            </div>
          </div>

          <div>
            <label class="block font-bold text-sm text-gray-700 mb-2">Mô tả sản phẩm</label>
            <textarea v-model="form.description" rows="4" class="w-full border-gray-300 rounded-xl focus:ring-indigo-500"></textarea>
          </div>

          <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
            <label class="block font-bold mb-4 text-gray-700">🖼️ Hình ảnh chính hiện tại</label>
            <div class="flex items-center gap-6">
                <img :src="form.image_main" class="h-32 w-32 object-cover rounded-xl shadow-md border-2 border-white" />
                <div class="flex-1">
                    <input type="file" @change="handleMainImageUpload" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" />
                </div>
            </div>
          </div>

          <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
            <label class="block font-bold mb-4 text-gray-700">📸 Thư viện ảnh phụ</label>
            <input type="file" multiple @change="handleSubImagesUpload" class="mb-6 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-600 file:text-white" />
            
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
              <div v-for="(url, index) in form.images_sub" :key="index" class="relative group">
                <img :src="url" class="h-24 w-full object-cover rounded-lg border shadow-sm" />
                <button @click="removeImageSub(index)" type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:scale-110 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
              </div>
            </div>
          </div>

          <div class="p-6 rounded-2xl border border-gray-100">
            <label class="block font-bold mb-4 text-gray-700">⚙️ Thông số kỹ thuật</label>
            <div v-for="(spec, index) in form.specs" :key="index" class="flex gap-3 mb-3">
              <input v-model="spec.key" placeholder="Tên thông số" class="w-1/3 border-gray-300 rounded-xl" />
              <input v-model="spec.value" placeholder="Giá trị" class="flex-1 border-gray-300 rounded-xl" />
              <button @click="removeSpec(index)" type="button" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </div>
            <button @click="addSpec" type="button" class="text-indigo-600 font-bold text-sm">+ Thêm thông số</button>
          </div>
          <div class="flex items-center gap-2 mt-4">
            <input 
                type="checkbox" 
                id="is_highlight" 
                v-model="form.is_highlight" 
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            />
            <label for="is_highlight" class="text-sm font-medium text-gray-700">
                Hiển thị sản phẩm nổi bật ở trang chủ
            </label>
        </div>
          <div class="pt-6 border-t flex gap-4">
            <button type="submit" :disabled="form.processing || isUploading" class="flex-1 bg-emerald-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition">
                {{ form.processing ? "Đang lưu..." : "Cập nhật sản phẩm ngay" }}
            </button>
            <Link :href="route('products.index')" class="px-8 py-4 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">Hủy</Link>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>