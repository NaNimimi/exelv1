<template>
  <div>
    <Head :title="title" />

    <Banner />

    <div class="min-h-screen bg-gray-100 dark:bg-gradient-to-br dark:from-gray-900 dark:to-gray-800">
      <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
              <div class="shrink-0 flex items-center">
                <Link :href="route('dashboard')">
                  <ApplicationMark class="block h-9 w-auto" />
                </Link>
              </div>
              <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                  Dashboard
                </NavLink>
                <NavLink :href="route('clients.index')" :active="route().current('clients.index')">
                  Clients
                </NavLink>
                <NavLink :href="route('users.index')" :active="route().current('users.index')">
                  Users
                </NavLink>
                <NavLink :href="route('roles.index')" :active="route().current('roles.index')">
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

      <div class="w-full h-full text-gray-900 dark:text-gray-100 flex flex-col">
        <div class="flex justify-between items-center px-8 py-6">
          <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">Klientlar</h1>
          <div class="relative flex items-center space-x-4">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Klients qidirish"
              class="bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-12 py-3 border-2 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 pl-10"
              @input="debouncedSearch"
            />
            <font-awesome-icon :icon="['fas', 'search']" class="absolute left-1 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400" />
            <button 
              @click="exportAllClients" 
              class="flex items-center bg-green-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-green-500 dark:hover:bg-green-700 transition-all duration-300 ease-in-out transform hover:scale-105"
            >
              <font-awesome-icon :icon="['fas', 'file-excel']" class="mr-2" />
              Hamma klientlarni Excelga
            </button>
            <button 
              @click="openCreateModal" 
              class="flex items-center bg-indigo-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-indigo-500 dark:hover:bg-indigo-700 transition-all duration-300 ease-in-out transform hover:scale-105"
            >
              Klient qoshish
            </button>
          </div>
        </div>

        <div class="flex-1 px-8 pb-8 overflow-auto">
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full h-full flex flex-col">
            <div class="overflow-x-auto">
              <table class="w-full table-auto">
                <thead>
                  <tr class="bg-gray-200 dark:bg-gradient-to-r dark:from-indigo-900 dark:to-indigo-800 text-gray-700 dark:text-gray-300 sticky top-0">
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-600 dark:text-gray-400">ID</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-600 dark:text-gray-400">Ism</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-600 dark:text-gray-400">Familiya</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-600 dark:text-gray-400">Kompaniyasi</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-600 dark:text-gray-400">Balansi</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-600 dark:text-gray-400">Harakat</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="client in clients.data" 
                    :key="client.id" 
                    class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 ease-in-out cursor-pointer"
                    @click="viewClient(client.id)"
                  >
                    <td class="p-6 text-gray-600 dark:text-gray-400">{{ client.id }}</td>
                    <td class="p-6 text-gray-900 dark:text-gray-100 font-medium">{{ client.first_name }}</td>
                    <td class="p-6 text-gray-900 dark:text-gray-100 font-medium">{{ client.lastname }}</td>
                    <td class="p-6 text-gray-600 dark:text-gray-400">{{ client.company_name }}</td>
                    <td class="p-6 text-gray-900 dark:text-gray-100 font-medium">{{ client.balance }} UZS</td>
                    <td class="p-6 flex space-x-4 items-center" @click.stop>
                      <button
                        @click="exportClient(client.id)"
                        class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500 dark:hover:text-yellow-300 transition duration-150 ease-in-out"
                        title="Excelga yuklash"
                      >
                        <font-awesome-icon :icon="['fas', 'file-excel']" class="w-5 h-5" />
                      </button>
                      <button
                        @click="openEditModal(client)"
                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition duration-150 ease-in-out"
                        title="Edit Client"
                      >
                        <font-awesome-icon :icon="['fas', 'user-pen']" class="w-5 h-5" />
                      </button>
                      <Link
                        :href="route('clients.show', client.id)"
                        class="text-green-600 dark:text-green-400 hover:text-green-500 dark:hover:text-green-300 transition duration-150 ease-in-out"
                        title="View Client"
                      >
                        <font-awesome-icon :icon="['fas', 'file-lines']" class="w-5 h-5" />
                      </Link>
                      <button
                        @click="openOperationModal(client)"
                        class="bg-indigo-600 text-white font-semibold py-3 px-6 rounded-full shadow-lg hover:bg-indigo-500 dark:hover:bg-indigo-700 transform transition-all duration-300 ease-in-out hover:scale-105"
                        title="Popolnit"
                      >
                        Popolnit
                      </button>
                      <button
                        @click="confirmDeleteClient(client)"
                        class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition duration-150 ease-in-out"
                        title="Klientni o'chirish"
                      >
                        <font-awesome-icon :icon="['fas', 'trash']" class="w-5 h-5" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700">
              <div class="text-sm text-gray-600 dark:text-gray-400">
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
                    'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': !link.active && link.url,
                    'bg-gray-300 dark:bg-gray-800 text-gray-500 dark:text-gray-500 cursor-not-allowed': !link.url
                  }"
                  preserve-scroll
                >
                  <span v-html="link.label"></span>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div v-if="isCreateModalOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]">
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-auto">
            <CreateClient @close="closeCreateModal" />
          </div>
        </div>

        <div v-if="isEditModalOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]">
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-auto">
            <EditClient :client="selectedClient" @close="closeEditModal" />
          </div>
        </div>

        <div v-if="isOperationModalOpen && selectedClient" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]">
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-auto">
            <SelectOperation :client="selectedClient" @close="closeOperationModal" @openPaymentTypeModal="openPaymentTypeModal" />
          </div>
        </div>

        <div v-if="isPaymentTypeModalOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]">
          <div v-if="paymentTypes && Array.isArray(paymentTypes)" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-auto">
            <SelectPaymentType 
              :client="selectedClient" 
              :paymentTypes="paymentTypes" 
              :formData="pendingFormData"
              @close="closePaymentTypeModal" 
              @openBalanceModal="openBalanceModal"
            />
          </div>
          <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 text-red-600 dark:text-red-400">
            Error: Payment types are not available.
            <button @click="closePaymentTypeModal" class="bg-gray-500 dark:bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 mt-4">
              Close
            </button>
          </div>
        </div>

        <div v-if="isBalanceModalOpen && selectedClient" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]">
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-auto">
            <AddBalance :client="selectedClient" :formData="pendingFormData" @close="closeBalanceModal" />
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="isDeleteConfirmationOpen" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1010]">
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
            <div class="p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Klientni o'chirish</h2>
              <p class="text-gray-600 dark:text-gray-300 mb-4">Haqiqatan ham <span class="font-bold">{{ clientToDelete?.first_name }} {{ clientToDelete?.lastname }}</span> ni o'chirmoqchimisiz?</p>
              <div class="flex justify-end gap-4">
                <button
                  class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                  @click="closeDeleteConfirmationModal"
                >
                  Bekor qilish
                </button>
                <button
                  class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                  @click="deleteConfirmedClient"
                >
                  O'chirish
                </button>
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
import axios from 'axios'
import { ref, watch, nextTick } from 'vue'
import ApplicationMark from '@/Components/ApplicationMark.vue'
import Banner from '@/Components/Banner.vue'
import NavLink from '@/Components/NavLink.vue'
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue'
import CreateClient from '@/Pages/Clients/Create.vue'
import EditClient from '@/Pages/Clients/Edit.vue'
import SelectOperation from '@/Pages/Clients/SelectOperation.vue'
import SelectPaymentType from '@/Pages/Clients/SelectPaymentType.vue'
import AddBalance from '@/Pages/Clients/AddBalance.vue'
import { debounce } from 'lodash'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUserPen, faFileLines, faFileExcel, faTrash, faSearch } from '@fortawesome/free-solid-svg-icons'

