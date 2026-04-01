<template>
  <Head title="Dashboard" />
  <AppLayout>
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Products Card -->
      <div class="bg-white border-2 border-gray-800 rounded-lg p-6 text-center">
        <div class="text-4xl mb-3">📦</div>
        <h5 class="text-lg font-medium text-gray-600">Number of Products</h5>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.products }}</p>
      </div>

      <!-- Users Card -->
      <div class="bg-white border-2 border-gray-800 rounded-lg p-6 text-center">
        <div class="text-4xl mb-3">👤</div>
        <h5 class="text-lg font-medium text-gray-600">Number of Users</h5>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.users }}</p>
      </div>

      <!-- Transactions Card -->
      <div class="bg-white border-2 border-gray-800 rounded-lg p-6 text-center">
        <div class="text-4xl mb-3">🧾</div>
        <h5 class="text-lg font-medium text-gray-600">Number of Transactions</h5>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.transactions }}</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  stats: Object,
});
</script>
