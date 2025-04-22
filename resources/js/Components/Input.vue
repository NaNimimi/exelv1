<template>
    <input
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      v-bind="$attrs"
      class="w-full bg-gray-700 text-gray-100 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
    >
  </template>
  
  <script setup>
  defineProps({
    modelValue: [String, Number]
  })
  defineEmits(['update:modelValue'])
  </script>