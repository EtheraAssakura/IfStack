<template>
  <AppLayout :title="`Modifier ${user.name}`">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Modifier {{ user.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="mb-6">
              <InputLabel for="name" value="Nom" />
              <TextInput
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
              />
              <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="mb-6">
              <InputLabel for="email" value="Email" />
              <TextInput
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="mb-6">
              <InputLabel for="password" value="Mot de passe" />
              <TextInput
                id="password"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full"
              />
              <small class="mt-1 text-sm text-gray-500">
                Laissez vide pour conserver le mot de passe actuel
              </small>
              <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="mb-6">
              <InputLabel for="role" value="Rôle" />
              <select
                id="role"
                v-model="form.role_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                required
              >
                <option value="">Sélectionnez un rôle</option>
                <option v-for="role in roles" :key="role.id" :value="role.id">
                  {{ role.name }}
                </option>
              </select>
              <InputError :message="form.errors.role_id" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
              <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
              <Link
                :href="route('utilisateurs.index')"
                class="rounded-md px-4 py-2 text-gray-600 hover:text-gray-900"
              >
                Annuler
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
  user: {
    id: number;
    name: string;
    email: string;
    roles: {
      id: number;
      name: string;
    }[];
  };
  roles: {
    id: number;
    name: string;
  }[];
}>();

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  role_id: props.user.roles[0]?.id || '',
});

const submit = () => {
  form.put(route('utilisateurs.update', props.user.id));
};
</script>