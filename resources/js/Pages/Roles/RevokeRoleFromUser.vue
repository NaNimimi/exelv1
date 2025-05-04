<template>
    <div class="p-6 bg-gray-800 rounded-2xl shadow-2xl">
      <h2 class="text-2xl font-bold text-white mb-6">Foydalanuvchidan rol olib tashlash</h2>
  
      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label for="user" class="block text-sm font-medium text-gray-300">Foydalanuvchi tanlang</label>
          <select
            id="user"
            v-model.number="form.user_id"
            class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 transition"
            :class="{ 'border-red-500': errors.user_id }"
          >
            <option value="" disabled>Tanlang</option>
            <option
              v-for="user in usersWithRole"
              :key="user.id"
              :value="user.id"
            >
              {{ user.name }} ({{ user.email }})
            </option>
          </select>
          <p v-if="errors.user_id" class="mt-1 text-sm text-red-400">{{ errors.user_id }}</p>
        </div>
  
        <div class="flex gap-4">
          <button
            type="submit"
            class="bg-red-600 text-white px-6 py-3 rounded-full hover:bg-red-500 transition transform hover:scale-105"
            :class="{ 'bg-red-600 hover:bg-red-500': Object.keys(errors).length }"
          >
            Olib tashlash
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
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  role: Object,
  users: Array,
});

const form = ref({
  user_id: '',
});

const errors = ref({});

const usersWithRole = computed(() => {
  return props.users.filter(
    (user) => user.roles.some((r) => r.id === props.role.id)
  );
});

const submit = () => {
  router.post(route('users.roles.revoke', form.value.user_id), { role_id: props.role.id }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      form.value.user_id = '';
      errors.value = {};
      emit('close');
    },
    onError: (err) => {
      errors.value = err;
    },
  });
};

const emit = defineEmits(['close']);
</script>