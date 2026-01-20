<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    courses: {
        type: Array,
        required: true,
    },
    message: {
        type: String,
        default: null,
    },
    gpa: {
        type: Number,
        default: null,
    },
    totalGrades: {
        type: Number,
        default: 0,
    },
});

const searchTerm = ref("");
const semesterFilter = ref("all");
const showOnlyGraded = ref(false);

const semesters = computed(() => {
    const set = new Set();

    props.courses.forEach((course) => {
        if (course.semester) {
            set.add(course.semester);
        }
    });

    return Array.from(set).sort();
});

const filteredCourses = computed(() => {
    return props.courses
        .map((course) => {
            const filteredSubjects = (course.subjects || []).filter(
                (subject) => {
                    if (
                        showOnlyGraded.value &&
                        (subject.score === null || subject.score === undefined)
                    ) {
                        return false;
                    }

                    const term = searchTerm.value.trim().toLowerCase();

                    if (term) {
                        const haystack = (
                            course.course_code +
                            " " +
                            course.title +
                            " " +
                            subject.subject_code +
                            " " +
                            subject.title
                        ).toLowerCase();

                        if (!haystack.includes(term)) {
                            return false;
                        }
                    }

                    return true;
                }
            );

            return {
                ...course,
                subjects: filteredSubjects,
            };
        })
        .filter((course) => {
            if (
                semesterFilter.value !== "all" &&
                course.semester !== semesterFilter.value
            ) {
                return false;
            }

            if (searchTerm.value || showOnlyGraded.value) {
                return course.subjects && course.subjects.length > 0;
            }

            return true;
        });
});

const gradeSummary = computed(() => {
    let totalScore = 0;
    let gradedCount = 0;
    let totalSubjects = 0;
    const coursesWithAnyGrade = new Set();

    props.courses.forEach((course) => {
        (course.subjects || []).forEach((subject) => {
            totalSubjects += 1;
            if (subject.score !== null && subject.score !== undefined) {
                totalScore += Number(subject.score);
                gradedCount += 1;
                coursesWithAnyGrade.add(course.id);
            }
        });
    });

    return {
        totalSubjects,
        gradedCount,
        coursesWithGrades: coursesWithAnyGrade.size,
        averageScore: gradedCount ? totalScore / gradedCount : null,
    };
});

// Convert numeric score to letter grade
// Standard grading scale: A: 80-100, B: 70-79, C: 60-69, D: 50-59, F: 0-49
const getLetterGrade = (score) => {
    if (score === null || score === undefined) return null;
    const s = parseFloat(score);
    if (isNaN(s)) return null;
    if (s >= 80) return { letter: "A", class: "bg-emerald-100 text-emerald-800" };
    if (s >= 70) return { letter: "B", class: "bg-blue-100 text-blue-800" };
    if (s >= 60) return { letter: "C", class: "bg-amber-100 text-amber-800" };
    if (s >= 50) return { letter: "D", class: "bg-yellow-100 text-yellow-800" };
    return { letter: "F", class: "bg-red-100 text-red-800" };
};
</script>

