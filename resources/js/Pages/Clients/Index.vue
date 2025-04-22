<template>
  <div>
    <Head :title="title" />

    <Banner />

    <div class="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800">
      <nav class="bg-gray-800 border-b border-gray-700">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <!-- Logo -->
              <div class="shrink-0 flex items-center">
                <Link :href="route('dashboard')">
                  <ApplicationMark class="block h-9 w-auto" />
                </Link>
              </div>

              <!-- Navigation Links -->
              <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                  Dashboard
                </NavLink>
                <NavLink :href="route('clients.index')" :active="route().current('clients.index')" class="ml-4">
                  Clients
                </NavLink>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <div class="w-full h-full text-gray-100 flex flex-col">
        <!-- Header Section -->
        <div class="flex justify-between items-center px-8 py-6">
          <h1 class="text-4xl font-extrabold tracking-tight text-white">klientlar</h1>
          
          <!-- Flex container for input and button -->
          <div class="flex items-center space-x-4">
            <!-- Search Input -->
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Klients qidirish"
              class="bg-gray-700 text-gray-100 rounded-lg px-12 py-3 border-2 border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
              @input="debouncedSearch"
            />
            
            <!-- Create Button -->
            <button 
              @click="openCreateModal" 
              class="flex items-center bg-indigo-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-indigo-500 transition-all duration-300 ease-in-out transform hover:scale-105"
            >
              Klient qoshish
            </button>
          </div>
        </div>



        <!-- Table Card -->
        <div class="flex-1 px-8 pb-8 overflow-auto">
          <div class="bg-gray-800 rounded-2xl shadow-2xl w-full h-full flex flex-col">
            <div class="overflow-x-auto">
              <table class="w-full table-auto">
                <thead>
                  <tr class="bg-gradient-to-r from-indigo-900 to-indigo-800 text-gray-300 sticky top-0">
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">ID</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">Ism</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">Familiya</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">Kompaniyasi</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">Balansi</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">Harakat</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="client in clients.data" 
                    :key="client.id" 
                    class="hover:bg-gray-700 transition-all duration-200 ease-in-out"
                  >
                    <td class="p-6 text-gray-400">{{ client.id }}</td>
                    <td class="p-6 text-gray-100 font-medium">{{ client.first_name }}</td>
                    <td class="p-6 text-gray-100 font-medium">{{ client.lastname }}</td>
                    <td class="p-6 text-gray-400">{{ client.company_name }}</td>
                    <td class="p-6 text-gray-100 font-medium">{{ client.balance }}UZS</td>
                    <td class="p-6 flex space-x-4">
                      <button
                        @click="openEditModal(client)"
                        class="text-indigo-400 hover:text-indigo-300 transition duration-150 ease-in-out"
                        title="Edit Client"
                      >
                        <font-awesome-icon :icon="['fas', 'user-pen']" />

                      </button>
                      <button
                        @click="openModal(client)"
                        class="text-green-400 hover:text-green-300 transition duration-150 ease-in-out"
                        title="View Client"
                      >
                      <font-awesome-icon :icon="['fas', 'file-lines']" />
                      </button>
                      <button
                        @click="openBalanceModal(client)"
                        class="text-yellow-400 hover:text-yellow-300 transition duration-150 ease-in-out"
                        title="Add Balance"
                      >
                      <button class="bg-indigo-600 text-white font-semibold py-3 px-6 rounded-full shadow-lg hover:bg-indigo-700 transform transition-all duration-300 ease-in-out hover:scale-105">
                        Pull berish
                      </button>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-700">
              <div class="text-sm text-gray-400">
                Showing {{ clients.from }} to {{ clients.to }} of {{ clients.total }} clients
              </div>
              <div class="flex space-x-2">
                <Link 
                  v-for="link in clients.links" 
                  :key="link.label"
                  :href="link.url || '#'" 
                  class="px-4 py-2 rounded-lg"
                  :class="{
                    'bg-indigo-600 text-white': link.active,
                    'bg-gray-700 text-gray-300 hover:bg-gray-600': !link.active && link.url,
                    'bg-gray-800 text-gray-500 cursor-not-allowed': !link.url
                  }"
                  preserve-scroll
                >
                  <span v-html="link.label"></span>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal for Viewing Client -->
        <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
          <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-auto">
            <div class="p-8">
              <!-- Modal Header -->
              <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-white">Client Details</h2>
                <button 
                  @click="closeModal"
                  class="text-gray-400 hover:text-white transition duration-150"
                >
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Client Details Content -->
              <div v-if="selectedClient" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <strong class="text-gray-400 text-sm uppercase tracking-wider">ID</strong>
                  <p class="text-gray-100 text-lg font-medium mt-2">{{ selectedClient.id }}</p>
                </div>
                <div>
                  <strong class="text-gray-400 text-sm uppercase tracking-wider">Ism</strong>
                  <p class="text-gray-100 text-lg font-medium mt-2">{{ selectedClient.first_name }}</p>
                </div>
                <div>
                  <strong class="text-gray-400 text-sm uppercase tracking-wider">Familiya</strong>
                  <p class="text-gray-100 text-lg font-medium mt-2">{{ selectedClient.lastname }}</p>
                </div>
                <div>
                  <strong class="text-gray-400 text-sm uppercase tracking-wider">Kompany</strong>
                  <p class="text-gray-100 text-lg font-medium mt-2">{{ selectedClient.company_name }}</p>
                </div>
                <div>
                  <strong class="text-gray-400 text-sm uppercase tracking-wider">Balanci</strong>
                  <p class="text-gray-100 text-lg font-medium mt-2">{{ selectedClient.balance }}UZS</p>
                </div>
                <div>
                  <strong class="text-gray-400 text-sm uppercase tracking-wider">Address</strong>
                  <p class="text-gray-100 text-lg font-medium mt-2">{{ selectedClient.address }}</p>
                </div>
                <div class="md:col-span-2">
                  <strong class="text-gray-400 text-sm uppercase tracking-wider">Nomeri</strong>
                  <ul class="mt-2 space-y-2">
                    <li 
                      v-for="phone in selectedClient.phones" 
                      :key="phone.id" 
                      class="text-gray-100 text-lg hover:bg-gray-700 transition-all duration-200 ease-in-out px-4 py-2 rounded-lg"
                    >
                      {{ phone.phone }}
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Modal Footer -->
              <div class="mt-8 flex justify-end space-x-4">
                <button
                  @click="closeModal"
                  class="px-6 py-3 bg-gray-700 text-white rounded-full hover:bg-gray-600 transition duration-300"
                >
                  Yopish
                </button>
          
              </div>
            </div>
          </div>
        </div>

        <!-- Modal for Creating Client -->
        <div v-if="isCreateModalOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
          <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-auto">
            <CreateClient @close="closeCreateModal" />
          </div>
        </div>

        <!-- Modal for Editing Client -->
        <div v-if="isEditModalOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
          <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-auto">
            <EditClient :client="selectedClient" @close="closeEditModal" />
          </div>
        </div>

        <!-- Modal for Adding Balance -->
        <div v-if="isBalanceModalOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
          <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-auto">
            <AddBalance :client="selectedClient" @close="closeBalanceModal" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

