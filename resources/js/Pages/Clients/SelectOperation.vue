<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold text-white mb-6">Select Operation for {{ client.first_name }}</h2>
    <form @submit.prevent="submit">
      <div class="space-y-6">
        <div>
          <label class="block text-gray-400 text-sm uppercase tracking-wider mb-2">Operation</label>
          <div class="flex space-x-4">
            <button
              type="button"
              :class="[
                'flex-1 px-6 py-3 rounded-lg text-white transition-colors duration-200',
                form.operation === 'add' ? 'bg-indigo-600 hover:bg-indigo-500' : 'bg-gray-600 hover:bg-gray-500'
              ]"
              @click="form.operation = 'add'; submit()"
            >
              Пополнить
            </button>
            <button
              type="button"
              :class="[
                'flex-1 px-6 py-3 rounded-lg text-white transition-colors duration-200',
                form.operation === 'subtract' ? 'bg-indigo-600 hover:bg-indigo-500' : 'bg-gray-600 hover:bg-gray-500'
              ]"
              @click="form.operation = 'subtract'; submit()"
            >
              Отнять
            </button>
          </div>
          <input type="hidden" v-model="form.operation" required />
          <span v-if="form.errors.operation" class="text-red-400 text-sm mt-2">{{ form.errors.operation }}</span>
        </div>
      </div>
      <div class="flex justify-end space-x-4 mt-6">
        <button
          type="button"
          @click="emit('close')"
          class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-500 transition-colors duration-200"
        >
          Back
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