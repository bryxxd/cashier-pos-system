<template>
  <Head title="Users" />
  <AppLayout>
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Users</h2>
      <button
        @click="showAddModal = true"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm transition"
      >
        + Add User
      </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="searchQuery"
        @input="debouncedSearch"
        type="text"
        placeholder="Search users..."
        class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800"
      />
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-900">{{ user.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ user.username }}</td>
            <td class="px-6 py-4 text-sm">
              <span
                :class="user.type === 'Administrator'
                  ? 'bg-purple-100 text-purple-800'
                  : 'bg-blue-100 text-blue-800'"
                class="px-2 py-1 text-xs font-medium rounded-full"
              >
                {{ user.type }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
            <td class="px-6 py-4 text-sm text-center space-x-2">
              <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800">Edit</button>
              <button @click="changePassword(user)" class="text-yellow-600 hover:text-yellow-800">Password</button>
              <button @click="confirmDelete(user)" class="text-red-600 hover:text-red-800">Delete</button>
            </td>
          </tr>
          <tr v-if="users.data.length === 0">
            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No users found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination :links="users.links" />

    <!-- Add User Modal -->
    <Modal :show="showAddModal" @close="showAddModal = false">
      <template #title>Add User</template>
      <form @submit.prevent="submitAdd">
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input v-model="addForm.name" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input v-model="addForm.username" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            <p v-if="addForm.errors.username" class="text-red-500 text-xs mt-1">{{ addForm.errors.username }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Type</label>
            <select v-model="addForm.type" class="mt-1 w-full px-3 py-2 border rounded-md">
              <option value="Cashier">Cashier</option>
              <option value="Administrator">Administrator</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input v-model="addForm.password" type="password" class="mt-1 w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input v-model="addForm.password_confirmation" type="password" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            <p v-if="addForm.errors.password" class="text-red-500 text-xs mt-1">{{ addForm.errors.password }}</p>
          </div>
        </div>
      </form>
      <template #footer>
        <button @click="showAddModal = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
        <button @click="submitAdd" :disabled="addForm.processing" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50">Save</button>
      </template>
    </Modal>

    <!-- Edit User Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false">
      <template #title>Edit User</template>
      <form @submit.prevent="submitEdit">
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input v-model="editForm.name" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input v-model="editForm.username" type="text" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            <p v-if="editForm.errors.username" class="text-red-500 text-xs mt-1">{{ editForm.errors.username }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Type</label>
            <select v-model="editForm.type" class="mt-1 w-full px-3 py-2 border rounded-md">
              <option value="Cashier">Cashier</option>
              <option value="Administrator">Administrator</option>
            </select>
          </div>
        </div>
      </form>
      <template #footer>
        <button @click="showEditModal = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
        <button @click="submitEdit" :disabled="editForm.processing" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">Update</button>
      </template>
    </Modal>

    <!-- Change Password Modal -->
    <Modal :show="showPasswordModal" @close="showPasswordModal = false">
      <template #title>Change Password</template>
      <form @submit.prevent="submitPassword">
        <div class="space-y-3">
          <p class="text-sm text-gray-600">Changing password for <strong>{{ passwordUser?.username }}</strong></p>
          <div>
            <label class="block text-sm font-medium text-gray-700">New Password</label>
            <input v-model="passwordForm.password" type="password" class="mt-1 w-full px-3 py-2 border rounded-md" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input v-model="passwordForm.password_confirmation" type="password" class="mt-1 w-full px-3 py-2 border rounded-md" required />
            <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
          </div>
        </div>
      </form>
      <template #footer>
        <button @click="showPasswordModal = false" class="px-4 py-2 text-sm border rounded-md hover:bg-gray-50">Cancel</button>
        <button @click="submitPassword" :disabled="passwordForm.processing" class="px-4 py-2 text-sm bg-yellow-600 text-white rounded-md hover:bg-yellow-700 disabled:opacity-50">Update Password</button>
      </template>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>Delete User</template>
      <p class="text-gray-600">Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>?</p>
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
  users: Object,
  filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const showAddModal = ref(false);
const showEditModal = ref(false);
const showPasswordModal = ref(false);
const showDeleteModal = ref(false);
const userToDelete = ref(null);
const passwordUser = ref(null);

let searchTimeout = null;
function debouncedSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/users', { search: searchQuery.value }, { preserveState: true, replace: true });
  }, 300);
}

const addForm = useForm({
  name: '',
  username: '',
  type: 'Cashier',
  password: '',
  password_confirmation: '',
});

const editForm = useForm({
  _method: 'PUT',
  name: '',
  username: '',
  type: 'Cashier',
});
let editUserId = null;

const passwordForm = useForm({
  _method: 'PUT',
  password: '',
  password_confirmation: '',
});
let passwordUserId = null;

function editUser(user) {
  editUserId = user.id;
  editForm.name = user.name;
  editForm.username = user.username;
  editForm.type = user.type;
  showEditModal.value = true;
}

function changePassword(user) {
  passwordUserId = user.id;
  passwordUser.value = user;
  passwordForm.reset();
  showPasswordModal.value = true;
}

function confirmDelete(user) {
  userToDelete.value = user;
  showDeleteModal.value = true;
}

function submitAdd() {
  addForm.post('/users', {
    onSuccess: () => {
      showAddModal.value = false;
      addForm.reset();
    },
  });
}

function submitEdit() {
  editForm.post(`/users/${editUserId}`, {
    onSuccess: () => {
      showEditModal.value = false;
    },
  });
}

function submitPassword() {
  passwordForm.post(`/users/${passwordUserId}/password`, {
    onSuccess: () => {
      showPasswordModal.value = false;
      passwordForm.reset();
    },
  });
}

function submitDelete() {
  router.delete(`/users/${userToDelete.value.id}`, {
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric',
  });
}
</script>
