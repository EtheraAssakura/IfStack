<template>
  <div v-if="error" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
          <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Une erreur est survenue</h3>
        <div class="mt-2 px-7 py-3">
          <p class="text-sm text-gray-500">{{ error.message }}</p>
        </div>
        <div class="items-center px-4 py-3">
          <button
            @click="resetError"
            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
          >
            Réessayer
          </button>
        </div>
      </div>
    </div>
  </div>
  <slot v-else></slot>
</template>

<script setup>
import { onErrorCaptured, ref } from 'vue'

const error = ref(null)

const resetError = () => {
  error.value = null
}

onErrorCaptured((err) => {
  error.value = err
  return false
})
</script> 