<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <Head title="Welcome to IfStack">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="min-h-screen bg-gradient-to-br from-[#023048] to-[#034268]">
        <header class="fixed w-full p-6 z-50">
            <nav class="max-w-7xl mx-auto flex justify-end items-center">
                <Link
                    v-if="page.props.auth.user"
                    :href="route('dashboard')"
                    class="px-6 py-2 bg-[#ffa034] text-white rounded-full font-medium transition-all hover:bg-[#d7912e] hover:shadow-lg"
                >
                    Dashboard
                </Link>
                <Link
                    v-else
                    :href="route('login')"
                    class="px-6 py-2 bg-[#319cb3] text-white rounded-full font-medium transition-all hover:bg-[#2b89a0] hover:shadow-lg"
                >
                    Connexion
                </Link>
            </nav>
        </header>

        <main class="relative min-h-screen flex items-center justify-center px-6">
            <div class="max-w-7xl w-full mx-auto grid lg:grid-cols-2 gap-12 items-center py-20">
                <div class="text-white space-y-8">
                    <h1 class="text-5xl font-bold leading-tight">
                        Gérez votre inventaire avec <span class="text-[#ffa034]">IfStack</span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        Une solution moderne et intuitive pour la gestion de vos stocks et commandes. Simplifiez vos opérations quotidiennes.
                    </p>
                    <div class="flex gap-4">
                        <a
                            :href="route('login')"
                            class="inline-flex items-center px-8 py-3 bg-[#ffa034] text-white rounded-full font-medium transition-all hover:bg-[#d7912e] hover:shadow-lg"
                        >
                            Commencer maintenant
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a
                            href="#features"
                            class="inline-flex items-center px-8 py-3 border-2 border-white text-white rounded-full font-medium transition-all hover:bg-white hover:text-[#023048]"
                        >
                            En savoir plus
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#023048]/20 to-transparent rounded-3xl transform rotate-6"></div>
                    <div class="relative bg-[#d2e9f7] rounded-3xl p-8 shadow-2xl">
                        <img src="/Logo.png" alt="IfStack Logo" class="w-full h-auto" />
                    </div>
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-[#023048] to-transparent"></div>
        </main>
    </div>
</template>

<style>
body {
    font-family: 'Inter var', sans-serif;
}
</style>
