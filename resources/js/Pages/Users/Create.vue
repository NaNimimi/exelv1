<template>
  <div class="p-6 bg-gray-800 rounded-2xl shadow-2xl">
    <h2 class="text-2xl font-bold text-white mb-6">Foydalanuvchi yaratish</h2>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Name Field -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-300">Ism</label>
        <input
          id="name"
          v-model.trim="form.name"
          type="text"
          class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 transition"
          :class="{ 'border-red-500': localErrors.name || errors.name }"
        />
        <p v-if="localErrors.name" class="mt-1 text-sm text-red-400">{{ localErrors.name }}</p>
        <p v-if="errors.name" class="mt-1 text-sm text-red-400">{{ errors.name }}</p>
      </div>

      <!-- Email Field -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
        <input
          id="email"
          v-model.trim="form.email"
          type="email"
          class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 transition"
          :class="{ 'border-red-500': localErrors.email || errors.email }"
          @input="checkEmail"
        />
        <p v-if="localErrors.email" class="mt-1 text-sm text-red-400">{{ localErrors.email }}</p>
        <p v-if="errors.email" class="mt-1 text-sm text-red-400">{{ errors.email }}</p>
      </div>

      <!-- Password Field -->
      <div class="relative">
        <label for="password" class="block text-sm font-medium text-gray-300">Parol</label>
        <input
          id="password"
          v-model="form.password"
          :type="showPassword ? 'text' : 'password'"
          class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 transition"
          :class="{ 'border-red-500': localErrors.password || errors.password }"
        />
        <button
          type="button"
          class="absolute inset-y-0 right-0 pr-3 flex items-center mt-6"
          @click="showPassword = !showPassword"
        >
          <font-awesome-icon
            :icon="showPassword ? ['fas', 'eye-slash'] : ['fas', 'eye']"
            class="text-gray-400 hover:text-gray-200 w-5 h-5"
          />
        </button>
        <p v-if="localErrors.password" class="mt-1 text-sm text-red-400">{{ localErrors.password }}</p>
        <p v-if="errors.password" class="mt-1 text-sm text-red-400">{{ errors.password }}</p>
      </div>

      <!-- Password Confirmation Field -->
      <div class="relative">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Parolni tasdiqlash</label>
        <input
          id="password_confirmation"
          v-model="form.password_confirmation"
          :type="showPasswordConfirmation ? 'text' : 'password'"
          class="mt-1 w-full rounded-lg border-gray-600 bg-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 transition"
          :class="{ 'border-red-500': localErrors.password_confirmation }"
        />
        <button
          type="button"
          class="absolute inset-y-0 right-0 pr-3 flex items-center mt-6"
          @click="showPasswordConfirmation = !showPasswordConfirmation"
        >
          <font-awesome-icon
            :icon="showPasswordConfirmation ? ['fas', 'eye-slash'] : ['fas', 'eye']"
            class="text-gray-400 hover:text-gray-200 w-5 h-5"
          />
        </button>
        <p v-if="localErrors.password_confirmation" class="mt-1 text-sm text-red-400">{{ localErrors.password_confirmation }}</p>
      </div>

      <!-- Buttons -->
      <div class="flex gap-4">
        <button
          type="submit"
          class="text-white px-6 py-3 rounded-full transition transform hover:scale-105"
          :class="{ 'bg-indigo-600 hover:bg-indigo-500': !hasFormError, 'bg-red-600 hover:bg-red-500': hasFormError }"
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
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faEye, faEyeSlash } from '@fortawesome/free-solid-svg-icons'
import axios from 'axios'

library.add(faEye, faEyeSlash)

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const localErrors = ref({})
const hasFormError = ref(false)
const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const validateForm = async () => {
  localErrors.value = {}
  hasFormError.value = false

  // Name validation
  if (!form.value.name) {
    localErrors.value.name = "Ism kiritilishi shart"
    hasFormError.value = true
  }

  // Email validation
  if (!form.value.email) {
    localErrors.value.email = "Email kiritilishi shart"
    hasFormError.value = true
  } else if (!/\S+@\S+\.\S+/.test(form.value.email)) {
    localErrors.value.email = "Email noto'g'ri formatda"
    hasFormError.value = true
  } else {
    // Check if email is already taken
    try {
      const response = await axios.get(route('users.email', form.value.email))
      if (response.data.data) {
        localErrors.value.email = "Bu email allaqachon ro'yxatdan o'tgan"
        hasFormError.value = true
      }
    } catch (error) {
      console.error('Error checking email:', error)
    }
  }

  // Password validation
  if (!form.value.password) {
    localErrors.value.password = "Parol kiritilishi shart"
    hasFormError.value = true
  } else if (form.value.password.length < 8) {
    localErrors.value.password = "Parol kamida 8 belgi bo'lishi kerak"
    hasFormError.value = true
  }

  // Password confirmation validation
  if (!form.value.password_confirmation) {
    localErrors.value.password_confirmation = "Parolni tasdiqlash shart"
    hasFormError.value = true
  } else if (form.value.password !== form.value.password_confirmation) {
    localErrors.value.password_confirmation = "Parollar mos emas"
    hasFormError.value = true
  }

  return !hasFormError.value
}

const checkEmail = async () => {
  localErrors.value.email = null
  hasFormError.value = Object.keys(localErrors.value).length > 0

  if (form.value.email && /\S+@\S+\.\S+/.test(form.value.email)) {
    try {
      const response = await axios.get(route('users.email', form.value.email))
      if (response.data.data) {
        localErrors.value.email = "Bu email allaqachon ro'yxatdan o'tgan"
        hasFormError.value = true
      }
    } catch (error) {
      console.error('Error checking email:', error)
    }
  }
}

const submit = async () => {
  localErrors.value = {}
  hasFormError.value = false

  const isValid = await validateForm()

  if (isValid) {
    Inertia.post(route('users.store'), form.value, {
      onSuccess: () => {
        alert("Foydalanuvchi muvaffaqiyatli yaratildi!")
        form.value = { name: '', email: '', password: '', password_confirmation: '' }
        localErrors.value = {}
        hasFormError.value = false
        emit('close')
      },
      onError: (err) => {
        localErrors.value = err
        hasFormError.value = true
        if (err.email) {
          localErrors.value.email = "Bu email allaqachon ro'yxatdan o'tgan"
        }
      },
    })
  } else {
    hasFormError.value = true
  }
}

const emit = defineEmits(['close'])
</script>