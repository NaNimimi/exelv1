<template>
  <div>
    <Head title="Client Details" />

    <Banner />

    <div class="min-h-screen bg-gray-100 dark:bg-gradient-to-br dark:from-gray-900 dark:to-gray-800">
      <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
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
                <NavLink :href="route('users.index')" :active="route().current('users.index')" class="ml-4">
                  Users
                </NavLink>
                <NavLink :href="route('roles.index')" :active="route().current('roles.index')" class="ml-4">
                  Roles
                </NavLink>
              </div>
            </div>
            <div class="flex items-center">
              <ThemeSwitcher />
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <div class="w-full h-full text-gray-900 dark:text-gray-100 flex flex-col px-4 sm:px-6 lg:px-8 py-10">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-12 max-w-7xl mx-auto w-full">
          <h1 class="text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white">Client Details</h1>
          <div class="flex space-x-6">
            <Link 
              :href="route('clients.index')" 
              class="flex items-center bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white px-8 py-4 rounded-full shadow-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-300 ease-in-out transform hover:scale-105 text-lg"
            >
              <svg class="w-6 h-6 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
              </svg>
              Back
            </Link>
          </div>
        </div>

        <!-- Client Details Card -->
        <div class="flex-1 bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 sm:p-12 max-w-7xl mx-auto w-full overflow-auto">
          <div class="space-y-8">
            <div class="flex items-center">
              <strong class="w-1/3 text-gray-600 dark:text-gray-400 text-base uppercase tracking-wider">Ism</strong>
              <p class="w-2/3 text-gray-900 dark:text-gray-100 text-2xl font-medium">{{ client.first_name }}</p>
            </div>
            <div class="flex items-center">
              <strong class="w-1/3 text-gray-600 dark:text-gray-400 text-base uppercase tracking-wider">Familiya</strong>
              <p class="w-2/3 text-gray-900 dark:text-gray-100 text-2xl font-medium">{{ client.lastname }}</p>
            </div>
            <div class="flex items-center">
              <strong class="w-1/3 text-gray-600 dark:text-gray-400 text-base uppercase tracking-wider">Kompaniya</strong>
              <p class="w-2/3 text-gray-900 dark:text-gray-100 text-2xl font-medium">{{ client.company_name }}</p>
            </div>
            <div class="flex items-center">
              <strong class="w-1/3 text-gray-600 dark:text-gray-400 text-base uppercase tracking-wider">Balans</strong>
              <p class="w-2/3 text-gray-900 dark:text-gray-100 text-2xl font-medium">{{ client.balance }} UZS</p>
            </div>
            <div class="flex items-center">
              <strong class="w-1/3 text-gray-600 dark:text-gray-400 text-base uppercase tracking-wider">Manzil</strong>
              <p class="w-2/3 text-gray-900 dark:text-gray-100 text-2xl font-medium">{{ client.address }}</p>
            </div>
            <div class="flex flex-col">
              <strong class="w-1/3 text-gray-600 dark:text-gray-400 text-base uppercase tracking-wider mb-4">Telefonlar</strong>
              <ul class="space-y-3">
                <li 
                  v-for="phone in client.phones" 
                  :key="phone.id" 
                  class="text-gray-900 dark:text-gray-100 text-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 ease-in-out px-6 py-3 rounded-lg"
                >
                  {{ phone.phone }}
                </li>
              </ul>
            </div>
          </div>

          <!-- Balance Movements Section -->
          <div class="mt-16">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Balance Movements</h2>
              <button
                @click="exportFilteredClient"
                class="flex items-center bg-green-600 text-white px-8 py-4 rounded-full shadow-lg hover:bg-green-500 dark:hover:bg-green-700 transition-all duration-300 ease-in-out transform hover:scale-105 text-lg"
              >
                <font-awesome-icon :icon="['fas', 'file-excel']" class="w-6 h-6 mr-3" />
                Export to Excel
              </button>
            </div>
            <!-- Date Range Filter -->
            <div class="flex flex-col sm:flex-row sm:space-x-4 mb-6">
              <div class="flex-1">
                <label for="start-date" class="block text-gray-600 dark:text-gray-400 text-sm uppercase tracking-wider mb-2">Start Date</label>
                <input
                  id="start-date"
                  v-model="startDate"
                  type="date"
                  :max="endDate || today"
                  class="w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-3 border-2 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                />
              </div>
              <div class="flex-1 mt-4 sm:mt-0">
                <label for="end-date" class="block text-gray-600 dark:text-gray-400 text-sm uppercase tracking-wider mb-2">End Date</label>
                <input
                  id="end-date"
                  v-model="endDate"
                  type="date"
                  :min="startDate"
                  :max="today"
                  class="w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-3 border-2 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                />
              </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-auto">
              <table class="w-full table-auto">
                <thead>
                  <tr class="bg-gray-200 dark:bg-gradient-to-r dark:from-indigo-900 dark:to-indigo-800 text-gray-700 dark:text-gray-300 sticky top-0">
                    <th class="p-6 text-left font-semibold text-base uppercase tracking-wider text-gray-600 dark:text-gray-400">Summa</th>
                    <th class="p-6 text-left font-semibold text-base uppercase tracking-wider text-gray-600 dark:text-gray-400">Yangi Balans</th>
                    <th class="p-6 text-left font-semibold text-base uppercase tracking-wider text-gray-600 dark:text-gray-400">Izoh</th>
                    <th class="p-6 text-left font-semibold text-base uppercase tracking-wider text-gray-600 dark:text-gray-400">Bugalter</th>
                    <th class="p-6 text-left font-semibold text-base uppercase tracking-wider text-gray-600 dark:text-gray-400">Sana</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="movement in balanceMovements.data" 
                    :key="movement.id" 
                    class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 ease-in-out"
                  >
                    <td class="p-6 font-medium text-lg" :class="{
                      'text-green-600 dark:text-green-400': movement.amount > 0,
                      'text-red-600 dark:text-red-400': movement.amount < 0,
                      'text-gray-900 dark:text-gray-100': movement.amount === 0
                    }">
                      {{ movement.amount }} UZS
                    </td>
                    <td class="p-6 text-gray-900 dark:text-gray-100 font-medium text-lg">{{ movement.new_balance }} UZS</td>
                    <td class="p-6 text-gray-600 dark:text-gray-400 text-lg">{{ movement.comment }}</td>
                    <td class="p-6 text-gray-900 dark:text-gray-100 font-medium text-lg">{{ movement.user?.name || 'Unknown' }}</td>
                    <td class="p-6 text-gray-600 dark:text-gray-400 text-lg">{{ new Date(movement.created_at).toLocaleDateString() }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700 mt-6">
              <div class="text-base text-gray-600 dark:text-gray-400">
                Showing {{ balanceMovements.from }} to {{ balanceMovements.to }} of {{ balanceMovements.total }} movements
              </div>
              <div class="flex space-x-3">
                <a 
                  v-for="link in balanceMovements.links" 
                  :key="link.label"
                  :href="link.url || '#'"
                  @click.prevent="navigateToPage(link.url)"
                  class="px-4 py-3 rounded-lg text-lg cursor-pointer"
                  :class="{
                    'bg-indigo-600 text-white': link.active,
                    'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': !link.active && link.url,
                    'bg-gray-300 dark:bg-gray-800 text-gray-500 dark:text-gray-500 cursor-not-allowed': !link.url
                  }"
                  :aria-current="link.active ? 'page' : null"
                  :aria-disabled="!link.url ? 'true' : null"
                  :aria-label="`Go to page ${link.label}`"
                >
                  <span v-html="link.label"></span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import { debounce } from 'lodash'
