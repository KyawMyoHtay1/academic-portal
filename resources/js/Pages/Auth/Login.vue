<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useRecaptcha } from "@/composables/useRecaptcha";
import { ref } from "vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
    recaptcha_token: "",
});

const showPassword = ref(false);

const { execute: executeRecaptcha, isAvailable: recaptchaAvailable } = useRecaptcha();

const submit = async () => {
    // Execute reCAPTCHA if available
    if (recaptchaAvailable) {
        const token = await executeRecaptcha('login');
        form.recaptcha_token = token || '';
    }

    form
        .transform((data) => ({
            ...data,
            remember: data.remember ? 'on' : '',
        }))
        .post(route("login"), {
            onFinish: () => form.reset("password"),
        });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />

                <div class="relative mt-1">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full pr-20"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-3 text-sm font-medium text-slate-600 hover:text-slate-900 focus:outline-none focus:text-slate-900"
                        @click="showPassword = !showPassword"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        :aria-pressed="showPassword"
                    >
                        {{ showPassword ? "Hide" : "Show" }}
                    </button>
                </div>

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block">
                <label class="flex items-center">
                    <Checkbox id="remember" name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <InputError class="mt-2" :message="form.errors.recaptcha_token" />

            <div
                class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between"
            >
                <div
                    v-if="$page.props?.canRegister ?? true"
                    class="text-gray-600"
                >
                    New here?
                    <Link
                        :href="route('register')"
                        class="font-semibold text-indigo-600 hover:text-indigo-800"
                    >
                        Create an account
                    </Link>
                </div>

                <div
                    class="flex flex-col items-start gap-3 sm:flex-row sm:items-center"
                >
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Forgot your password?
                    </Link>

                    <PrimaryButton
                        class="sm:ms-2 rounded-full px-5 py-2 text-sm font-semibold shadow-sm"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Login
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