<template>
    <Head title="My Grades" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Grades
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Grades' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- No Student Record Message -->
                <div v-if="message" class="portal-card p-6">
                    <div
                        class="rounded-lg bg-amber-50 p-4 ring-1 ring-amber-200"
                    >
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-amber-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-amber-800">
                                    Student Record Not Found
                                </h3>
                                <div class="mt-2 text-sm text-amber-700">
                                    <p>{{ message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grades List -->
                <div v-else class="portal-card overflow-hidden p-6">
                    <!-- Summary stats -->
                    <div
                        v-if="courses.length > 0"
                        class="mb-6 grid gap-4 md:grid-cols-4"
                    >
                        <div class="portal-card p-5">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Subjects
                            </p>
                            <p class="mt-2 text-2xl font-bold text-slate-900">
                                {{ gradeSummary.totalSubjects }}
                            </p>
                        </div>
                        <div class="portal-card p-5 bg-emerald-50">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                            >
                                Graded
                            </p>
                            <p class="mt-2 text-2xl font-bold text-emerald-900">
                                {{ gradeSummary.gradedCount }}
                            </p>
                        </div>
                        <div class="portal-card p-5 bg-amber-50">
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-amber-700"
                            >
                                Not graded yet
                            </p>
                            <p class="mt-2 text-2xl font-bold text-amber-900">
                                {{
                                    gradeSummary.totalSubjects -
                                    gradeSummary.gradedCount
                                }}
                            </p>
                        </div>
                        <div
                            v-if="gpa !== null"
                            class="portal-card p-5 bg-indigo-50"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-indigo-700"
                            >
                                GPA (Grade Point Average)
                            </p>
                            <p class="mt-2 text-2xl font-bold text-indigo-900">
                                {{ gpa.toFixed(2) }}
                            </p>
                            <p class="mt-1 text-xs text-indigo-700">
                                Based on {{ totalGrades }} grade(s)
                            </p>
                        </div>
                    </div>

                    <div
                        class="mb-4 flex flex-col gap-4 md:flex-row md:items-start md:justify-between"
                    >
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Subject Grades
                            </p>
                            <p class="mt-1 text-sm text-slate-600">
                                View grades for subjects in your enrolled
                                courses
                            </p>
                        </div>
                        <div
                            v-if="gradeSummary.gradedCount > 0 || gpa !== null"
                            class="rounded-lg bg-emerald-50 px-4 py-3 text-right shadow-sm"
                        >
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-emerald-700"
                            >
                                Academic Summary
                            </p>
                            <p
                                v-if="gradeSummary.gradedCount > 0"
                                class="mt-1 text-xs text-emerald-800"
                            >
                                Graded subjects:
                                <span class="font-semibold">{{
                                    gradeSummary.gradedCount
                                }}</span>
                            </p>
                            <p
                                v-if="gpa !== null"
                                class="mt-0.5 text-xs text-emerald-800"
                            >
                                Overall GPA:
                                <span class="font-semibold text-base">
                                    {{ gpa.toFixed(2) }}
                                </span>
                            </p>
                            <p
                                v-else-if="gradeSummary.averageScore !== null"
                                class="mt-0.5 text-xs text-emerald-800"
                            >
                                Average score:
                                <span class="font-semibold">
                                    {{ gradeSummary.averageScore.toFixed(2) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Grades by Course -->
                    <div v-if="courses.length > 0" class="space-y-6">
                        <!-- Filters -->
                        <div
                            class="mb-2 flex flex-col gap-3 md:flex-row md:items-end md:justify-between"
                        >
                            <div class="flex-1">
                                <label
                                    for="grades-search"
                                    class="block text-xs font-medium text-slate-600"
                                >
                                    Search
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                                    >
                                        <svg
                                            class="h-4 w-4 text-slate-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"
                                            />
                                        </svg>
                                    </div>
                                    <input
                                        id="grades-search"
                                        v-model="searchTerm"
                                        type="search"
                                        class="block w-full rounded-md border-slate-300 pl-9 pr-3 py-2 text-sm placeholder-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Search by course or subject name / code"
                                    />
                                </div>
                            </div>

                            <div
                                class="flex flex-col gap-2 md:w-72 md:flex-row md:items-center md:justify-end"
                            >
                                <div class="md:w-40">
                                    <label
                                        for="semester-filter"
                                        class="block text-xs font-medium text-slate-600"
                                    >
                                        Semester
                                    </label>
                                    <select
                                        id="semester-filter"
                                        v-model="semesterFilter"
                                        class="mt-1 block w-full rounded-md border-slate-300 py-2 pl-3 pr-8 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500"
                                    >
                                        <option value="all">
                                            All semesters
                                        </option>
                                        <option
                                            v-for="semester in semesters"
                                            :key="semester"
                                            :value="semester"
                                        >
                                            {{ semester }}
                                        </option>
                                    </select>
                                </div>

                                <label
                                    class="inline-flex items-center gap-2 text-xs font-medium text-slate-600"
                                >
                                    <input
                                        v-model="showOnlyGraded"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    Show only graded subjects
                                </label>
                            </div>
                        </div>

                        <div
                            v-if="filteredCourses.length > 0"
                            class="space-y-6"
                        >
                            <div
                                v-for="course in filteredCourses"
                                :key="course.id"
                                class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                            >
                                <div class="mb-3 flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                    >
                                        <img
                                            v-if="course.photo"
                                            :src="`/storage/${course.photo}`"
                                            :alt="`Photo for ${course.title}`"
                                            class="h-full w-full object-cover"
                                        />
                                        <span
                                            v-else
                                            class="text-xs font-semibold text-slate-500"
                                        >
                                            {{
                                                course.title
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </div>
                                    <div>
                                        <h3
                                            class="text-sm font-semibold text-slate-900"
                                        >
                                            {{ course.course_code }} -
                                            {{ course.title }}
                                        </h3>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ course.credits }} credits •
                                            {{ course.semester }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        course.subjects &&
                                        course.subjects.length > 0
                                    "
                                    class="overflow-x-auto"
                                >
                                    <table
                                        class="min-w-full divide-y divide-slate-200"
                                    >
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Subject
                                                </th>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-700"
                                                >
                                                    Score & Grade
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="divide-y divide-slate-200 bg-white"
                                        >
                                            <tr
                                                v-for="subject in course.subjects"
                                                :key="subject.id"
                                                class="bg-white hover:bg-slate-50 transition-colors"
                                            >
                                                <td
                                                    class="px-4 py-3 text-sm text-slate-700"
                                                >
                                                    <div
                                                        class="flex items-center gap-3"
                                                    >
                                                        <div
                                                            class="h-9 w-9 overflow-hidden rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center"
                                                        >
                                                            <img
                                                                v-if="
                                                                    subject.photo
                                                                "
                                                                :src="`/storage/${subject.photo}`"
                                                                :alt="`Photo for ${subject.title}`"
                                                                class="h-full w-full object-cover"
                                                            />
                                                            <span
                                                                v-else
                                                                class="text-xs font-semibold text-slate-500"
                                                            >
                                                                {{
                                                                    subject.title
                                                                        .charAt(
                                                                            0
                                                                        )
                                                                        .toUpperCase()
                                                                }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div
                                                                class="text-sm font-medium text-slate-900"
                                                            >
                                                                {{
                                                                    subject.subject_code
                                                                }}
                                                            </div>
                                                            <div
                                                                class="text-xs text-slate-500"
                                                            >
                                                                {{
                                                                    subject.title
                                                                }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <div
                                                        v-if="
                                                            subject.score !==
                                                                null &&
                                                            subject.score !==
                                                                undefined
                                                        "
                                                        class="flex items-center gap-2"
                                                    >
                                                        <span
                                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                                            :class="getLetterGrade(subject.score)?.class || 'bg-slate-100 text-slate-700'"
                                                        >
                                                            {{
                                                                Number(
                                                                    subject.score
                                                                ).toFixed(2)
                                                            }}
                                                        </span>
                                                        <span
                                                            v-if="getLetterGrade(subject.score)"
                                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                                            :class="getLetterGrade(subject.score).class"
                                                        >
                                                            {{
                                                                getLetterGrade(
                                                                    subject.score
                                                                ).letter
                                                            }}
                                                        </span>
                                                    </div>
                                                    <span
                                                        v-else
                                                        class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600"
                                                    >
                                                        Not graded yet
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else class="text-sm text-slate-500 py-2">
                                    No subjects with grades yet.
                                </div>
                            </div>
                        </div>
                        <div
                            v-else
                            class="rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-500"
                        >
                            No subjects match your current search or filters.
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="rounded-lg bg-slate-50 p-8 text-center">
                        <svg
                            class="mx-auto h-12 w-12 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                            />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">
                            No enrolled courses
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            You haven't enrolled in any courses yet.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
