<template>
  <div class="w-full h-screen bg-gradient-to-br from-gray-900 to-gray-800 text-gray-100 flex flex-col p-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-10">
      <h1 class="text-4xl font-extrabold tracking-tight text-white">Client Details</h1>
      <div class="flex space-x-4">
        <Link 
          href="/clients" 
          class="flex items-center bg-gray-700 text-white px-6 py-3 rounded-full shadow-lg hover:bg-gray-600 transition-all duration-300 ease-in-out transform hover:scale-105"
        >
          <!-- Flaticon Back Icon -->
          <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
          </svg>
          Back
        </Link>
        <Link 
          :href="`/clients/${client.id}/edit`" 
          class="flex items-center bg-indigo-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-indigo-500 transition-all duration-300 ease-in-out transform hover:scale-105"
        >
          <!-- Flaticon Edit Icon -->
          <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
          </svg>
          Edit Client
        </Link>
      </div>
    </div>

    <!-- Client Details Card -->
    <div class="flex-1 bg-gray-800 rounded-2xl shadow-2xl p-8 w-full h-full overflow-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <strong class="text-gray-400 text-sm uppercase tracking-wider">Name</strong>
          <p class="text-gray-100 text-lg font-medium mt-2">{{ client.first_name }} {{ client.lastname }}</p>
        </div>
        <div>
          <strong class="text-gray-400 text-sm uppercase tracking-wider">Company</strong>
          <p class="text-gray-100 text-lg font-medium mt-2">{{ client.company_name }}</p>
        </div>
        <div>
          <strong class="text-gray-400 text-sm uppercase tracking-wider">Balance</strong>
          <p class="text-gray-100 text-lg font-medium mt-2">${{ client.balance }}</p>
        </div>
        <div>
          <strong class="text-gray-400 text-sm uppercase tracking-wider">Address</strong>
          <p class="text-gray-100 text-lg font-medium mt-2">{{ client.address }}</p>
        </div>
        <div class="md:col-span-2">
          <strong class="text-gray-400 text-sm uppercase tracking-wider">Phones</strong>
          <ul class="mt-2 space-y-2">
            <li 
              v-for="phone in client.phones" 
              :key="phone.id" 
              class="text-gray-100 text-lg hover:bg-gray-700 transition-all duration-200 ease-in-out px-4 py-2 rounded-lg"
            >
              {{ phone.phone }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  client: Object
})
</script>