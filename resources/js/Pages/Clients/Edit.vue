
<template>
  <div class="p-8">
    <!-- Modal Header -->
    <div class="flex justify-between items-center mb-8">
      <h2 class="text-2xl font-bold text-white">Klient ozgartirish</h2>
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
    <form @submit.prevent="validateBeforeSave" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- First Column -->
      <div class="space-y-6">
        <div>
          <label class="text-gray-400 text-sm uppercase tracking-wider">Ism *</label>
          <input 
            v-model="form.first_name" 
            type="text" 
            :class="{'border-red-500': fieldErrors.first_name, 'border-gray-600': !fieldErrors.first_name}"
            class="mt-2 w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
            placeholder="Enter first name"
            @blur="validateField('first_name')"
          />
          <span v-if="fieldErrors.first_name" class="text-red-400 text-sm mt-1">{{ fieldErrors.first_name }}</span>
        </div>

        <div>
          <label class="text-gray-400 text-sm uppercase tracking-wider">Familiyasi *</label>
          <input 
            v-model="form.lastname" 
            type="text" 
            :class="{'border-red-500': fieldErrors.lastname, 'border-gray-600': !fieldErrors.lastname}"
            class="mt-2 w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
            placeholder="Enter last name"
            @blur="validateField('lastname')"
          />
          <span v-if="fieldErrors.lastname" class="text-red-400 text-sm mt-1">{{ fieldErrors.lastname }}</span>
        </div>

        <div>
          <label class="text-gray-400 text-sm uppercase tracking-wider">Companiya ismi *</label>
          <input 
            v-model="form.company_name" 
            type="text" 
            :class="{'border-red-500': fieldErrors.company_name, 'border-gray-600': !fieldErrors.company_name}"
            class="mt-2 w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
            placeholder="Enter company name"
            @blur="validateField('company_name')"
          />
          <span v-if="fieldErrors.company_name" class="text-red-400 text-sm mt-1">{{ fieldErrors.company_name }}</span>
        </div>
      </div>

      <!-- Second Column -->
      <div class="space-y-6">
        <div class="md:col-span-2">
          <label class="text-gray-400 text-sm uppercase tracking-wider">Address *</label>
          <input 
            v-model="form.address" 
            type="text" 
            :class="{'border-red-500': fieldErrors.address, 'border-gray-600': !fieldErrors.address}"
            class="mt-2 w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
            placeholder="Enter address"
            @blur="validateField('address')"
          />
          <span v-if="fieldErrors.address" class="text-red-400 text-sm mt-1">{{ fieldErrors.address }}</span>
        </div>

        <div class="md:col-span-2">
          <label class="text-gray-400 text-sm uppercase tracking-wider">Phones *</label>
          <div v-for="(phone, index) in form.phones" :key="index" class="flex items-center mt-2">
            <input 
              v-model="form.phones[index]" 
              type="text" 
              :class="{'border-red-500': phoneErrors[index], 'border-gray-600': !phoneErrors[index]}"
              class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
              placeholder="(+998) bilan bolishi shart"
              @blur="validatePhone(index)"
            />
            <button 
              type="button" 
              @click="showDeleteModal(index)" 
              class="ml-4 text-red-400 hover:text-red-300 transition duration-150 ease-in-out"
            >
              <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
              </svg>
            </button>
          </div>
          <button 
            type="button" 
            @click="addPhone" 
            class="mt-4 flex items-center text-indigo-400 hover:text-indigo-300 transition duration-150 ease-in-out"
          >
            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
            </svg>
            Nomer Qoshish
          </button>
          <span v-if="fieldErrors.phones" class="text-red-400 text-sm mt-1">{{ fieldErrors.phones }}</span>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="md:col-span-2 flex justify-end space-x-4">
        <button
          @click="$emit('close')"
          class="px-6 py-3 bg-gray-700 text-white rounded-full hover:bg-gray-600 transition duration-300"
        >
          Cancel
        </button>
        <button 
          type="submit" 
          class="flex items-center bg-indigo-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-indigo-500 transition-all duration-300 ease-in-out transform hover:scale-105"
          :disabled="form.processing"
        >
          <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17 3H5c-1.11 0-2 .89-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
          </svg>
          {{ form.processing ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </form>

    <!-- Delete Phone Confirmation Modal -->
    <div v-if="showDeletePhoneModal !== null" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
      <div class="bg-gray-800 rounded-2xl p-8 max-w-md w-full shadow-2xl">
        <h2 class="text-2xl font-bold mb-4">Confirm Deletion</h2>
        <p class="text-gray-300 mb-6">Are you sure you want to delete this phone number?</p>
        <div class="flex justify-end space-x-4">
          <button 
            @click="showDeletePhoneModal = null" 
            class="px-6 py-2 rounded-full bg-gray-700 hover:bg-gray-600 transition duration-200"
          >
            Cancel
          </button>
          <button 
            @click="confirmDeletePhone" 
            class="px-6 py-2 rounded-full bg-red-600 hover:bg-red-500 transition duration-200"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Success Notification -->
    <div v-if="showSuccessNotification" class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center z-50">
      <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
      </svg>
      <span>Changes saved successfully!</span>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  client: {
    type: Object,
    default: () => ({ phones: [] })
  }
})

