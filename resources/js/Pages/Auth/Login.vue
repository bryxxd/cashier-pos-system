<template>
  <Head title="Login" />
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
      <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Cashier POS System</h1>

      <div v-if="form.errors.username" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ form.errors.username }}
      </div>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Username</label>
          <input
            v-model="form.username"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent"
            autofocus
            required
          />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent"
            required
          />
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-md transition disabled:opacity-50"
        >
          {{ form.processing ? 'Logging in...' : 'Login' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  username: '',
  password: '',
});

function submit() {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  });
}
</script>
