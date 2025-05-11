<template>
    <div>
      <Head title="Rollar" />
  
      <Banner />
  
      <div class="min-h-screen bg-gray-100 dark:bg-gradient-to-br dark:from-gray-900 dark:to-gray-800">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
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
              <div class="flex items-center">
              <ThemeSwitcher />
            </div>
            </div>
          </div>
        </nav>
  
        <div class="w-full h-full text-gray-900 dark:text-gray-100 flex flex-col">
          <div class="flex justify-between items-center px-8 py-6">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">Rollar</h1>
            <div class="flex items-center space-x-4">
              <button
                @click="openCreateModal"
                class="flex items-center bg-indigo-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-indigo-500 dark:hover:bg-indigo-700 transition-all duration-300 ease-in-out transform hover:scale-105"
              >
                Rol qo'shish
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
                      <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-600 dark:text-gray-400">Nomi</th>
                      <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-600 dark:text-gray-400">Ruxsatlar</th>
                      <th class="p-6 text-left font-semibold text-sm uppercase tracking-wider text-gray-600 dark:text-gray-400">Harakat</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="role in roles"
                      :key="role.id"
                      class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 ease-in-out"
                    >
                      <td class="p-6 text-gray-600 dark:text-gray-400">{{ role.id }}</td>
                      <td class="p-6 text-gray-900 dark:text-gray-100 font-medium">{{ role.name }}</td>
                      <td class="p-6 text-gray-600 dark:text-gray-400">
                        {{ role.permissions.map(p => p.name).join(', ') || "Ruxsatlar yo'q" }}
                      </td>
                      <td class="p-6 flex space-x-4 items-center">
                        <template v-if="role.name !== 'admin'">
                          <button
                            @click="openAssignPermissionModal(role)"
                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition duration-150 ease-in-out"
                            title="Ruxsat biriktirish"
                          >
                            <font-awesome-icon :icon="['fas', 'plus']" class="w-5 h-5" />
                          </button>
                          <button
                            @click="openRevokePermissionModal(role)"
                            class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500 dark:hover:text-yellow-300 transition duration-150 ease-in-out"
                            title="Ruxsat olib tashlash"
                          >
                            <font-awesome-icon :icon="['fas', 'minus']" class="w-5 h-5" />
                          </button>
                          <button
                            @click="openAssignRoleToUserModal(role)"
                            class="text-green-600 dark:text-green-400 hover:text-green-500 dark:hover:text-green-300 transition duration-150 ease-in-out"
                            title="Foydalanuvchiga rol biriktirish"
                          >
                            <font-awesome-icon :icon="['fas', 'user-plus']" class="w-5 h-5" />
                          </button>
                          <button
                            @click="openRevokeRoleFromUserModal(role)"
                            class="text-purple-600 dark:text-purple-400 hover:text-purple-500 dark:hover:text-purple-300 transition duration-150 ease-in-out"
                            title="Foydalanuvchidan rol olib tashlash"
                          >
                            <font-awesome-icon :icon="['fas', 'user-minus']" class="w-5 h-5" />
                          </button>
                          <button
                            @click="confirmDeleteRole(role)"
                            class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition duration-150 ease-in-out"
                            title="Rolni o'chirish"
                          >
                            <font-awesome-icon :icon="['fas', 'trash']" class="w-5 h-5" />
                          </button>
                        </template>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
  
          <!-- Create Role Modal -->
          <div
            v-if="isCreateModalOpen"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]"
          >
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-auto">
              <CreateRole :permissions="permissions" @close="closeCreateModal" />
            </div>
          </div>
  
          <!-- Assign Permission Modal -->
          <div
            v-if="isAssignPermissionModalOpen"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]"
          >
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-auto">
              <AssignPermission :role="selectedRole" :permissions="permissions" @close="closeAssignPermissionModal" />
            </div>
          </div>
  
          <!-- Revoke Permission Modal -->
          <div
            v-if="isRevokePermissionModalOpen"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]"
          >
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-auto">
              <RevokePermission :role="selectedRole" @close="closeRevokePermissionModal" />
            </div>
          </div>
  
          <!-- Assign Role to User Modal -->
          <div
            v-if="isAssignRoleToUserModalOpen"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]"
          >
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-auto">
              <AssignRoleToUser :role="selectedRole" :users="users" @close="closeAssignRoleToUserModal" />
            </div>
          </div>
  
          <!-- Revoke Role from User Modal -->
          <div
            v-if="isRevokeRoleFromUserModalOpen"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1000]"
          >
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-auto">
              <RevokeRoleFromUser :role="selectedRole" :users="users" @close="closeRevokeRoleFromUserModal" />
            </div>
          </div>
  
          <!-- Delete Role Confirmation Modal -->
          <div
            v-if="isDeleteConfirmationOpen"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-[1010]"
          >
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
              <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rolni o'chirish</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                  Haqiqatan ham <span class="font-bold">{{ roleToDelete?.name }}</span> rolini
                  o'chirmoqchimisiz?
                </p>
                <div class="flex justify-end gap-4">
                  <button
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    @click="closeDeleteConfirmationModal"
                  >
                    Bekor qilish
                  </button>
                  <button
                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    @click="deleteConfirmedRole"
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
  import { Head, Link, router } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import ApplicationMark from '@/Components/ApplicationMark.vue';
  import Banner from '@/Components/Banner.vue';
  import NavLink from '@/Components/NavLink.vue';
  import CreateRole from '@/Pages/Roles/CreateRole.vue';
  import AssignPermission from '@/Pages/Roles/AssignPermission.vue';
  import RevokePermission from '@/Pages/Roles/RevokePermission.vue';
  import AssignRoleToUser from '@/Pages/Roles/AssignRoleToUser.vue';
  import RevokeRoleFromUser from '@/Pages/Roles/RevokeRoleFromUser.vue';
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  import { library } from '@fortawesome/fontawesome-svg-core';
  import { faPlus, faMinus, faUserPlus, faUserMinus, faTrash } from '@fortawesome/free-solid-svg-icons';
  
  library.add(faPlus, faMinus, faUserPlus, faUserMinus, faTrash);
  
  const props = defineProps({
    roles: Array,
    permissions: Array,
    users: Array,
  });
  
  const isCreateModalOpen = ref(false);
  const isAssignPermissionModalOpen = ref(false);
  const isRevokePermissionModalOpen = ref(false);
  const isAssignRoleToUserModalOpen = ref(false);
  const isRevokeRoleFromUserModalOpen = ref(false);
  const isDeleteConfirmationOpen = ref(false);
  const selectedRole = ref(null);
  const roleToDelete = ref(null);
  
  const openCreateModal = () => {
    isCreateModalOpen.value = true;
  };
  
  const closeCreateModal = () => {
    isCreateModalOpen.value = false;
  };
  
  const openAssignPermissionModal = (role) => {
    selectedRole.value = role;
    isAssignPermissionModalOpen.value = true;
  };
  
  const closeAssignPermissionModal = () => {
    isAssignPermissionModalOpen.value = false;
    selectedRole.value = null;
  };
  
  const openRevokePermissionModal = (role) => {
    selectedRole.value = role;
    isRevokePermissionModalOpen.value = true;
  };
  
  const closeRevokePermissionModal = () => {
    isRevokePermissionModalOpen.value = false;
    selectedRole.value = null;
  };
  
  const openAssignRoleToUserModal = (role) => {
    selectedRole.value = role;
    isAssignRoleToUserModalOpen.value = true;
  };
  
  const closeAssignRoleToUserModal = () => {
    isAssignRoleToUserModalOpen.value = false;
    selectedRole.value = null;
  };
  
  const openRevokeRoleFromUserModal = (role) => {
    selectedRole.value = role;
    isRevokeRoleFromUserModalOpen.value = true;
  };
  
  const closeRevokeRoleFromUserModal = () => {
    isRevokeRoleFromUserModalOpen.value = false;
    selectedRole.value = null;
  };
  
  const confirmDeleteRole = (role) => {
    roleToDelete.value = role;
    isDeleteConfirmationOpen.value = true;
  };
  
  const closeDeleteConfirmationModal = () => {
    isDeleteConfirmationOpen.value = false;
    roleToDelete.value = null;
  };
  
  const deleteConfirmedRole = () => {
    if (roleToDelete.value) {
      router.delete(route('roles.destroy', roleToDelete.value.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          closeDeleteConfirmationModal();
          alert("Rol muvaffaqiyatli o'chirildi.");
        },
        onError: (errors) => {
          alert("Rolni o'chirishda xatolik yuz berdi.");
        },
      });
    }
  };
  </script>