import { ref,watch } from 'vue'
import ApplicationMark from '@/Components/ApplicationMark.vue'
import Banner from '@/Components/Banner.vue'
import NavLink from '@/Components/NavLink.vue'
import CreateClient from '@/Pages/Clients/Create.vue'
import EditClient from '@/Pages/Clients/Edit.vue'
import { debounce } from 'lodash'
import AddBalance from '@/Pages/Clients/AddBalance.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUserPen } from '@fortawesome/free-solid-svg-icons'
import { faFileLines } from '@fortawesome/free-solid-svg-icons'


library.add(faUserPen)
library.add(faFileLines)



defineProps({
  title: String,
  clients: Object,
  auth: Object,
  filters: Object
})

const isModalOpen = ref(false)
const isCreateModalOpen = ref(false)
const isEditModalOpen = ref(false)
const isBalanceModalOpen = ref(false)
const selectedClient = ref(null)
const searchQuery = ref('')

const openModal = (client) => {
  selectedClient.value = client
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  selectedClient.value = null
}

const openCreateModal = () => {
  isCreateModalOpen.value = true
}

const closeCreateModal = () => {
  isCreateModalOpen.value = false
}

const openEditModal = (client) => {
  selectedClient.value = client
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
  selectedClient.value = null
}

const openBalanceModal = (client) => {
  selectedClient.value = client
  isBalanceModalOpen.value = true
}

const closeBalanceModal = () => {
  isBalanceModalOpen.value = false
  selectedClient.value = null
}

const debouncedSearch = debounce(() => {
  router.get(route('clients.index'), 
    { search: searchQuery.value },
    { 
      preserveState: true, 
      preserveScroll: true,
      replace: true
    }
  )
}, 500)

watch(searchQuery, () => {
  debouncedSearch()
})
</script>
```