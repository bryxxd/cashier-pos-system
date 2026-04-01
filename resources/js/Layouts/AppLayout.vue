<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gray-800 text-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center space-x-8">
            <h1 class="text-xl font-semibold">Cashier POS System</h1>
            <div class="hidden md:flex space-x-4">
              <NavLink href="/dashboard" :active="currentRoute === 'dashboard'">Dashboard</NavLink>
              <NavLink href="/pos" :active="currentRoute === 'pos.index'">POS System</NavLink>
              <template v-if="isAdmin">
                <NavLink href="/products" :active="currentRoute === 'products.index'">Products</NavLink>
                <NavLink href="/users" :active="currentRoute === 'users.index'">Users</NavLink>
                <NavLink href="/transactions" :active="currentRoute === 'transactions.index'">Transactions</NavLink>
              </template>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-300">
              {{ auth.user?.name }} ({{ auth.user?.type }})
            </span>
            <button
              @click="logout"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm transition"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Flash Messages -->
    <div v-if="flash.success" class="max-w-7xl mx-auto px-4 mt-4">
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        {{ flash.success }}
        <button @click="dismissFlash" class="absolute top-0 right-0 px-4 py-3">
          <span class="text-green-500">&times;</span>
        </button>
      </div>
    </div>
    <div v-if="flash.error" class="max-w-7xl mx-auto px-4 mt-4">
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        {{ flash.error }}
      </div>
    </div>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const page = usePage();
const auth = computed(() => page.props.auth);
const flash = computed(() => page.props.flash);
const currentRoute = computed(() => page.url);
const isAdmin = computed(() => auth.value.user?.type === 'Administrator');

function logout() {
  router.post('/logout');
}

function dismissFlash() {
  page.props.flash.success = null;
}
</script>
