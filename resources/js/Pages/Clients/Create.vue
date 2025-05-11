<template>
  <div class="p-8 bg-white dark:bg-gray-800 rounded-2xl">
    <!-- Modal Header -->
    <div class="flex justify-between items-center mb-8">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Klient Qoshish</h2>
      <button 
        @click="$emit('close')"
        class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition duration-150"
      >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Form Content -->
    <form @submit.prevent="validateAndSubmit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- First Column -->
      <div class="space-y-6">
        <div>
          <label class="text-gray-600 dark:text-gray-400 text-sm uppercase tracking-wider">Ism *</label>
          <input 
            v-model="form.first_name" 
            :class="{
              'border-red-500': errors.first_name, 
              'border-gray-300 dark:border-gray-600': !errors.first_name
            }"
            class="mt-2 w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
            placeholder="Enter first name"
            @blur="validateField('first_name')"
          />
          <span v-if="errors.first_name" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.first_name }}</span>
        </div>

        <div>
          <label class="text-gray-600 dark:text-gray-400 text-sm uppercase tracking-wider">Familiya *</label>
          <input 
            v-model="form.lastname" 
            :class="{
              'border-red-500': errors.lastname, 
              'border-gray-300 dark:border-gray-600': !errors.lastname
            }"
            class="mt-2 w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
            placeholder="Enter last name"
            @blur="validateField('lastname')"
          />
          <span v-if="errors.lastname" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.lastname }}</span>
        </div>

        <div>
          <label class="text-gray-600 dark:text-gray-400 text-sm uppercase tracking-wider">Companiya Ismi *</label>
          <input 
            v-model="form.company_name" 
            :class="{
              'border-red-500': errors.company_name, 
              'border-gray-300 dark:border-gray-600': !errors.company_name
            }"
            class="mt-2 w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
            placeholder="Enter company name"
            @blur="validateField('company_name')"
          />
          <span v-if="errors.company_name" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.company_name }}</span>
        </div>
      </div>

      <!-- Second Column -->
      <div class="space-y-6">
        <div class="md:col-span-2">
          <label class="text-gray-600 dark:text-gray-400 text-sm uppercase tracking-wider">Address *</label>
          <textarea 
            v-model="form.address"
            :class="{
              'border-red-500': errors.address, 
              'border-gray-300 dark:border-gray-600': !errors.address
            }"
            class="mt-2 w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
            placeholder="Enter full address"
            rows="3"
            @blur="validateField('address')"
          ></textarea>
          <span v-if="errors.address" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.address }}</span>
        </div>

        <div class="md:col-span-2">
          <label class="text-gray-600 dark:text-gray-400 text-sm uppercase tracking-wider">Nomeri *</label>
          <div v-for="(phone, index) in form.phones" :key="index" class="flex items-center mt-2">
            <input 
              v-model="form.phones[index]" 
              :class="{
                'border-red-500': phoneErrors[index], 
                'border-gray-300 dark:border-gray-600': !phoneErrors[index]
              }"
              class="w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-3 border-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
              placeholder="+998......"
              @blur="validatePhone(index)"
            />
            <button 
              v-if="index > 0"
              type="button" 
              @click="removePhone(index)" 
              class="ml-4 text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 transition duration-150 ease-in-out"
            >
              <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
              </svg>
            </button>
          </div>
          <button 
            type="button" 
            @click="addPhone" 
            class="mt-4 flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition duration-150 ease-in-out"
          >
            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
            </svg>
            Nomer qoshish
          </button>
          <span v-if="errors.phones" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.phones }}</span>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="md:col-span-2 flex justify-end space-x-4">
        <button
          @click="$emit('close')"
          class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-full hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-300"
        >
          Bekor qilish
        </button>
        <button 
          type="submit" 
          class="flex items-center bg-indigo-600 text-white px-8 py-4 rounded-full shadow-lg hover:bg-indigo-500 dark:hover:bg-indigo-700 transition-all duration-300 ease-in-out transform hover:scale-105"
          :disabled="form.processing"
        >
          <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17 3H5c-1.11 0-2 .89-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
          </svg>
          {{ form.processing ? 'Saving...' : 'Saqlash' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const emit = defineEmits(['close'])

const form = useForm({
  first_name: '',
  lastname: '',
  company_name: '',
  address: '',
  phones: ['']
})

const errors = ref({
  first_name: '',
  lastname: '',
  company_name: '',
  address: '',
  phones: ''
})

const phoneErrors = ref([])

const addPhone = () => {
  form.phones.push('')
  phoneErrors.value.push('')
}

const removePhone = (index) => {
  form.phones.splice(index, 1)
  phoneErrors.value.splice(index, 1)
  validatePhones()
}

const validateField = (field) => {
  if (!form[field] || form[field].trim() === '') {
    errors.value[field] = 'This field is required'
  } else {
    errors.value[field] = ''
  }
}

const validatePhone = (index) => {
  if (!form.phones[index] || form.phones[index].trim() === '') {
    phoneErrors.value[index] = true
    errors.value.phones = 'At least one phone number is required'
  } else {
    phoneErrors.value[index] = false
    errors.value.phones = phoneErrors.value.some(err => err) ? 'Please fill all phone fields' : ''
  }
}

const validatePhones = () => {
  const hasEmptyPhone = form.phones.some(phone => !phone || phone.trim() === '')
  if (hasEmptyPhone) {
    errors.value.phones = 'Please fill all phone fields'
    phoneErrors.value = form.phones.map(phone => !phone || phone.trim() === '')
  } else {
    errors.value.phones = ''
    phoneErrors.value = form.phones.map(() => false)
  }
}

const validateForm = () => {
  validateField('first_name')
  validateField('lastname')
  validateField('company_name')
  validateField('address')
  validatePhones()

  return !Object.values(errors.value).some(error => error) && 
         !phoneErrors.value.some(error => error) &&
         form.phones.length > 0 &&
         form.phones.every(phone => phone && phone.trim() !== '')
}

const validateAndSubmit = () => {
  if (validateForm()) {
    submit()
  }
}

const submit = () => {
  form.post('/clients', {
    preserveScroll: true,
    onSuccess: () => {
      emit('close')
      form.reset()
      form.clearErrors()
      errors.value = {
        first_name: '',
        lastname: '',
        company_name: '',
        address: '',
        phones: ''
      }
      phoneErrors.value = ['']
    },
    onError: (formErrors) => {
      Object.keys(formErrors).forEach(field => {
        if (field.startsWith('phones.')) {
          const index = parseInt(field.split('.')[1])
          phoneErrors.value[index] = true
          errors.value.phones = formErrors[field]
        } else if (field in errors.value) {
          errors.value[field] = formErrors[field]
        }
      })
    }
  })
}
</script>