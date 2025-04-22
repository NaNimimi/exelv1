```vue
<template>
  <div class="p-8">
    <!-- Modal Header -->
    <div class="flex justify-between items-center mb-8">
      <h2 class="text-2xl font-bold text-white">PUL QOSHISH</h2>
      <button 
        @click="$emit('close')"
        class="text-gray-400 hover:text-white transition duration-150"
      >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Form Content -->
    <form @submit.prevent="validateAndSubmit" class="grid grid-cols-1 gap-6">
      <div>
        <label class="text-gray-400 text-sm uppercase tracking-wider">SUMMA *</label>
        <input 
          v-model="form.amount" 
          type="number" 
          step="0.01"
          :class="{'border-red-500': errors.amount, 'border-transparent': !errors.amount}"
          class="mt-2 w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
          placeholder="SUMMA KIRITING"
          @blur="validateField('amount')"
        />
        <span v-if="errors.amount" class="text-red-400 text-sm mt-1">{{ errors.amount }}</span>
      </div>

      <div>
        <label class="text-gray-400 text-sm uppercase tracking-wider">Izoh *</label>
        <textarea 
          v-model="form.comment"
          :class="{'border-red-500': errors.comment, 'border-transparent': !errors.comment}"
          class="mt-2 w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
          placeholder="Izoh"
          rows="4"
          @blur="validateField('comment')"
        ></textarea>
        <span v-if="errors.comment" class="text-red-400 text-sm mt-1">{{ errors.comment }}</span>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-end space-x-4">
        <button
          @click="$emit('close')"
          class="px-6 py-3 bg-gray-700 text-white rounded-full hover:bg-gray-600 transition duration-300"
        >
          Bekor qilish
        </button>
        <button 
          type="submit" 
          class="flex items-center bg-indigo-600 text-white px-8 py-4 rounded-full shadow-lg hover:bg-indigo-500 transition-all duration-300 ease-in-out transform hover:scale-105"
          :disabled="form.processing"
        >
          <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17 3H5c-1.11 0-2 .89-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
          </svg>
          {{ form.processing ? 'Saqlanypti...' : 'PUL QOSHISH' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const { client } = defineProps(['client'])

const emit = defineEmits(['close'])

const form = useForm({
  amount: '',
  comment: ''
})

const errors = ref({
  amount: '',
  comment: ''
})

const validateField = (field) => {
  if (!form[field] || form[field].toString().trim() === '') {
    errors.value[field] = 'This field is required'
  } else if (field === 'amount' && (form[field] <= 0 || isNaN(form[field]))) {
    errors.value[field] = 'Amount must be a positive number'
  } else {
    errors.value[field] = ''
  }
}

const validateForm = () => {
  validateField('amount')
  validateField('comment')

  return !Object.values(errors.value).some(error => error)
}

const validateAndSubmit = () => {
  if (validateForm()) {
    submit()
  }
}

const submit = () => {
  form.post(`/clients/${client.id}/balance`, {
    preserveScroll: true,
    onSuccess: () => {
      emit('close')
      form.reset()
      errors.value = { amount: '', comment: '' }
    },
    onError: (formErrors) => {
      Object.keys(formErrors).forEach(field => {
        errors.value[field] = formErrors[field]
      })
    }
  })
}
</script>
```