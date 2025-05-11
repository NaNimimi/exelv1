<template>
  <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Ruxsat olib tashlash</h2>

    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Ruxsatlar tanlang</label>
        <div class="mt-2 flex flex-wrap gap-2 max-h-40 overflow-y-auto bg-gray-100 dark:bg-gray-700 rounded-lg p-2">
          <div
            v-for="permission in role.permissions"
            :key="permission.id"
            @click="togglePermission(permission.id)"
            class="w-30 h-10 rounded-full border-2 cursor-pointer flex items-center justify-center text-sm text-gray-900 dark:text-white transition"
            :class="{
              'border-gray-300 bg-gray-200 dark:border-gray-500 dark:bg-gray-600': !form.permission_ids.includes(permission.id),
              'border-blue-500 bg-blue-500 text-white': form.permission_ids.includes(permission.id),
            }"
            :title="permission.name"
          >
            {{ permission.name }}
          </div>
        </div>
        <input type="hidden" name="permission_ids" v-model="form.permission_ids" />
        <p v-if="errors.permission_ids" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.permission_ids }}</p>
      </div>

      <div class="flex gap-4">
        <button
          type="submit"
          class="bg-red-600 text-white px-6 py-3 rounded-full hover:bg-red-700 dark:hover:bg-red-800 transition transform hover:scale-105"
          :class="{ 'bg-red-600 hover:bg-red-700 dark:hover:bg-red-800': Object.keys(errors).length }"
        >
          Olib tashlash
        </button>
        <button
          type="button"
          @click="$emit('close')"
          class="bg-gray-500 text-white px-6 py-3 rounded-full hover:bg-gray-600 dark:hover:bg-gray-700 transition transform hover:scale-105"
        >
          Bekor qilish
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  role: Object,
});

const form = reactive({
  permission_ids: [],
});

const errors = ref({});

const togglePermission = (permissionId) => {
  if (form.permission_ids.includes(permissionId)) {
    form.permission_ids = form.permission_ids.filter((id) => id !== permissionId);
  } else {
    form.permission_ids.push(permissionId);
  }
};

const submit = () => {
  router.post(route('roles.permissions.revoke', props.role.id), form, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      form.permission_ids = [];
      errors.value = {};
      emit('revoked', page.props.role); // Emit the updated role
      emit('close');
    },
    onError: (err) => {
      errors.value = err;
    },
  });
};

const emit = defineEmits(['close', 'revoked']);
</script>