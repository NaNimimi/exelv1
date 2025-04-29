```vue
<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold text-white mb-6">Add Balance for {{ client?.first_name || 'Unknown' }}</h2>
    <form @submit.prevent="submit">
      <div class="space-y-6">
        <div>
          <label for="amount" class="block text-gray-400 text-sm uppercase tracking-wider mb-2">Amount</label>
          <input
            id="amount"
            type="number"
            step="0.01"
            v-model="form.amount"
            required
            class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <span v-if="form.errors.amount" class="text-red-400 text-sm mt-2">{{ form.errors.amount }}</span>
        </div>
        <div>
          <label for="comment" class="block text-gray-400 text-sm uppercase tracking-wider mb-2">Comment</label>
          <textarea
            id="comment"
            v-model="form.comment"
            class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          ></textarea>
          <span v-if="form.errors.comment" class="text-red-400 text-sm mt-2">{{ form.errors.comment }}</span>
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
          Submit
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  client: {
    type: Object,
    default: null
  },
  formData: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close'])

const form = useForm({
  operation: props.formData.operation || null,
  payment_type_id: props.formData.payment_type_id || null,
  amount: null,
  comment: null
})

console.log('AddBalance props:', { client: props.client, formData: props.formData })

const submit = () => {
  console.log('Submitting balance:', form.data())
  form.post(route('clients.addBalance', props.client?.id), {
    onSuccess: () => {
      console.log('Balance updated successfully')
      emit('close')
    },
    onError: (errors) => {
      console.error('Error updating balance:', errors)
    }
  })
}
</script>
```