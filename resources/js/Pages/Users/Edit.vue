<template>
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Tahrirlash</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Ism</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :class="{ 'border-red-500': errors.name }"
        />
        <p v-if="errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.name }}</p>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :class="{ 'border-red-500': errors.email }"
          @input="checkEmail"
        />
        <p v-if="errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.email }}</p>
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Parol (o'zgartirish uchun kiriting)</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :class="{ 'border-red-500': errors.password }"
        />
        <p v-if="errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.password }}</p>
      </div>

      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Parolni tasdiqlash</label>
        <input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :class="{ 'border-red-500': errors.password_confirmation }"
        />
        <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.password_confirmation }}</p>
      </div>

      <div class="flex justify-end gap-4">
        <button
          type="button"
          class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
          @click="props.close"
        >
          Bekor qilish
        </button>
        <button
          type="submit"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-500 dark:hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          :class="{ 'bg-red-600 hover:bg-red-500 dark:hover:bg-red-700': hasFormError }"
        >
          Saqlash
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'

const props = defineProps({
  user: Object,
  close: Function,
})

const form = ref({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
})

const errors = ref({})
const hasFormError = ref(false)

const validateForm = async () => {
  errors.value = {}
  hasFormError.value = false

  // Name validation
  if (!form.value.name) {
    errors.value.name = "Ism kiritilishi shart"
    hasFormError.value = true
  }

  // Email validation
  if (!form.value.email) {
    errors.value.email = "Email kiritilishi shart"
    hasFormError.value = true
  } else if (!/\S+@\S+\.\S+/.test(form.value.email)) {
    errors.value.email = "Email noto'g'ri formatda"
    hasFormError.value = true
  } else if (form.value.email !== props.user.email) {
    // Check if email is taken, but only if it’s changed
    try {
      const response = await axios.get(route('users.email', form.value.email))
      if (response.data.data) {
        errors.value.email = "Bu email allaqachon ro'yxatdan o'tgan"
        hasFormError.value = true
      }
    } catch (error) {
      console.error('Error checking email:', error)
    }
  }

  // Password validation (optional, only if provided)
  if (form.value.password) {
    if (form.value.password.length < 8) {
      errors.value.password = "Parol kamida 8 belgi bo'lishi kerak"
      hasFormError.value = true
    }
    if (form.value.password !== form.value.password_confirmation) {
      errors.value.password_confirmation = "Parollar mos emas"
      hasFormError.value = true
    }
  }

  return !hasFormError.value
}

const checkEmail = async () => {
  errors.value.email = null
  hasFormError.value = Object.keys(errors.value).length > 0

  if (form.value.email && /\S+@\S+\.\S+/.test(form.value.email) && form.value.email !== props.user.email) {
    try {
      const response = await axios.get(route('users.email', form.value.email))
      if (response.data.data) {
        errors.value.email = "Bu email allaqachon ro'yxatdan o'tgan"
        hasFormError.value = true
      }
    } catch (error) {
      console.error('Error checking email:', error)
    }
  }
}

const submit = async () => {
  errors.value = {}
  hasFormError.value = false

  const isValid = await validateForm()

  if (isValid) {
    Inertia.put(route('users.update', props.user.id), form.value, {
      onSuccess: () => {
        props.close()
        alert("Foydalanuvchi muvaffaqiyatli yangilandi!")
      },
      onError: (err) => {
        errors.value = err
        hasFormError.value = true
        if (err.email) {
          errors.value.email = "Bu email allaqachon ro'yxatdan o'tgan"
        }
      },
    })
  }
}
</script>