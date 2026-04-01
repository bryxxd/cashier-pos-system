<template>
  <nav v-if="links.length > 3" class="flex items-center justify-between mt-4">
    <div class="flex-1 flex justify-between sm:hidden">
      <Link
        v-if="links[0].url"
        :href="links[0].url"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border rounded-md hover:bg-gray-50"
      >
        Previous
      </Link>
      <Link
        v-if="links[links.length - 1].url"
        :href="links[links.length - 1].url"
        class="ml-3 px-4 py-2 text-sm font-medium text-gray-700 bg-white border rounded-md hover:bg-gray-50"
      >
        Next
      </Link>
    </div>
    <div class="hidden sm:flex sm:items-center sm:gap-1">
      <template v-for="(link, i) in links" :key="i">
        <Link
          v-if="link.url"
          :href="link.url"
          class="px-3 py-1 text-sm border rounded"
          :class="link.active ? 'bg-gray-800 text-white border-gray-800' : 'text-gray-600 hover:bg-gray-50'"
          v-html="link.label"
        />
        <span
          v-else
          class="px-3 py-1 text-sm text-gray-400 border rounded"
          v-html="link.label"
        />
      </template>
    </div>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: Array,
});
</script>
