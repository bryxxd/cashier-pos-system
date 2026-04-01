<template>
  <a
    :href="href"
    class="px-3 py-2 rounded-md text-sm font-medium transition"
    :class="active
      ? 'bg-gray-900 text-white'
      : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
    @click.prevent="navigate"
  >
    <slot />
  </a>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
  href: String,
  active: Boolean,
});

function navigate() {
  router.visit(props.href);
}
</script>
