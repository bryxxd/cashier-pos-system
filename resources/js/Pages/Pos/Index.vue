<template>
  <Head title="POS" />
  <AppLayout>
    <!-- Keyboard Shortcuts Banner -->
    <div class="bg-gray-800 text-white p-3 rounded-lg mb-4">
      <div class="flex flex-wrap gap-4 text-sm">
        <span class="font-semibold">Shortcuts:</span>
        <span>Ctrl+F1 = Barcode</span>
        <span>Ctrl+F2 = Product Name</span>
        <span>Ctrl+F3 = Alias</span>
        <span>Ctrl+M = Payment</span>
      </div>
    </div>

    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-bold text-gray-800">POS</h2>
      <button
        @click="openPayment"
        :disabled="cart.length === 0"
        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Payment
      </button>
    </div>

    <!-- Cart Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-44">Barcode</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-36">Alias</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Price</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Qty</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Unit</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Subtotal</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <!-- Existing cart items -->
          <tr v-for="(item, index) in cart" :key="index" class="hover:bg-gray-50">
            <td class="px-4 py-2 text-sm text-gray-900">{{ item.bar_code }}</td>
            <td class="px-4 py-2 text-sm text-gray-900">{{ item.name }}</td>
            <td class="px-4 py-2 text-sm text-gray-500">{{ item.alias || '—' }}</td>
            <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ formatPrice(item.unit_price) }}</td>
            <td class="px-4 py-2">
              <input
                v-model.number="item.quantity"
                type="number"
                step="0.001"
                min="0.001"
                @input="recalculate"
                class="w-20 px-2 py-1 text-sm text-center border rounded-md"
              />
            </td>
            <td class="px-4 py-2 text-sm text-gray-500 text-center">{{ item.unit }}</td>
            <td class="px-4 py-2 text-sm text-gray-900 text-right font-medium">{{ formatPrice(item.subtotal) }}</td>
            <td class="px-4 py-2 text-center">
              <button @click="removeItem(index)" class="text-red-500 hover:text-red-700 text-lg">&times;</button>
            </td>
          </tr>

          <!-- New item input row -->
          <tr class="bg-gray-50">
            <td class="px-4 py-2">
              <input
                ref="barcodeInput"
                v-model="newItem.barcode"
                @keydown.enter.prevent="lookupByBarcode"
                type="text"
                placeholder="Scan barcode..."
                class="w-full px-2 py-1 text-sm border rounded-md"
              />
            </td>
            <td class="px-4 py-2">
              <select
                ref="nameInput"
                v-model="newItem.name"
                @change="lookupByName"
                class="w-full px-2 py-1 text-sm border rounded-md"
              >
                <option value="">Choose product...</option>
                <option v-for="p in products" :key="p.id" :value="p.name">{{ p.name }}</option>
              </select>
            </td>
            <td class="px-4 py-2">
              <input
                ref="aliasInput"
                v-model="newItem.alias"
                @keydown.enter.prevent="lookupByAlias"
                type="text"
                placeholder="Alias..."
                class="w-full px-2 py-1 text-sm border rounded-md"
              />
            </td>
            <td colspan="5" class="px-4 py-2 text-sm text-gray-400 italic">
              Enter barcode, select product, or type alias
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Totals Bar -->
    <div class="mt-4 bg-white rounded-lg shadow p-4">
      <div class="flex flex-wrap items-center justify-end gap-6">
        <div class="flex items-center gap-2">
          <label class="text-sm font-medium text-gray-700">Discount %:</label>
          <input
            v-model.number="discount"
            type="number"
            min="0"
            max="100"
            @input="recalculate"
            class="w-20 px-2 py-1 text-sm border rounded-md text-right"
          />
        </div>
        <div class="text-xl font-bold text-gray-800">
          Total: <span class="text-green-600">{{ formatPrice(grandTotal) }}</span>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <Modal :show="showPaymentModal" @close="showPaymentModal = false">
      <template #title>Payment</template>
      <div class="space-y-4">
        <div>
          <label class="block text-lg font-semibold text-gray-700">Payable Amount</label>
          <input
            :value="formatPrice(grandTotal)"
            type="text"
            readonly
            class="mt-1 w-full px-3 py-2 text-right text-lg border rounded-md bg-gray-50"
          />
        </div>
        <div>
          <label class="block text-lg font-semibold text-gray-700">Tendered Amount</label>
          <input
            ref="tenderInput"
            v-model.number="tenderedAmount"
            type="number"
            step="0.01"
            @input="calculateChange"
            @keydown.enter.prevent="submitPayment"
            class="mt-1 w-full px-3 py-2 text-right text-lg border rounded-md focus:ring-2 focus:ring-green-500"
          />
        </div>
        <div>
          <label class="block text-lg font-semibold text-gray-700">Change</label>
          <input
            :value="formatPrice(change)"
            type="text"
            readonly
            class="mt-1 w-full px-3 py-2 text-right text-lg border rounded-md"
            :class="change < 0 ? 'bg-red-50 border-red-300 text-red-600' : 'bg-green-50 text-green-700'"
          />
        </div>
      </div>
      <template #footer>
        <button @click="showPaymentModal = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Close</button>
        <button
          @click="submitPayment"
          :disabled="change < 0 || paymentProcessing"
          class="px-6 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
        >
          {{ paymentProcessing ? 'Processing...' : 'Save & Print' }}
        </button>
      </template>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  products: Array,
});

