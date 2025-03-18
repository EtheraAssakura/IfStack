<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in to IfStack" />
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#d2e9f7] to-[#a7d5e9] dark:from-[#1d6d83] dark:to-[#023048] p-6">
        <div class="w-full max-w-md transform-gpu will-change-transform backface-visibility-hidden perspective-1000 transition-all duration-300 hover:scale-[1.02]">
            <div class="bg-white/90 backdrop-blur-lg dark:bg-[#023049]/90 rounded-2xl shadow-[0_20px_50px_rgba(8,_112,_184,_0.7)] p-8 space-y-6">
                <div class="text-center space-y-4">
                    <img src="/Logo.png" alt="IfStack Logo" class="mx-auto w-32 h-auto transform transition-transform duration-300 hover:rotate-3" />
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-[#023048] to-[#319cb3] dark:from-[#d2e9f7] dark:to-[#319cb3] bg-clip-text text-transparent">
                        Bienvenue
                    </h2>
                    <p class="text-sm text-[#666] dark:text-[#a7d5e9]">Connectez-vous à votre compte</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-4">
                        <div class="relative">
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-[#012338] border border-gray-200 dark:border-[#1d6d83] rounded-lg focus:ring-2 focus:ring-[#319cb3] focus:border-transparent transition-all duration-300 outline-none text-[#023048] dark:text-[#d2e9f7]"
                                placeholder="Email"
                            />
                            <span class="absolute inset-y-0 right-4 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </span>
                        </div>

                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-[#012338] border border-gray-200 dark:border-[#1d6d83] rounded-lg focus:ring-2 focus:ring-[#319cb3] focus:border-transparent transition-all duration-300 outline-none text-[#023048] dark:text-[#d2e9f7]"
                                placeholder="Mot de passe"
                            />
                            <span class="absolute inset-y-0 right-4 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-2 cursor-pointer group">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="w-4 h-4 rounded border-gray-300 text-[#319cb3] focus:ring-[#319cb3] transition-all duration-300"
                            />
                            <span class="text-sm text-gray-600 dark:text-gray-300 group-hover:text-[#319cb3] transition-colors duration-300">Se souvenir de moi</span>
                        </label>

                        <a href="#" class="text-sm font-medium text-[#319cb3] hover:text-[#1d6d83] transition-colors duration-300">
                            Mot de passe oublié?
                        </a>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 px-4 bg-gradient-to-r from-[#319cb3] to-[#1d6d83] text-white rounded-lg font-medium transform transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#319cb3]"
                    >
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 animate-spin" v-if="form.processing" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Connexion...' : 'Se connecter' }}</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.backface-visibility-hidden {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.perspective-1000 {
    perspective: 1000px;
    -webkit-perspective: 1000px;
}
</style>
