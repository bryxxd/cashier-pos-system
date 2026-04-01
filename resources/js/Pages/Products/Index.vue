<template>
  <Head title="Products" />
  <AppLayout>
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Products</h2>
      <button
        @click="showAddModal = true"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm transition"
      >
        + Add Product
      </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="searchQuery"
        @input="debouncedSearch"
        type="text"
        placeholder="Search products by name, barcode, or alias..."
        class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800"
      />
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barcode</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alias</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sale Price</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-900">{{ product.bar_code }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ product.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ product.alias || '—' }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ product.unit }}</td>
            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ formatPrice(product.mrp) }}</td>
            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ formatPrice(product.sale_price) }}</td>
            <td class="px-6 py-4 text-sm text-center space-x-2">
              <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-800">Edit</button>
              <button @click="confirmDelete(product)" class="text-red-600 hover:text-red-800">Delete</button>
            </td>
          </tr>
          <tr v-if="products.data.length === 0">
            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No products found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination :links="products.links" />

    <!-- Add Product Modal -->
    <Modal :show="showAddModal" @close="showAddModal = false">
      <template #title>Add Product</template>
      <form @submit.prevent="submitAdd">
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700">Barcode</label>
            <input v-model="addForm.bar_code" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            <p v-if="addForm.errors.bar_code" class="text-red-500 text-xs mt-1">{{ addForm.errors.bar_code }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input v-model="addForm.name" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Alias</label>
            <input v-model="addForm.alias" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" placeholder="Optional" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Unit</label>
            <select v-model="addForm.unit" class="mt-1 w-full px-3 py-2 border rounded-md">
              <option value="Pcs">Pcs</option>
              <option value="Box">Box</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700">Price (MRP)</label>
              <input v-model="addForm.mrp" type="number" step="0.01" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Sale Price</label>
              <input v-model="addForm.sale_price" type="number" step="0.01" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            </div>
          </div>
        </div>
      </form>
      <template #footer>
        <button @click="showAddModal = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
        <button @click="submitAdd" :disabled="addForm.processing" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50">Save</button>
      </template>
    </Modal>

    <!-- Edit Product Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false">
      <template #title>Edit Product</template>
      <form @submit.prevent="submitEdit">
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700">Barcode</label>
            <input v-model="editForm.bar_code" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            <p v-if="editForm.errors.bar_code" class="text-red-500 text-xs mt-1">{{ editForm.errors.bar_code }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input v-model="editForm.name" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Alias</label>
            <input v-model="editForm.alias" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Unit</label>
            <select v-model="editForm.unit" class="mt-1 w-full px-3 py-2 border rounded-md">
              <option value="Pcs">Pcs</option>
              <option value="Box">Box</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700">Price (MRP)</label>
              <input v-model="editForm.mrp" type="number" step="0.01" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Sale Price</label>
              <input v-model="editForm.sale_price" type="number" step="0.01" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            </div>
          </div>
        </div>
      </form>
      <template #footer>
        <button @click="showEditModal = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
        <button @click="submitEdit" :disabled="editForm.processing" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">Update</button>
      </template>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>Delete Product</template>
      <p class="text-gray-600">Are you sure you want to delete <strong>{{ productToDelete?.name }}</strong>?</p>
      <template #footer>
        <button @click="showDeleteModal = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
        <button @click="submitDelete" class="px-4 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
      </template>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  products: Object,
  filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const productToDelete = ref(null);

let searchTimeout = null;
function debouncedSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/products', { search: searchQuery.value }, { preserveState: true, replace: true });
  }, 300);
}

const addForm = useForm({
  bar_code: '',
  name: '',
  alias: '',
  unit: 'Pcs',
  mrp: '',
  sale_price: '',
});

const editForm = useForm({
  _method: 'PUT',
  bar_code: '',
  name: '',
  alias: '',
  unit: 'Pcs',
  mrp: '',
  sale_price: '',
});
let editProductId = null;

function editProduct(product) {
  editProductId = product.id;
  editForm.bar_code = product.bar_code;
  editForm.name = product.name;
  editForm.alias = product.alias || '';
  editForm.unit = product.unit;
  editForm.mrp = product.mrp;
  editForm.sale_price = product.sale_price;
  showEditModal.value = true;
}

function submitAdd() {
  addForm.post('/products', {
    onSuccess: () => {
      showAddModal.value = false;
      addForm.reset();
    },
  });
}

function submitEdit() {
  editForm.post(`/products/${editProductId}`, {
    onSuccess: () => {
      showEditModal.value = false;
    },
  });
}

function confirmDelete(product) {
  productToDelete.value = product;
  showDeleteModal.value = true;
}

function submitDelete() {
  router.delete(`/products/${productToDelete.value.id}`, {
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
}

function formatPrice(price) {
  return parseFloat(price).toFixed(2);
}
</script>
