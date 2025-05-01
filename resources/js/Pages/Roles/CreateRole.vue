<template>
  <div class="p-6 bg-gray-800 rounded-2xl shadow-2xl">
    <h2 class="text-2xl font-bold text-white mb-6">Rol yaratish</h2>

    <form @submit.prevent="submit" class="space-y-6">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-300">Rol nomi</label>
        <input
          id="name"
          v-model.trim="form.name"
          type="text"
          class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 transition"
          :class="{ 'border-red-500': errors.name }"
        />
        <p v-if="errors.name" class="mt-1 text-sm text-red-400">{{ errors.name }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-300">Ruxsatlar tanlang</label>
        <div class="mt-2 flex flex-wrap gap-2 max-h-40 overflow-y-auto bg-gray-700 rounded-lg p-2">
          <div
            v-for="permission in permissions"
            :key="permission.id"
            @click="togglePermission(permission.id)"
            class="w-30 h-10 rounded-full border-2 cursor-pointer flex items-center justify-center text-sm text-white transition"
            :class="{
              'border-gray-500 bg-gray-600': !form.permission_ids.includes(permission.id),
              'border-blue-500 bg-blue-500': form.permission_ids.includes(permission.id),
            }"
            :title="permission.name"
          >
            {{ permission.name }}
          </div>
        </div>
        <input type="hidden" name="permission_ids" v-model="form.permission_ids" />
        <p v-if="errors.permission_ids" class="mt-1 text-sm text-red-400">{{ errors.permission_ids }}</p>
      </div>

      <div class="flex gap-4">
        <button
          type="submit"
          class="bg-indigo-600 text-white px-6 py-3 rounded-full hover:bg-indigo-500 transition transform hover:scale-105"
          :class="{ 'bg-red-600 hover:bg-red-500': Object.keys(errors).length }"
        >
          Yaratish
        </button>
        <button
          type="button"
          @click="$emit('close')"
          class="bg-gray-600 text-white px-6 py-3 rounded-full hover:bg-gray-500 transition transform hover:scale-105"
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

defineProps({
  permissions: Array,
});

const form = reactive({
  name: '',
  permission_ids: [],
});

const errors = ref({});

const emit = defineEmits(['close', 'created']);

const togglePermission = (permissionId) => {
  if (form.permission_ids.includes(permissionId)) {
    form.permission_ids = form.permission_ids.filter((id) => id !== permissionId);
  } else {
    form.permission_ids.push(permissionId);
  }
};

const submit = () => {
  router.post(route('roles.store'), form, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      // Emit the 'created' event with the new role data
      emit('created', page.props.role);
      // Emit the 'close' event to close the modal
      emit('close');
      // Reset the form
      form.name = '';
      form.permission_ids = [];
      errors.value = {};
    },
    onError: (err) => {
      errors.value = err;
    },
  });
};
</script>