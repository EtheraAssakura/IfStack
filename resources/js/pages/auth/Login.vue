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
    <div class="flex min-h-screen flex-col items-center justify-center bg-[#d2e9f7] p-6 text-[#023048] dark:bg-[#1d6d83]">
        <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-xl shadow-2xl dark:bg-[#023049]">
            <div class="text-center">
                <img src="/Logo.png" alt="IfStack Logo" class="mx-auto" style="width: 150px" />
                <h2 class="mt-6 text-3xl font-extrabold text-[#023048] dark:text-[#d2e9f7]">Log in to your account</h2>
                <p class="mt-2 text-sm text-[#023048] dark:text-[#d2e9f7]">Enter your email and password below to log in</p>
            </div>
            <form @submit.prevent="submit" class="mt-8 space-y-6">
                <div class="rounded-md shadow-2xl -space-y-px">
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            v-model="form.email"
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-[#319cb3] focus:border-[#319cb3] focus:z-10 sm:text-sm"
                            placeholder="Email address"
                        />
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            v-model="form.password"
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-[#319cb3] focus:border-[#319cb3] focus:z-10 sm:text-sm"
                            placeholder="Password"
                        />
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="remember_me"
                            name="remember_me"
                            type="checkbox"
                            v-model="form.remember"
                            class="h-4 w-4 text-[#319cb3] focus:ring-[#319cb3] border-gray-300 rounded"
                        />
                        <label for="remember_me" class="ml-2 block text-sm text-[#023048] dark:text-[#d2e9f7]">
                            Remember me
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="font-medium text-[#319cb3] hover:text-[#023048]">
                            Forgot your password?
                        </a>
                    </div>
                </div>
                <div>
                    <button
                        type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-[#319cb3] hover:bg-[#1d6d83] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#319cb3]"
                        :disabled="form.processing"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-[#d2e9f7] group-hover:text-[#023048]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3a1 1 0 001 1h2a1 1 0 100-2h-1V7z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Log in
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
