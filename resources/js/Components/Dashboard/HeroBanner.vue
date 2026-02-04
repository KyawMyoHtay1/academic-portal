<script setup>
import { computed } from 'vue';

const props = defineProps({
    user: Object,
    role: String,
});

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good Morning';
    if (hour < 18) return 'Good Afternoon';
    return 'Good Evening';
});

const todayDate = computed(() => {
    return new Date().toLocaleDateString('en-GB', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
});

const roleColors = computed(() => {
    switch (props.role) {
        case 'staff':
            return 'from-slate-800 to-slate-900 shadow-slate-900/20';
        case 'teacher':
            return 'from-emerald-600 to-teal-700 shadow-emerald-700/20';
        case 'student':
            return 'from-blue-600 to-indigo-700 shadow-blue-700/20';
        default:
            return 'from-slate-700 to-slate-800';
    }
});

const roleLabel = computed(() => {
     if (props.role === 'staff') return 'Staff Dashboard';
     if (props.role === 'teacher') return 'Teacher Workspace';
     if (props.role === 'student') return 'Student Portal';
     return 'Dashboard';
});
</script>

<template>
    <div
        class="relative overflow-hidden rounded-2xl bg-gradient-to-br p-8 text-white shadow-xl"
        :class="roleColors"
    >
        <!-- Decorational blobs -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 h-48 w-48 rounded-full bg-black/10 blur-2xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-xs font-medium backdrop-blur-md border border-white/10">
                        {{ roleLabel }}
                    </span>
                    <span class="text-xs font-medium text-white/80">
                        {{ todayDate }}
                    </span>
                </div>
                
                <h1 class="text-3xl font-bold tracking-tight text-white md:text-5xl">
                    {{ greeting }}, {{ user?.name?.split(' ')[0] }}!
                </h1>
                
                <p class="max-w-xl text-lg text-blue-50/90" v-if="role === 'student'">
                    Stay updated with your course enrolments, check your latest grades, and manage your academic profile.
                </p>
                <p class="max-w-xl text-lg text-emerald-50/90" v-else-if="role === 'teacher'">
                     Manage your subjects, track student performance, and organize your teaching schedule efficiently.
                </p>
                 <p class="max-w-xl text-lg text-slate-300" v-else>
                    Manage students, courses and academic data. High-level view of registrations, fees and attendance across the institution.
                </p>
            </div>

            <!-- Optional right-side stat or action -->
            <div class="hidden md:block">
                 <div class="h-24 w-24 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20 shadow-inner">
                    <img
                        v-if="user?.photo"
                        :src="`/storage/${user.photo}`"
                        :alt="user.name"
                        class="h-full w-full object-cover rounded-2xl"
                    />
                     <span v-else class="text-3xl font-bold opacity-80">
                        {{ user?.name?.charAt(0) }}
                     </span>
                 </div>
            </div>
        </div>
    </div>
</template>