const cart = ref([]);
const discount = ref(0);
const showPaymentModal = ref(false);
const tenderedAmount = ref(0);
const change = ref(0);
const paymentProcessing = ref(false);

const barcodeInput = ref(null);
const nameInput = ref(null);
const aliasInput = ref(null);
const tenderInput = ref(null);

const newItem = ref({ barcode: '', name: '', alias: '' });

// Computed
const subtotal = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.subtotal, 0);
});

const grandTotal = computed(() => {
  const disc = discount.value || 0;
  return subtotal.value * (1 - disc / 100);
});

// Methods
function recalculate() {
  cart.value.forEach(item => {
    item.subtotal = parseFloat((item.unit_price * item.quantity).toFixed(2));
  });
}

function addToCart(product) {
  // Check for duplicate
  const existing = cart.value.find(item => item.product_id === product.id);
  if (existing) {
    alert('Product already in cart');
    resetNewItem();
    return;
  }

  cart.value.push({
    product_id: product.id,
    name: product.name,
    bar_code: product.bar_code,
    alias: product.alias || '',
    unit_price: parseFloat(product.sale_price),
    quantity: 1,
    unit: product.unit,
    subtotal: parseFloat(product.sale_price),
  });

  resetNewItem();
  nextTick(() => barcodeInput.value?.focus());
}

function resetNewItem() {
  newItem.value = { barcode: '', name: '', alias: '' };
}

function removeItem(index) {
  cart.value.splice(index, 1);
}

async function lookupByBarcode() {
  if (!newItem.value.barcode) return;
  try {
    const response = await fetch(`/api/products/search?bar_code=${encodeURIComponent(newItem.value.barcode)}`);
    const data = await response.json();
    if (data.type === 'Success') {
      addToCart(data);
    } else {
      alert('Barcode not found');
      newItem.value.barcode = '';
      barcodeInput.value?.focus();
    }
  } catch {
    alert('Error looking up barcode');
  }
}

async function lookupByName() {
  if (!newItem.value.name) return;
  try {
    const response = await fetch(`/api/products/search?name=${encodeURIComponent(newItem.value.name)}`);
    const data = await response.json();
    if (data.type === 'Success') {
      addToCart(data);
    } else {
      alert('Product not found');
    }
  } catch {
    alert('Error looking up product');
  }
}

async function lookupByAlias() {
  if (!newItem.value.alias) return;
  try {
    const response = await fetch(`/api/products/search?alias=${encodeURIComponent(newItem.value.alias)}`);
    const data = await response.json();
    if (data.type === 'Success') {
      addToCart(data);
    } else {
      alert('Alias not found');
      newItem.value.alias = '';
      aliasInput.value?.focus();
    }
  } catch {
    alert('Error looking up alias');
  }
}

function openPayment() {
  tenderedAmount.value = 0;
  change.value = 0;
  showPaymentModal.value = true;
  nextTick(() => tenderInput.value?.focus());
}

function calculateChange() {
  change.value = parseFloat(((tenderedAmount.value || 0) - grandTotal.value).toFixed(2));
}

function submitPayment() {
  if (change.value < 0) {
    alert('Insufficient amount');
    return;
  }

  const printReceipt = confirm('Print receipt?');

  paymentProcessing.value = true;
  router.post('/pos/payment', {
    total_amount: grandTotal.value,
    tendered_amount: tenderedAmount.value,
    discount: discount.value,
    total_change: change.value,
    items: cart.value.map(item => ({
      product_id: item.product_id,
      quantity: item.quantity,
      unit_price: item.unit_price,
      sale_price: item.subtotal,
    })),
  }, {
    onSuccess: () => {
      if (printReceipt) {
        window.print();
      }
      cart.value = [];
      discount.value = 0;
      showPaymentModal.value = false;
      paymentProcessing.value = false;
      nextTick(() => barcodeInput.value?.focus());
    },
    onError: () => {
      paymentProcessing.value = false;
    },
  });
}

function formatPrice(price) {
  return parseFloat(price || 0).toFixed(2);
}

// Keyboard shortcuts
function handleKeydown(e) {
  if (e.ctrlKey || e.metaKey) {
    switch (e.key) {
      case 'F1':
        e.preventDefault();
        barcodeInput.value?.focus();
        break;
      case 'F2':
        e.preventDefault();
        nameInput.value?.focus();
        break;
      case 'F3':
        e.preventDefault();
        aliasInput.value?.focus();
        break;
      case 'm':
      case 'M':
        e.preventDefault();
        if (cart.value.length > 0) openPayment();
        break;
    }
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
  barcodeInput.value?.focus();
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});
</script>

<style>
@media print {
  nav, .bg-gray-800, button, input, select, .shadow { display: none !important; }
  table { border-collapse: collapse; }
  td, th { border: 1px solid #000; padding: 4px 8px; }
}
</style>
