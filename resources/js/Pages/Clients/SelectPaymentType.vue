<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold text-white mb-6">Select Payment Type for {{ client?.first_name || 'Unknown' }}</h2>
    <form @submit.prevent="submit">
      <div class="space-y-6">
        <div>
          <label class="block text-gray-1000 text-sm uppercase tracking-wider mb-2">Payment Type</label>
          <div class="flex flex-wrap gap-4">
            <button
              v-for="type in paymentTypes"
              :key="type.id"
              type="button"
              :class="[
                'px-6 py-3 rounded-lg text-white transition-colors duration-200',
                form.payment_type_id === type.id ? 'bg-indigo-600 hover:bg-indigo-500' : 'bg-indigo-600 hover:bg-indigo-500'
              ]"
              :disabled="!paymentTypes || paymentTypes.length === 0"
              @click="form.payment_type_id = type.id; submit()"
            >
              {{ type.name }}
            </button>
            <span
              v-if="!paymentTypes || paymentTypes.length === 0"
              class="text-gray-400 text-sm"
            >
              No payment types available
            </span>
          </div>
          <input type="hidden" v-model="form.payment_type_id" required />
          <span v-if="form.errors.payment_type_id" class="text-red-400 text-sm mt-2">{{ form.errors.payment_type_id }}</span>
        </div>
      </div>
      <div class="flex justify-end space-x-4 mt-6">
        <button
          type="button"
          @click="emit('close')"
          class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500 transition-colors duration-200"
        >
          Back
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
