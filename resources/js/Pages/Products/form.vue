<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
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
const form = useForm({
  name: props.product?.name ?? "",
  price: props.product?.price ?? 0,
  category: props.product?.category ?? "",
  image_main: props.product?.image_main ?? "",
  images_sub: props.product?.images_sub ?? [], // Mảng chứa các URL ảnh phụ
  description: props.product?.description ?? "",
  specs: props.product?.specs ?? [{ key: "", value: "" }],
  document: props.product?.document ?? "",
  is_highlight: props.product?.is_highlight ?? 0
});

/**
 * Hàm upload dùng chung cho tất cả các loại file
 */
const uploadFile = async (file) => {
  const formData = new FormData();
  formData.append("image", file); // API của bạn dùng key là 'image'

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
    // Lấy URL từ kết quả trả về của API
    return res.data?.url || res.data?.data?.url;
  } catch (err) {
    console.error("Upload thất bại:", err);
    alert("Lỗi upload file!");
    return null;
  }
};

// Xử lý upload ảnh chính (1 ảnh duy nhất)
const handleMainImageUpload = async (e) => {
  const file = e.target.files[0];
  if (!file) return;

  isUploading.value = true;
  const url = await uploadFile(file);
  if (url) {
    form.image_main = url;
  }
  isUploading.value = false;
};

// Xử lý upload nhiều ảnh phụ (Gallery)
const handleSubImagesUpload = async (e) => {
  const files = Array.from(e.target.files);
  if (files.length === 0) return;

  isUploading.value = true;
  
  // Chạy vòng lặp upload từng ảnh và đẩy vào mảng images_sub
  for (const file of files) {
    const url = await uploadFile(file);
    if (url) {
      form.images_sub.push(url);
    }
  }
  
  isUploading.value = false;
  e.target.value = ""; // Reset input file để có thể chọn lại cùng 1 file nếu muốn
};

// Xử lý upload tài liệu
const handleDocUpload = async (e) => {
  const file = e.target.files[0];
  if (!file) return;

  isUploading.value = true;
  const url = await uploadFile(file);
  if (url) {
    form.document = url;
  }
  isUploading.value = false;
};

const removeSpec = (index) => form.specs.splice(index, 1);
const addSpec = () => form.specs.push({ key: "", value: "" });
const removeImageSub = (index) => form.images_sub.splice(index, 1);

const submit = () => {
  if (isUploading.value) {
    alert("Vui lòng đợi quá trình tải ảnh hoàn tất!");
    return;
  }

  if (props.product) {
    form.put(route("products.update", props.product.id));
  } else {
    form.post(route("products.store"));
  }
};
</script>

<template>
  <Head title="Quản lý sản phẩm" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ props.product ? "Chỉnh sửa sản phẩm" : "Thêm sản phẩm mới" }}
      </h2>
    </template>

    <div class="py-12 px-4">
      <div class="max-w-5xl mx-auto bg-white p-8 shadow-sm rounded-xl border border-gray-200">
        <form @submit.prevent="submit" class="space-y-8">
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block font-bold text-sm text-gray-700 mb-2">Tên sản phẩm</label>
              <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500" required />
            </div>
            <div>
              <label class="block font-bold text-sm text-gray-700 mb-2">Giá (VND)</label>
              <input v-model="form.price" type="number" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500" />
            </div>
          </div>
          <div>
            <label class="block font-bold text-sm text-gray-700 mb-2">Mô tả sản phẩm</label>
            <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500" required />
          </div>
          <div class="p-6 rounded-xl bg-slate-50 border border-slate-200">
            <label class="block font-bold mb-3 text-gray-700">🖼️ Hình ảnh chính</label>
            <div class="flex items-start gap-6">
                <div v-if="form.image_main" class="shrink-0">
                    <img :src="form.image_main" class="h-40 w-40 object-cover rounded-xl border-2 border-white shadow-md" />
                </div>
                <div class="flex-1">
                    <input type="file" @change="handleMainImageUpload" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer" />
                    <p class="mt-2 text-xs text-gray-400 italic">Đây là ảnh đại diện của sản phẩm.</p>
                </div>
            </div>
          </div>

          <div class="p-6 rounded-xl bg-slate-50 border border-slate-200">
            <label class="block font-bold mb-3 text-gray-700">📸 Hình ảnh phụ (Nhiều ảnh)</label>
            <input type="file" multiple @change="handleSubImagesUpload" class="mb-6 block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-4">
              <div v-for="(url, index) in form.images_sub" :key="index" class="relative group aspect-square">
                <img :src="url" class="h-full w-full object-cover rounded-lg border-2 border-white shadow-sm" />
                <button @click="removeImageSub(index)" type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 shadow-lg hover:scale-110 transition">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <div>
            <label class="block font-bold text-sm text-gray-700 mb-2">Loại sản phẩm (Category)</label>
            <select 
              v-model="form.category" 
              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              required
            >
              <option value="" disabled selected>-- Chọn loại sản phẩm --</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.name">
                {{ cat.name }}
              </option>
            </select>
          </div>
          <div class="p-6 rounded-xl border border-gray-200">
            <label class="block font-bold mb-4 text-gray-700">⚙️ Thông số kỹ thuật</label>
            <div v-for="(spec, index) in form.specs" :key="index" class="flex gap-3 mb-3">
              <input v-model="spec.key" placeholder="Tên thông số (VD: Màu sắc)" class="w-1/3 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500" />
              <input v-model="spec.value" placeholder="Giá trị (VD: Đen nhám)" class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500" />
              <button @click="removeSpec(index)" type="button" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              </button>
            </div>
            <button @click="addSpec" type="button" class="mt-2 inline-flex items-center text-indigo-600 text-sm font-bold hover:text-indigo-800">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
              Thêm thông số mới
            </button>
          </div>

          <div>
            <label class="block font-bold text-sm text-gray-700 mb-2">📄 Tài liệu (PDF/Document)</label>
            <div class="flex items-center gap-4 p-4 border rounded-lg bg-gray-50">
                <input type="file" @change="handleDocUpload" class="text-sm" accept=".pdf,.doc,.docx" />
                <div v-if="form.document" class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold">
                    Đã có file đính kèm
                </div>
            </div>
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
          <div class="pt-6 border-t">
            <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 disabled:bg-gray-400" :disabled="form.processing || isUploading">
                <span v-if="isUploading">Đang tải dữ liệu lên máy chủ...</span>
                <span v-else-if="form.processing">Đang lưu thông tin...</span>
                <span v-else>{{ props.product ? "Cập nhật sản phẩm" : "Lưu sản phẩm mới" }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>