<template>
  <AppSideBar title="Nouvelle catégorie">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Nouvelle catégorie
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">
                  Nom
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">
                  Description
                </label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
              </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
              <Link
                :href="route('categories.index')"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
              >
                Annuler
              </Link>
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                :disabled="form.processing"
              >
                Créer
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppSideBar>
</template>

<script setup lang="ts">
import AppSideBar from '@/components/AppSidebar.vue'
import { Link, useForm } from '@inertiajs/vue3'

interface FormData {
  name: string;
  description: string;
}

const form = useForm<FormData>({
  name: '',
  description: '',
});

const submit = () => {
  form.post(route('categories.store'));
};
</script>
