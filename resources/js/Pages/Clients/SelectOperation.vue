```vue
<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold text-white mb-6">Select Operation for {{ client.first_name }}</h2>
    <form @submit.prevent="submit">
      <div class="space-y-6">
        <div>
          <label for="operation" class="block text-gray-400 text-sm uppercase tracking-wider mb-2">Operation</label>
          <select
            id="operation"
            v-model="form.operation"
            required
            class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="add">Пополнить</option>
            <option value="subtract">Отнять</option>
          </select>
          <span v-if="form.errors.operation" class="text-red-400 text-sm mt-2">{{ form.errors.operation }}</span>
        </div>
      </div>
      <div class="flex justify-end space-x-4 mt-6">
        <button
          type="button"
          @click="emit('close')"
          class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-500"
        >
          Back
        </button>
        <button
          type="submit"
          :disabled="form.processing"
          class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500"
        >
          Next
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

defineProps({
  client: Object
})

const emit = defineEmits(['close', 'openPaymentTypeModal'])

const form = useForm({
  operation: 'add'
})

const submit = () => {
  console.log('Submitting operation:', form.data())
  emit('openPaymentTypeModal', form.data())
}
</script>
```