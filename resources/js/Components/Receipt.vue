<script setup>
defineProps({
    cart: Array,
    discount: Number,
    grandTotal: Number,
});

function formatPrice(price) {
    return parseFloat(price || 0).toFixed(2);
}
</script>
<template>
    <!-- Cart Table -->
    <div class="bg-white overflow-x-auto mt-8">
        <table class="min-w-full text-xs">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase">
                        Name
                    </th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase w-28">
                        Price
                    </th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase w-24">
                        Qty
                    </th>
                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase w-20">
                        Unit
                    </th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase w-28">
                        Subtotal
                    </th>
                </tr>
            </thead>
            <tbody>
                <!-- Existing cart items -->
                <tr v-for="(item, index) in cart" :key="index" class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-900">
                        {{ item.name }}
                    </td>
                    <td class="px-4 py-2 text-gray-900 text-right">
                        {{ formatPrice(item.unit_price) }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        {{ item.quantity }}
                    </td>
                    <td class="px-4 py-2 text-gray-500 text-center">
                        {{ item.unit }}
                    </td>
                    <td class="px-4 py-2 text-gray-900 text-right font-medium">
                        {{ formatPrice(item.subtotal) }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="mt-4 text-sm">
            <div class="flex flex-wrap items-center justify-end gap-6">
                <p class="flex items-center gap-2">Discount %: {{ discount }}</p>
                <p class="font-bold">Total: {{ formatPrice(grandTotal) }}</p>
            </div>
        </div>
    </div>
</template>
<!-- test -->