import axios from 'axios'
import ApplicationMark from '@/Components/ApplicationMark.vue'
import Banner from '@/Components/Banner.vue'
import NavLink from '@/Components/NavLink.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faFileExcel } from '@fortawesome/free-solid-svg-icons'

library.add(faFileExcel)

const props = defineProps({
  client: Object,
  balanceMovements: Object,
  filters: {
    type: Object,
    default: () => ({ start_date: null, end_date: null })
  }
})

const startDate = ref(props.filters.start_date || '')
const endDate = ref(props.filters.end_date || new Date().toISOString().split('T')[0]) // Default to today
const today = computed(() => new Date().toISOString().split('T')[0])

watch(
  () => props.filters,
  (newFilters) => {
    console.log('Filters changed:', newFilters) // Debug log
    startDate.value = newFilters.start_date || ''
    endDate.value = newFilters.end_date || new Date().toISOString().split('T')[0]
  },
  { immediate: true, deep: true }
)

const updateFilters = debounce(() => {
  console.log('Updating filters:', { start_date: startDate.value, end_date: endDate.value }) // Debug log
  router.get(
    route('clients.show', props.client.id),
    { start_date: startDate.value, end_date: endDate.value },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onError: (errors) => {
        console.error('Filter update failed:', errors)
      }
    }
  )
}, 500)

function navigateToPage(url) {
  if (url) {
    router.get(url, {}, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onError: (errors) => {
        console.error('Pagination navigation failed:', errors)
      }
    })
  }
}

const exportFilteredClient = async () => {
  try {
    const response = await axios.get(route('clients.export.filtered', {
      id: props.client.id,
      start_date: startDate.value,
      end_date: endDate.value
    }), { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `client_${props.client.id}_details_${startDate.value || 'start'}_to_${endDate.value || 'end'}.xlsx`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error exporting client:', error)
    alert('Failed to export client data. Please try again.')
  }
}
</script>