<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useRecaptcha } from '@/composables/useRecaptcha';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    recaptcha_token: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const { execute: executeRecaptcha, isAvailable: recaptchaAvailable } = useRecaptcha();

const submit = async () => {
    // Execute reCAPTCHA if available
    if (recaptchaAvailable) {
        const token = await executeRecaptcha('register');
        form.recaptcha_token = token || '';
    }

    form
        .transform((data) => ({
            ...data,
            terms: data.terms ? 'on' : '',
        }))
        .post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <div class="relative mt-1">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full pr-20"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-3 text-sm font-medium text-slate-600 hover:text-slate-900 focus:outline-none focus:text-slate-900"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        :aria-pressed="showPassword"
                        @click="showPassword = !showPassword"
                    >
                        {{ showPassword ? 'Hide' : 'Show' }}
                    </button>
                </div>

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <div class="relative mt-1">
                    <TextInput
                        id="password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        class="block w-full pr-20"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-3 text-sm font-medium text-slate-600 hover:text-slate-900 focus:outline-none focus:text-slate-900"
                        :aria-label="showPasswordConfirmation ? 'Hide confirm password' : 'Show confirm password'"
                        :aria-pressed="showPasswordConfirmation"
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                    >
                        {{ showPasswordConfirmation ? 'Hide' : 'Show' }}
                    </button>
                </div>

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox id="terms" name="terms" v-model:checked="form.terms" required />
                    <span class="ms-2 text-sm text-gray-600">
                        I agree to the
                        <a
                            target="_blank"
                            :href="route('terms-and-conditions')"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Terms & Conditions
                        </a>
                    </span>
                </label>
                <InputError class="mt-2" :message="form.errors.terms" />
            </div>

            <InputError class="mt-2" :message="form.errors.recaptcha_token" />

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
