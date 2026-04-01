<template>
  <Head :title="`Transaction #${transaction.id}`" />
  <AppLayout>
    <div class="mb-6">
      <Link href="/transactions" class="text-blue-600 hover:text-blue-800 text-sm">&larr; Back to Transactions</Link>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Transaction #{{ transaction.id }}</h2>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div>
          <p class="text-sm text-gray-500">Cashier</p>
          <p class="font-medium">{{ transaction.user?.name }} ({{ transaction.user?.username }})</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Date</p>
          <p class="font-medium">{{ formatDate(transaction.created_at) }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Discount</p>
          <p class="font-medium">{{ transaction.discount }}%</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Total</p>
          <p class="text-xl font-bold text-green-600">{{ formatPrice(transaction.total_amount) }}</p>
        </div>
      </div>

      <!-- Items Table -->
      <h3 class="text-lg font-semibold text-gray-700 mb-3">Items</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="item in transaction.items" :key="item.id">
              <td class="px-4 py-3 text-sm text-gray-900">{{ item.product?.name || 'Deleted Product' }}</td>
              <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ formatPrice(item.unit_price) }}</td>
              <td class="px-4 py-3 text-sm text-gray-900 text-center">{{ item.quantity }}</td>
              <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ formatPrice(item.sale_price) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Payment Summary -->
      <div class="mt-6 border-t pt-4">
        <div class="flex flex-col items-end gap-1 text-sm">
          <div class="flex gap-8">
            <span class="text-gray-500">Total Amount:</span>
            <span class="font-medium">{{ formatPrice(transaction.total_amount) }}</span>
          </div>
          <div class="flex gap-8">
            <span class="text-gray-500">Tendered:</span>
            <span class="font-medium">{{ formatPrice(transaction.tendered_amount) }}</span>
          </div>
          <div class="flex gap-8">
            <span class="text-gray-500">Change:</span>
            <span class="font-medium" :class="transaction.total_change < 0 ? 'text-red-600' : ''">
              {{ formatPrice(transaction.total_change) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  transaction: Object,
});

function formatPrice(price) {
  return parseFloat(price || 0).toFixed(2);
}

function formatDate(date) {
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
  });
}
</script>
