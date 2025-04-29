<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
    user: Object,
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-6">
        <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">User Details</h1>

            <div class="space-y-4">
                <!-- Имя -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ user.name }}</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ user.email }}</p>
                </div>

                <!-- Дата создания -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created At</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ user.created_at }}</p>
                </div>

                <!-- Дата обновления -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Updated At</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ user.updated_at }}</p>
                </div>

                <!-- Кнопки -->
                <div class="flex gap-4 mt-6">
                    <Link
                        :href="route('users.edit', user.id)"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition dark:bg-indigo-500 dark:hover:bg-indigo-600"
                    >
                        Edit
                    </Link>
                    <Link
                        :href="route('users.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition dark:bg-gray-600 dark:hover:bg-gray-700"
                    >
                        Back
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>