<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    recipients: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    receiver_id: "",
    body: "",
});

const submit = () => {
    form.post(route("messages.store"));
};
</script>

<template>
    <Head title="New Message" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-slate-900">
                    New Message
                </h2>
                <Link
                    :href="route('messages.index')"
                    class="rounded-md bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50"
                >
                    Back to Inbox
                </Link>
            </div>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb
                    :items="[
                        { label: 'Messages', href: route('messages.index') },
                        { label: 'New Message' },
                    ]"
                />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="portal-card p-6">
                    <form @submit.prevent="submit">
                        <div class="space-y-6">
                            <!-- Receiver -->
                            <div>
                                <label
                                    for="receiver_id"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    To <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="receiver_id"
                                    v-model="form.receiver_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.receiver_id,
                                    }"
                                >
                                    <option value="">Select recipient</option>
                                    <option
                                        v-for="recipient in recipients"
                                        :key="recipient.id"
                                        :value="recipient.id"
                                    >
                                        {{ recipient.name }} ({{
                                            recipient.email
                                        }}) — {{ recipient.role }}
                                    </option>
                                </select>
                                <!-- Recipient Preview -->
                                <div
                                    v-if="form.receiver_id"
                                    class="mt-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3"
                                >
                                    <div
                                        class="h-10 w-10 overflow-hidden rounded-full border border-slate-200 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="
                                                recipients.find(
                                                    (r) =>
                                                        r.id == form.receiver_id
                                                )?.photo
                                            "
                                            :src="`/storage/${
                                                recipients.find(
                                                    (r) =>
                                                        r.id == form.receiver_id
                                                ).photo
                                            }`"
                                            :alt="`Photo of ${
                                                recipients.find(
                                                    (r) =>
                                                        r.id == form.receiver_id
                                                )?.name
                                            }`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-xs font-semibold text-slate-500"
                                        >
                                            {{
                                                recipients
                                                    .find(
                                                        (r) =>
                                                            r.id ==
                                                            form.receiver_id
                                                    )
                                                    ?.name?.charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-medium text-slate-900"
                                        >
                                            {{
                                                recipients.find(
                                                    (r) =>
                                                        r.id == form.receiver_id
                                                )?.name
                                            }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            {{
                                                recipients.find(
                                                    (r) =>
                                                        r.id == form.receiver_id
                                                )?.email
                                            }}
                                            ·
                                            {{
                                                recipients.find(
                                                    (r) =>
                                                        r.id == form.receiver_id
                                                )?.role
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <p
                                    v-if="form.errors.receiver_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.receiver_id }}
                                </p>
                            </div>

                            <!-- Body -->
                            <div>
                                <label
                                    for="body"
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    id="body"
                                    v-model="form.body"
                                    rows="6"
                                    required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-portal-navy focus:ring-portal-navy sm:text-sm"
                                    :class="{
                                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                                            form.errors.body,
                                    }"
                                ></textarea>
                                <p
                                    v-if="form.errors.body"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.body }}
                                </p>
                            </div>

                            <!-- Form Actions -->
                            <div
                                class="flex items-center justify-end gap-3 pt-4"
                            >
                                <Link
                                    :href="route('messages.index')"
                                    class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-md bg-portal-navy px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-portal-navy-dark focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2 disabled:opacity-50"
                                >
                                    <span v-if="form.processing"
                                        >Sending...</span
                                    >
                                    <span v-else>Send</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
