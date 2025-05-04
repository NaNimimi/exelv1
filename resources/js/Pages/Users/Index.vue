<template>
  <div>
    <Head title="Foydalanuvchilar" />

    <Banner />

    <div class="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800">
      <nav class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <div class="shrink-0 flex items-center">
                <Link :href="route('dashboard')">
                  <ApplicationMark class="block h-9 w-auto" />
                </Link>
              </div>
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
          </div>
        </div>
      </nav>

      <div class="w-full h-full text-gray-100 flex flex-col">
        <div class="flex justify-between items-center px-8 py-6">
          <h1 class="text-4xl font-bold text-white">Foydalanuvchilar</h1>
          <div class="relative flex items-center space-x-4">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Foydalanuvchilarni qidirish"
              class="bg-gray-700 text-gray-100 rounded-lg px-12 py-3 border-2 border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 pl-10"
              @input="debouncedSearch"
            />
            <font-awesome-icon
              :icon="['fas', 'search']"
              class="absolute left-1 top-1/2 transform -translate-y-1/2 text-gray-400"
            />
            <button
              @click="openCreateModal"
              class="flex items-center bg-indigo-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-indigo-500 transition-all duration-300 ease-in-out transform hover:scale-105"
            >
              Foydalanuvchi qo'shish
            </button>
          </div>
        </div>

        <div class="flex-1 px-8 pb-8 overflow-auto">
          <div class="bg-gray-800 rounded-2xl shadow-2xl w-full h-full flex flex-col">
            <div class="overflow-x-auto">
              <table class="w-full table-auto">
                <thead>
                  <tr class="bg-gradient-to-r from-indigo-900 to-indigo-800 text-gray-300 sticky top-0">
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">ID</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">Ism</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">Email</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">Rollar</th>
                    <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-400">Harakat</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="user in users.data"
                    :key="user.id"
                    class="hover:bg-gray-700 transition-all duration-200 ease-in-out"
                  >
                    <td class="p-6 text-gray-400">{{ user.id }}</td>
                    <td class="p-6 text-gray-100 font-medium">{{ user.name }}</td>
                    <td class="p-6 text-gray-100 font-medium">{{ user.email }}</td>
                    <td class="p-6 text-gray-100 font-medium">
                      <span
                        v-if="user.roles.length"
                        class="flex flex-wrap gap-2"
                      >
                        <span
                          v-for="role in user.roles"
                          :key="role.id"
                          class="inline-block border border-indigo-600  text-white text-sm font-semibold px-3 py-1 rounded-full"
                        >
                          {{ role.name }}
                        </span>
                      </span>
                      <span v-else class="text-gray-400">Rol yo'q</span>
                    </td>
                    <td class="p-6 flex space-x-4 items-center">
                      <button
                        @click="openEditModal(user)"
                        class="text-indigo-400 hover:text-indigo-300 transition duration-150 ease-in-out"
                        title="Foydalanuvchini tahrirlash"
                      >
                        <font-awesome-icon :icon="['fas', 'user-pen']" class="w-5 h-5" />
                      </button>
                      <button
                        @click="confirmDeleteUser(user)"
                        class="text-red-400 hover:text-red-300 transition duration-150 ease-in-out"
                        title="Foydalanuvchini o'chirish"
                      >
                        <font-awesome-icon :icon="['fas', 'trash']" class="w-5 h-5" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-700">
              <div class="text-sm text-gray-400">
                {{ users.from }} dan {{ users.to }} gacha, jami {{ users.total }} foydalanuvchi
              </div>
              <div class="flex space-x-2">
                <Link
                  v-for="link in users.links"
                  :key="link.label"
                  :href="link.url || '#'"
                  class="px-4 py-2 rounded-lg"
                  :class="{
                    'bg-indigo-600 text-white': link.active,
                    'bg-gray-700 text-gray-300 hover:bg-gray-600': !link.active && link.url,
                    'bg-gray-800 text-gray-500 cursor-not-allowed': !link.url,
                  }"
                  preserve-scroll
                >
                  <span v-html="link.label"></span>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div
          v-if="isCreateModalOpen"
          class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]"
        >
          <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-auto">
            <CreateUser @close="closeCreateModal" />
          </div>
        </div>

        <div
          v-if="isEditModalOpen"
          class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]"
        >
          <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-auto">
            <EditUser :user="selectedUser" @close="closeEditModal" />
          </div>
        </div>

        <div
          v-if="isDeleteConfirmationOpen"
          class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1010]"
        >
          <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
            <div class="p-6">
              <h2 class="text-lg font-semibold text-gray-100 mb-4">Foydalanuvchini o'chirish</h2>
              <p class="text-gray-300 mb-4">
                Haqiqatan ham <span class="font-bold">{{ userToDelete?.name }}</span> ni
                o'chirmoqchimisiz?
              </p>
              <div class="flex justify-end gap-4">
                <button
                  class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                  @click="closeDeleteConfirmationModal"
                >
                  Bekor qilish
                </button>
                <button
                  class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                  @click="deleteConfirmedUser"
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
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import ApplicationMark from '@/Components/ApplicationMark.vue'
import Banner from '@/Components/Banner.vue'
import NavLink from '@/Components/NavLink.vue'
import CreateUser from '@/Pages/Users/Create.vue'
import EditUser from '@/Pages/Users/Edit.vue'
import { debounce } from 'lodash'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUserPen, faTrash, faSearch } from '@fortawesome/free-solid-svg-icons'

library.add(faUserPen, faTrash, faSearch)

const props = defineProps({
  users: Object,
  filters: Object,
  title: String,
  auth: Object,
})

const isCreateModalOpen = ref(false)
const isEditModalOpen = ref(false)
const isDeleteConfirmationOpen = ref(false)
const selectedUser = ref(null)
const userToDelete = ref(null)
const searchQuery = ref(props.filters?.search || '')

const openCreateModal = () => {
  isCreateModalOpen.value = true
}

const closeCreateModal = () => {
  isCreateModalOpen.value = false
}

const openEditModal = (user) => {
  selectedUser.value = user
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
  selectedUser.value = null
}

const confirmDeleteUser = (user) => {
  userToDelete.value = user
  isDeleteConfirmationOpen.value = true
}

const closeDeleteConfirmationModal = () => {
  isDeleteConfirmationOpen.value = false
  userToDelete.value = null
}

const deleteConfirmedUser = () => {
  if (userToDelete.value) {
    router.delete(route('users.destroy', userToDelete.value.id), {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        closeDeleteConfirmationModal()
        alert("Foydalanuvchi muvaffaqiyatli o'chirildi!")
      },
      onError: (errors) => {
        let errorMessage = 'Xatolik yuz berdi.'
        if (errors.authorization) {
          errorMessage = 'Sizda bu foydalanuvchini o\'chirish uchun ruxsat yo\'q.'
        } else if (errors.not_found) {
          errorMessage = 'Foydalanuvchi topilmadi.'
        }
        alert(errorMessage)
      },
    })
  }
}

const debouncedSearch = debounce(() => {
  router.get(
    route('users.index'),
    { search: searchQuery.value },
    { preserveState: true, preserveScroll: true, replace: true }
  )
}, 500)

watch(searchQuery, (newQuery) => {
  debouncedSearch()
})
</script>