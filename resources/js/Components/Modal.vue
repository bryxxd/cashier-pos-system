<template>
  <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black/50 transition-opacity" @click="$emit('close')" />
      <!-- Modal -->
      <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 z-10">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">
            <slot name="title" />
          </h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 text-xl leading-none"
          >
            &times;
          </button>
        </div>
        <!-- Body -->
        <div class="p-4">
          <slot />
        </div>
        <!-- Footer -->
        <div v-if="$slots.footer" class="flex items-center justify-end gap-2 p-4 border-t">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
defineProps({
  show: Boolean,
});

defineEmits(['close']);
</script>
