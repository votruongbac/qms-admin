<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue"; // Bạn có thể custom lại component này hoặc dùng trực tiếp Link
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const isSidebarOpen = ref(true); // Trạng thái đóng/mở sidebar
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex">
    <aside
      :class="isSidebarOpen ? 'w-64' : 'w-20'"
      class="fixed inset-y-0 left-0 z-50 bg-white text-white transition-all duration-300 ease-in-out hidden md:flex flex-col"
    >
      <div
        class="h-16 flex items-center justify-center border-b border-slate-800 shrink-0"
      >
        <Link :href="route('dashboard')" class="flex items-center gap-3">
          <ApplicationLogo class="h-8 w-8 fill-current text-indigo-400" />
          <span v-if="isSidebarOpen" class="font-bold text-xl tracking-wider"
            >ADMIN</span
          >
        </Link>
      </div>

      <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-2">
        <Link
          :href="route('dashboard')"
          :class="
            route().current('dashboard')
              ? 'bg-indigo-600 text-white'
              : 'text-slate-400 hover:bg-slate-800 hover:text-white'
          "
          class="flex items-center p-3 rounded-lg transition-colors group"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
            />
          </svg>
          <span v-if="isSidebarOpen" class="ms-3 font-medium">Dashboard</span>
        </Link>

        <Link
          :href="route('products.index')"
          :class="
            route().current('products.*')
              ? 'bg-indigo-600 text-white'
              : 'text-slate-400 hover:bg-slate-800 hover:text-white'
          "
          class="flex items-center p-3 rounded-lg transition-colors group"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V5"
            />
          </svg>
          <span v-if="isSidebarOpen" class="ms-3 font-medium"
            >Quản lý Sản phẩm</span
          >
        </Link>

        <Link
          :href="route('news.index')"
          :class="
            route().current('news.*')
              ? 'bg-indigo-600 text-white'
              : 'text-slate-400 hover:bg-slate-800 hover:text-white'
          "
          class="flex items-center p-3 rounded-lg transition-colors group"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M14 2v4a2 2 0 002 2h4"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M7 8h0M7 12h8M7 16h8"
            />
          </svg>
          <span v-if="isSidebarOpen" class="ms-3 font-medium"
            >Quản lý Tin tức</span
          >
        </Link>

        <Link
          :href="route('settings.index')"
          :class="
            route().current('settings.index')
              ? 'bg-indigo-600 text-white'
              : 'text-slate-400 hover:bg-slate-800 hover:text-white'
          "
          class="flex items-center p-3 rounded-lg transition-colors group"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
            />
          </svg>
          <span v-if="isSidebarOpen" class="ms-3 font-medium"
            >Cài đặt hệ thống</span
          >
        </Link>
      </nav>

      <div class="p-4 border-t border-slate-800">
        <button
          @click="isSidebarOpen = !isSidebarOpen"
          class="w-full flex justify-center p-2 rounded-lg hover:bg-slate-800 text-slate-400"
        >
          <svg
            class="w-6 h-6 transition-transform"
            :class="!isSidebarOpen && 'rotate-180'"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
            />
          </svg>
        </button>
      </div>
    </aside>

    <div
      :class="isSidebarOpen ? 'md:ms-64' : 'md:ms-20'"
      class="flex-1 flex flex-col transition-all duration-300 min-w-0"
    >
      <header
        class="h-16 bg-white border-b flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-40"
      >
        <div>
          <h2
            v-if="$slots.header"
            class="text-lg font-semibold text-gray-800 leading-tight"
          >
            <slot name="header" />
          </h2>
        </div>

        <div class="flex items-center gap-4">
          <Dropdown align="right" width="48">
            <template #trigger>
              <button
                type="button"
                class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition"
              >
                <div
                  class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold"
                >
                  {{ $page.props.auth.user.name.charAt(0) }}
                </div>
                <span class="hidden md:inline">{{
                  $page.props.auth.user.name
                }}</span>
                <svg
                  class="h-4 w-4"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </template>

            <template #content>
              <DropdownLink :href="route('profile.edit')"> Hồ sơ </DropdownLink>
              <DropdownLink :href="route('logout')" method="post" as="button">
                Đăng xuất
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </header>

      <main class="p-4 sm:p-6 lg:p-8">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Tùy chỉnh thanh cuộn sidebar */
nav::-webkit-scrollbar {
  width: 4px;
}
nav::-webkit-scrollbar-thumb {
  background: #1e293b;
  border-radius: 10px;
}
</style>
