<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome to IfStack">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#d2e9f7] p-6 text-[#023048] dark:bg-[#023048] lg:justify-center lg:p-8">
        <header class="mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#023048] px-5 py-1.5 text-sm leading-normal text-[#023048] hover:bg-[#319cb3] dark:text-[#d2e9f7] dark:hover:bg-[#319cb3]"
                >
                    Dashboard
                </Link>
                <Link
                    v-else
                    :href="route('login')"
                    class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#023048] hover:border-[#023048] dark:text-[#d2e9f7] dark:hover:border-[#319cb3]"
                >
                    Log in
                </Link>
            </nav>
        </header>
        <div class="flex w-full items-center justify-center lg:grow">
            <main class="flex w-full max-w-[335px] flex-col overflow-hidden rounded-lg lg:max-w-4xl lg:flex-col">
                <div class="flex justify-center items-center w-full bg-[#d2e9f7] dark:bg-[#023048] lg:w-full h-[335px]">
                    <img src="/Logo.png" alt="IfStack Logo" class="object-contain h-full" />
                </div>
                <div class="flex flex-col justify-center items-center bg-white p-6 text-[13px] leading-[20px] shadow-lg dark:bg-[#023048] dark:text-[#d2e9f7] ">
                    <h1 class="mb-4 text-2xl font-bold text-[#023048] dark:text-[#d2e9f7]">Welcome to IfStack</h1>
                    <p class="mb-6 text-[#023048] dark:text-[#d2e9f7]">
                        Please log in to manage your stack efficiently.
                    </p>
                    <a
                        :href="route('login')"
                        class="inline-block rounded-sm bg-[#319cb3] px-5 py-2 text-sm font-medium text-white hover:bg-[#023048] dark:bg-[#ffa034] dark:hover:bg-[#d7912e]"
                    >
                        Log in
                    </a>
                </div>
            </main>
        </div>
    </div>
</template>
