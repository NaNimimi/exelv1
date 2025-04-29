```vue
<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold text-white mb-6">Select Payment Type for {{ client?.first_name || 'Unknown' }}</h2>
    <form @submit.prevent="submit">
      <div class="space-y-6">
        <div>
          <label for="payment_type_id" class="block text-gray-400 text-sm uppercase tracking-wider mb-2">Payment Type</label>
          <select
            id="payment_type_id"
            v-model="form.payment_type_id"
            required
            class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            :disabled="!paymentTypes || paymentTypes.length === 0"
          >
            <option v-for="type in paymentTypes" :key="type.id" :value="type.id">
              {{ type.name }} ({{ type.currency }})
            </option>
            <option v-if="!paymentTypes || paymentTypes.length === 0" disabled>No payment types available</option>
          </select>
          <span v-if="form.errors.payment_type_id" class="text-red-400 text-sm mt-2">{{ form.errors.payment_type_id }}</span>
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
          :disabled="form.processing || !paymentTypes || paymentTypes.length === 0"
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

const props = defineProps({
  client: {
    type: Object,
    default: null
  },
  paymentTypes: {
    type: Array,
    default: () => []
  },
  formData: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'openBalanceModal'])

const form = useForm({
  operation: props.formData.operation || null,
  payment_type_id: null
})

console.log('SelectPaymentType props:', { client: props.client, paymentTypes: props.paymentTypes, formData: props.formData })

const submit = () => {
  console.log('Submitting payment type:', form.data())
  emit('openBalanceModal', form.data())
}
</script>
```