const emit = defineEmits(['close'])

// Modal and notification states
const showDeletePhoneModal = ref(null)
const showSuccessNotification = ref(false)

// Form validation errors
const fieldErrors = ref({
  first_name: '',
  lastname: '',
  company_name: '',
  address: '',
  phones: ''
})
const phoneErrors = ref([])

// Initialize form
const form = useForm({
  first_name: props.client?.first_name || '',
  lastname: props.client?.lastname || '',
  company_name: props.client?.company_name || '',
  address: props.client?.address || '',
  phones: props.client?.phones?.map(phone => phone.phone) || []
})

// Initialize phone errors array
phoneErrors.value = form.phones.map(() => false)

const addPhone = () => {
  form.phones.push('')
  phoneErrors.value.push(false)
}

const showDeleteModal = (index) => {
  showDeletePhoneModal.value = index
}

const confirmDeletePhone = () => {
  removePhone(showDeletePhoneModal.value)
  showDeletePhoneModal.value = null
}

const removePhone = (index) => {
  form.phones.splice(index, 1)
  phoneErrors.value.splice(index, 1)
  validatePhones()
}

const validateField = (field) => {
  if (!form[field] || form[field].trim() === '') {
    fieldErrors.value[field] = 'This field is required'
  } else {
    fieldErrors.value[field] = ''
  }
}

const validatePhone = (index) => {
  if (!form.phones[index] || form.phones[index].trim() === '') {
    phoneErrors.value[index] = true
    fieldErrors.value.phones = 'Please fill all phone fields'
  } else {
    phoneErrors.value[index] = false
    fieldErrors.value.phones = phoneErrors.value.some(err => err) ? 'Please fill all phone fields' : ''
  }
}

const validatePhones = () => {
  const hasEmptyPhone = form.phones.some(phone => !phone || phone.trim() === '')
  if (hasEmptyPhone) {
    fieldErrors.value.phones = 'Please fill all phone fields'
    phoneErrors.value = form.phones.map(phone => !phone || phone.trim() === '')
  } else {
    fieldErrors.value.phones = ''
    phoneErrors.value = form.phones.map(() => false)
  }
}

const validateForm = () => {
  validateField('first_name')
  validateField('lastname')
  validateField('company_name')
  validateField('address')
  validatePhones()

  return !Object.values(fieldErrors.value).some(error => error) && 
         !phoneErrors.value.some(error => error) &&
         form.phones.length > 0 &&
         form.phones.every(phone => phone && phone.trim() !== '')
}

const validateBeforeSave = () => {
  if (validateForm()) {
    submit()
  }
}

const submit = () => {
  form.put(`/clients/${props.client?.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showSuccessNotification.value = true
      setTimeout(() => {
        showSuccessNotification.value = false
        emit('close')
      }, 3000)
    },
    onError: (errors) => {
      Object.keys(errors).forEach(field => {
        if (field.startsWith('phones.')) {
          const index = parseInt(field.split('.')[1])
          phoneErrors.value[index] = true
          fieldErrors.value.phones = errors[field]
        } else if (field in fieldErrors.value) {
          fieldErrors.value[field] = errors[field]
        }
      })
    }
  })
}
</script>
```