library.add(faUserPen, faFileLines, faFileExcel, faTrash, faSearch)

const props = defineProps({
  title: String,
  clients: Object,
  auth: Object,
  filters: Object,
  paymentTypes: {
    type: Array,
    default: () => []
  }
})

const isCreateModalOpen = ref(false)
const isEditModalOpen = ref(false)
const isOperationModalOpen = ref(false)
const isPaymentTypeModalOpen = ref(false)
const isBalanceModalOpen = ref(false)
const isDeleteConfirmationOpen = ref(false)
const selectedClient = ref(null)
const clientToDelete = ref(null)
const searchQuery = ref(props.filters?.search || '')
const pendingFormData = ref(null)

const openCreateModal = () => {
  isCreateModalOpen.value = true
}

const closeCreateModal = () => {
  isCreateModalOpen.value = false
}

const openEditModal = (client) => {
  console.log('Opening edit modal for client:', client)
  selectedClient.value = client
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  console.log('Closing edit modal')
  isEditModalOpen.value = false
  selectedClient.value = null
}

const openOperationModal = async (client) => {
  try {
    console.log('Opening operation modal for client:', client)
    selectedClient.value = client
    await nextTick()
    isOperationModalOpen.value = true
    console.log('Modal state:', { isOperationModalOpen: isOperationModalOpen.value, selectedClient: selectedClient.value })
  } catch (error) {
    console.error('Error opening operation modal:', error)
  }
}

