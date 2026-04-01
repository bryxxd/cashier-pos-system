<template>
  <Head title="Transactions" />
  <AppLayout>
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Transactions</h2>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 mb-4">
      <input
        v-model="searchQuery"
        @input="debouncedSearch"
        type="text"
        placeholder="Search by user..."
        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800"
      />
      <select
        v-model="selectedMonth"
        @change="applyFilters"
        class="px-3 py-2 border border-gray-300 rounded-md"
      >
        <option value="">All Months</option>
        <option v-for="m in 12" :key="m" :value="m">{{ monthName(m) }}</option>
      </select>
      <select
        v-model="selectedYear"
        @change="applyFilters"
        class="px-3 py-2 border border-gray-300 rounded-md"
      >
        <option value="">All Years</option>
        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tendered</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Discount</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Change</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="t in transactions.data" :key="t.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-900">#{{ t.id }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ t.user?.username || 'N/A' }}</td>
            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ formatPrice(t.total_amount) }}</td>
            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ formatPrice(t.tendered_amount) }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 text-center">{{ t.discount }}%</td>
            <td class="px-6 py-4 text-sm text-right" :class="t.total_change < 0 ? 'text-red-600' : 'text-gray-900'">
              {{ formatPrice(t.total_change) }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(t.created_at) }}</td>
            <td class="px-6 py-4 text-sm text-center space-x-2">
              <Link :href="`/transactions/${t.id}`" class="text-blue-600 hover:text-blue-800">View</Link>
              <button @click="confirmDelete(t)" class="text-red-600 hover:text-red-800">Delete</button>
            </td>
          </tr>
          <tr v-if="transactions.data.length === 0">
            <td colspan="8" class="px-6 py-8 text-center text-gray-500">No transactions found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination :links="transactions.links" />

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>Delete Transaction</template>
      <p class="text-gray-600">Are you sure you want to delete transaction <strong>#{{ transactionToDelete?.id }}</strong>?</p>
      <template #footer>
        <button @click="showDeleteModal = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
        <button @click="submitDelete" class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
      </template>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  transactions: Object,
  filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const selectedMonth = ref(props.filters?.month || '');
const selectedYear = ref(props.filters?.year || '');
const showDeleteModal = ref(false);
const transactionToDelete = ref(null);

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 10 }, (_, i) => currentYear - i);

let searchTimeout = null;
function debouncedSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => applyFilters(), 300);
}

function applyFilters() {
  router.get('/transactions', {
    search: searchQuery.value || undefined,
    month: selectedMonth.value || undefined,
    year: selectedYear.value || undefined,
  }, { preserveState: true, replace: true });
}

function confirmDelete(t) {
  transactionToDelete.value = t;
  showDeleteModal.value = true;
}

function submitDelete() {
  router.delete(`/transactions/${transactionToDelete.value.id}`, {
    onSuccess: () => { showDeleteModal.value = false; },
  });
}

function monthName(m) {
  return new Date(2000, m - 1).toLocaleString('en', { month: 'long' });
}

function formatPrice(price) {
  return parseFloat(price || 0).toFixed(2);
}

function formatDate(date) {
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
  });
}
</script>
