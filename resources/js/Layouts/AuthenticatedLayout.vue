<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const isSidebarOpen = ref(true); 

// Trạng thái mở menu con của Sản phẩm
const isProductMenuOpen = ref(route().current('products.*') || route().current('categories.*'));
</script>
<template>
  <div class="min-h-screen bg-[#f8fafc] flex">
    <aside
      :class="isSidebarOpen ? 'w-64' : 'w-20'"
      class="fixed inset-y-0 left-0 z-50 bg-white text-slate-600 transition-all duration-300 ease-in-out hidden md:flex flex-col border-e border-gray-200 shadow-sm"
    >
      <div class="h-16 flex items-center justify-center border-b border-gray-100 shrink-0">
        <Link :href="route('dashboard')" class="flex items-center gap-3">
          <ApplicationLogo class="h-8 w-8 fill-current text-indigo-600" />
          <span v-if="isSidebarOpen" class="font-black text-xl tracking-tight text-slate-800">ADMIN</span>
        </Link>
      </div>

      <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5">
        <Link :href="route('dashboard')"
          :class="route().current('dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500 hover:bg-gray-50 hover:text-slate-900'"
          class="flex items-center p-3 rounded-xl transition-all duration-200 group"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span v-if="isSidebarOpen" class="ms-3 text-sm">Dashboard</span>
        </Link>

        <div class="space-y-1">
          <button
            @click="isProductMenuOpen = !isProductMenuOpen"
            :class="(route().current('products.*') || route().current('categories.*')) ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-gray-50 hover:text-slate-900'"
            class="w-full flex items-center p-3 rounded-xl transition-all duration-200 group justify-between"
          >
            <div class="flex items-center">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V5" />
              </svg>
              <span v-if="isSidebarOpen" class="ms-3 font-semibold text-sm">Quản lý Sản phẩm</span>
            </div>
            <svg v-if="isSidebarOpen" 
                 class="w-4 h-4 transition-transform duration-300" 
                 :class="isProductMenuOpen ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <div v-show="isProductMenuOpen && isSidebarOpen" class="mt-1 space-y-1">
            <Link :href="route('products.index')"
              :class="route().current('products.index') ? 'text-indigo-600 font-bold' : 'text-slate-400 hover:text-slate-900'"
              class="flex items-center py-2 pe-3 ps-11 rounded-lg text-[13px] transition-all"
            >
              Danh sách sản phẩm
            </Link>
            <Link :href="route('categories.index')"
              :class="route().current('categories.*') ? 'text-indigo-600 font-bold' : 'text-slate-400 hover:text-slate-900'"
              class="flex items-center py-2 pe-3 ps-11 rounded-lg text-[13px] transition-all"
            >
              Phân loại sản phẩm
            </Link>
          </div>
        </div>

        <Link :href="route('news.index')"
          :class="route().current('news.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500 hover:bg-gray-50 hover:text-slate-900'"
          class="flex items-center p-3 rounded-xl transition-all duration-200 group"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v4a2 2 0 002 2h4" />
          </svg>
          <span v-if="isSidebarOpen" class="ms-3 text-sm">Quản lý Tin tức</span>
        </Link>

        <Link :href="route('settings.index')"
          :class="route().current('settings.index') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500 hover:bg-gray-50 hover:text-slate-900'"
          class="flex items-center p-3 rounded-xl transition-all duration-200 group"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          </svg>
          <span v-if="isSidebarOpen" class="ms-3 text-sm">Cài đặt hệ thống</span>
        </Link>
      </nav>

      <div class="p-4 border-t border-gray-100">
        <button @click="isSidebarOpen = !isSidebarOpen" class="w-full flex items-center justify-center p-2.5 rounded-xl hover:bg-gray-50 text-slate-400 hover:text-slate-900 transition-all">
          <svg class="w-5 h-5 transition-transform duration-500" :class="!isSidebarOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
          </svg>
          <span v-if="isSidebarOpen" class="ms-3 text-xs font-bold uppercase tracking-widest">Thu gọn</span>
        </button>
      </div>
    </aside>

    <div :class="isSidebarOpen ? 'md:ms-64' : 'md:ms-20'" class="flex-1 flex flex-col transition-all duration-300 min-w-0">
      <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-40 shadow-sm">
        <h2 v-if="$slots.header" class="text-md font-bold text-slate-800 uppercase tracking-wide">
          <slot name="header" />
        </h2>
        
        <div class="flex items-center gap-4">
           </div>
      </header>

      <main class="p-8">
        <slot />
      </main>
    </div>
  </div>
</template>