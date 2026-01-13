<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    photo_url: {
        type: String,
        default: null,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    photo: null,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="form.transform((data) => ({ ...data, _method: 'patch' })).post(route('profile.update'), { forceFormData: true, onFinish: () => form.reset('photo') })"
            class="mt-6 space-y-6"
        >
            <!-- Profile Photo -->
            <div>
                <InputLabel value="Profile Photo" />
                <div class="mt-2 flex items-center gap-6">
                    <div v-if="photo_url" class="flex-shrink-0">
                        <img
                            :src="photo_url"
                            :alt="`Photo for ${form.name}`"
                            class="h-20 w-20 rounded-full border-2 border-slate-200 object-cover"
                        />
                    </div>
                    <div v-else class="flex h-20 w-20 items-center justify-center rounded-full border-2 border-slate-200 bg-slate-100">
                        <span class="text-xl font-semibold text-slate-500">
                            {{ form.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <input
                            type="file"
                            accept="image/jpeg,image/jpg,image/png"
                            @change="(e) => (form.photo = e.target.files[0])"
                            class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-portal-navy file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-portal-navy-dark"
                        />
                        <p class="mt-1 text-xs text-slate-500">
                            JPEG or PNG, max 2MB. Leave empty to keep current photo.
                        </p>
                        <InputError class="mt-2" :message="form.errors.photo" />
                    </div>
                </div>
            </div>

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

            <div>
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

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
