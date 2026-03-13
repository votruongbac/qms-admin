<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ categories: Array });
</script>

<template>
    <Head title="Quản lý loại" />
    <AuthenticatedLayout>
        <template #header>Quản lý loại sản phẩm</template>
        <div class="bg-white p-6 rounded-lg shadow">
            Danh sách loại inox
        </div>
    </AuthenticatedLayout>
</template>