const closeOperationModal = () => {
  console.log('Closing operation modal')
  isOperationModalOpen.value = false
  selectedClient.value = null
}

const openPaymentTypeModal = async (formData) => {
  try {
    console.log('Opening payment type modal with formData:', formData)
    console.log('Payment types available:', props.paymentTypes)
    if (!props.paymentTypes || !Array.isArray(props.paymentTypes)) {
      console.warn('Warning: paymentTypes is not defined or not an array')
    }
    pendingFormData.value = formData
    await nextTick()
    isOperationModalOpen.value = false
    isPaymentTypeModalOpen.value = true
  } catch (error) {
    console.error('Error opening payment type modal:', error)
  }
}

const closePaymentTypeModal = () => {
  console.log('Closing payment type modal')
  isPaymentTypeModalOpen.value = false
  pendingFormData.value = null
  selectedClient.value = null
}

const openBalanceModal = async (formData) => {
  try {
    console.log('Opening balance modal with formData:', formData)
    pendingFormData.value = formData
    await nextTick()
    isPaymentTypeModalOpen.value = false
    isBalanceModalOpen.value = true
    console.log('Balance modal state:', { isBalanceModalOpen: isBalanceModalOpen.value, pendingFormData: pendingFormData.value })
  } catch (error) {
    console.error('Error opening balance modal:', error)
  }
}

const closeBalanceModal = () => {
  console.log('Closing balance modal')
  isBalanceModalOpen.value = false
  pendingFormData.value = null
  selectedClient.value = null
}

const exportAllClients = async () => {
  try {
    const response = await axios.get(route('clients.export'), { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'clients.xlsx')
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error exporting clients:', error)
    alert('Failed to export clients. Please try again.')
  }
}

const exportClient = (clientId) => {
  window.location.href = route('clients.export.single', clientId)
}

const viewClient = (clientId) => {
  router.visit(route('clients.show', clientId))
}

const confirmDeleteClient = (client) => {
  clientToDelete.value = client
  isDeleteConfirmationOpen.value = true
}

const closeDeleteConfirmationModal = () => {
  isDeleteConfirmationOpen.value = false
  clientToDelete.value = null
}

const deleteConfirmedClient = () => {
  if (clientToDelete.value) {
    router.delete(route('clients.destroy', clientToDelete.value.id), {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        closeDeleteConfirmationModal()
        alert("Klient muvaffaqiyatli o'chirildi!")
      },
      onError: (errors) => {
        let errorMessage = 'Xatolik yuz berdi.'
        if (errors.authorization) {
          errorMessage = 'Sizda bu klientni o\'chirish uchun ruxsat yo\'q.'
        } else if (errors.not_found) {
          errorMessage = 'Klient topilmadi.'
        }
        alert(errorMessage)
      },
    })
  }
}

const debouncedSearch = debounce(() => {
  router.get(
    route('clients.index'),
    { search: searchQuery.value },
    { preserveState: true, preserveScroll: true, replace: true }
  )
}, 500)

watch(searchQuery, (newQuery) => {
  debouncedSearch()
})

watch([isOperationModalOpen, selectedClient], ([newModalState, newClient]) => {
  console.log('Operation modal state changed:', { isOperationModalOpen: newModalState, selectedClient: newClient })
})

watch([isBalanceModalOpen, pendingFormData], ([newModalState, newFormData]) => {
  console.log('Balance modal state changed:', { isBalanceModalOpen: newModalState, pendingFormData: newFormData })
})
</script>