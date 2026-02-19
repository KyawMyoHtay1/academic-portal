<script setup>
import { computed } from "vue";

const props = defineProps({
    /**
     * entries shape (minimum):
     * { id, day_of_week, start_time, end_time, subject_code, subject_title, location, course_code? }
     */
    entries: {
        type: Array,
        required: true,
    },
    /**
     * Optional: show course info in blocks.
     */
    showCourse: {
        type: Boolean,
        default: false,
    },
    /**
     * Optional: limit to these days (for example ["Monday", "Tuesday", ...]).
     */
    days: {
        type: Array,
        default: null,
    },
    /**
     * Highlight the current weekday column.
     */
    highlightToday: {
        type: Boolean,
        default: true,
    },
    /**
     * Display format for times.
     */
    timeFormat: {
        type: String,
        default: "12h", // 12h | 24h
    },
});

const DEFAULT_DAYS = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

const dayColumns = computed(() => props.days ?? DEFAULT_DAYS);

const parseTimeParts = (value) => {
    if (!value) return null;
    const [hhRaw = "0", mmRaw = "0"] = String(value).split(":");
    const hh = Number.parseInt(hhRaw, 10);
    const mm = Number.parseInt(mmRaw, 10);
    if (Number.isNaN(hh) || Number.isNaN(mm)) return null;
    return { hh, mm };
};

const toMinutes = (hhmm) => {
    const parts = parseTimeParts(hhmm);
    if (!parts) return null;
    return parts.hh * 60 + parts.mm;
};

const clamp = (v, min, max) => Math.min(Math.max(v, min), max);

const normalized = computed(() => {
    return (props.entries ?? [])
        .map((e) => {
            const start = toMinutes(e.start_time);
            const end = toMinutes(e.end_time);
            return {
                ...e,
                _start: start,
                _end: end,
                _duration: start != null && end != null ? Math.max(end - start, 0) : 0,
            };
        })
        .filter((e) => e._start != null && e._end != null && e._end > e._start);
});

const bounds = computed(() => {
    const starts = normalized.value.map((e) => e._start);
    const ends = normalized.value.map((e) => e._end);

    // Default school day bounds if empty.
    let minStart = starts.length ? Math.min(...starts) : 9 * 60;
    let maxEnd = ends.length ? Math.max(...ends) : 17 * 60;

    // Pad to nearest hour.
    minStart = Math.floor(minStart / 60) * 60;
    maxEnd = Math.ceil(maxEnd / 60) * 60;

    // Clamp to a reasonable timetable window.
    minStart = clamp(minStart, 6 * 60, 12 * 60);
    maxEnd = clamp(maxEnd, 13 * 60, 22 * 60);

    return { minStart, maxEnd };
});

const formatMinutes = (minutes) => {
    const safe = Number(minutes);
    if (!Number.isFinite(safe)) return "-";

    const hh = Math.floor(safe / 60);
    const mm = safe % 60;
    const mmLabel = String(mm).padStart(2, "0");

    if (props.timeFormat === "24h") {
        return `${String(hh).padStart(2, "0")}:${mmLabel}`;
    }

    const suffix = hh >= 12 ? "PM" : "AM";
    const hour12 = hh % 12 || 12;
    return `${hour12}:${mmLabel} ${suffix}`;
};

const formatTime = (value) => {
    const parts = parseTimeParts(value);
    if (!parts) return String(value ?? "-");
    return formatMinutes(parts.hh * 60 + parts.mm);
};

const formatRange = (entry) => `${formatTime(entry?.start_time)} - ${formatTime(entry?.end_time)}`;

const timeSlots = computed(() => {
    const { minStart, maxEnd } = bounds.value;
    const slots = [];
    for (let t = minStart; t <= maxEnd; t += 60) {
        slots.push({ minutes: t, label: formatMinutes(t) });
    }
    return slots;
});

const laneByDay = computed(() => {
    const map = {};
    for (const d of dayColumns.value) map[d] = [];

    for (const e of normalized.value) {
        if (!map[e.day_of_week]) map[e.day_of_week] = [];
        map[e.day_of_week].push(e);
    }

    for (const d of Object.keys(map)) {
        map[d].sort((a, b) => a._start - b._start);
    }

    return map;
});

const colorClass = (subjectCode) => {
    // Stable pseudo-hash from subject code to color family.
    const s = String(subjectCode ?? "");
    let hash = 0;
    for (let i = 0; i < s.length; i++) hash = (hash * 31 + s.charCodeAt(i)) >>> 0;

    const palette = [
        "bg-blue-100 text-blue-900 ring-blue-200",
        "bg-emerald-100 text-emerald-900 ring-emerald-200",
        "bg-amber-100 text-amber-900 ring-amber-200",
        "bg-purple-100 text-purple-900 ring-purple-200",
        "bg-rose-100 text-rose-900 ring-rose-200",
        "bg-indigo-100 text-indigo-900 ring-indigo-200",
        "bg-cyan-100 text-cyan-900 ring-cyan-200",
    ];

    return palette[hash % palette.length];
};

const blockStyle = (entry) => {
    const { minStart, maxEnd } = bounds.value;
    const total = maxEnd - minStart;
    const top = ((entry._start - minStart) / total) * 100;
    const height = ((entry._end - entry._start) / total) * 100;
    return {
        top: `${top}%`,
        height: `${height}%`,
    };
};

const todayName = computed(() => {
    if (!props.highlightToday) return null;
    try {
        return new Intl.DateTimeFormat("en-US", { weekday: "long" }).format(new Date());
    } catch {
        return null;
    }
});

const browserTimezone = computed(() => {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone || "Local";
    } catch {
        return "Local";
    }
});
</script>

<template>
    <div class="portal-card overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50 px-4 py-2 text-[11px] text-slate-600">
            Times are shown in schedule local time. Browser timezone: {{ browserTimezone }}
        </div>

        <!-- Desktop: week grid -->
        <div class="hidden md:block">
            <div class="grid grid-cols-[96px,1fr]">
                <!-- Time column -->
                <div class="border-r border-slate-200 bg-slate-50">
                    <div class="h-12 border-b border-slate-200"></div>
                    <div class="relative">
                        <div
                            v-for="t in timeSlots"
                            :key="t.minutes"
                            class="h-20 border-b border-slate-200 px-3 py-2 text-xs font-medium text-slate-500"
                        >
                            {{ t.label }}
                        </div>
                    </div>
                </div>

                <!-- Days -->
                <div class="grid" :style="{ gridTemplateColumns: `repeat(${dayColumns.length}, minmax(0, 1fr))` }">
                    <div
                        v-for="d in dayColumns"
                        :key="d"
                        class="border-r border-slate-200 last:border-r-0"
                    >
                        <div
                            class="flex h-12 items-center justify-between border-b border-slate-200 bg-white px-4"
                            :class="{
                                'bg-amber-50 ring-1 ring-inset ring-amber-200': todayName === d,
                            }"
                        >
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                {{ d }}
                                <span
                                    v-if="todayName === d"
                                    class="ml-2 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-800"
                                >
                                    Today
                                </span>
                            </p>
                            <p class="text-[10px] text-slate-400">
                                {{ (laneByDay[d] ?? []).length }} session(s)
                            </p>
                        </div>

                        <div class="relative" style="height: 560px">
                            <!-- hour grid lines -->
                            <div
                                v-for="t in timeSlots.slice(0, -1)"
                                :key="`${d}-${t.minutes}`"
                                class="h-20 border-b border-slate-200/70"
                            />

                            <!-- blocks -->
                            <div
                                v-for="entry in laneByDay[d] ?? []"
                                :key="entry.id"
                                class="absolute left-2 right-2 rounded-lg p-2 text-xs shadow-sm ring-1"
                                :class="colorClass(entry.subject_code)"
                                :style="blockStyle(entry)"
                                :title="`${entry.subject_code} ${formatRange(entry)}`"
                            >
                                <p class="truncate font-semibold">
                                    {{ entry.subject_code }}
                                    <span class="font-normal" v-if="showCourse && entry.course_code">
                                        - {{ entry.course_code }}
                                    </span>
                                </p>
                                <p class="mt-0.5 line-clamp-2 text-[11px] text-slate-700/80">
                                    {{ entry.subject_title }}
                                </p>
                                <p class="mt-1 flex items-center gap-2 text-[10px] text-slate-700/80">
                                    <span>{{ formatRange(entry) }}</span>
                                    <span v-if="entry.location">- {{ entry.location }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile: list per day -->
        <div class="md:hidden">
            <div class="border-b border-slate-200 bg-white px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">
                    Weekly timetable
                </p>
                <p class="mt-1 text-sm text-slate-600">
                    Sessions grouped by day
                </p>
            </div>

            <div class="divide-y divide-slate-200">
                <div v-for="d in dayColumns" :key="`m-${d}`" class="p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        {{ d }}
                    </p>
                    <div v-if="(laneByDay[d] ?? []).length === 0" class="mt-2 text-sm text-slate-500">
                        No sessions.
                    </div>
                    <div v-else class="mt-3 space-y-2">
                        <div
                            v-for="entry in laneByDay[d]"
                            :key="`m-${entry.id}`"
                            class="rounded-lg p-3 ring-1"
                            :class="colorClass(entry.subject_code)"
                        >
                            <p class="text-sm font-semibold text-slate-900">
                                {{ entry.subject_code }}
                                <span class="font-normal" v-if="showCourse && entry.course_code">
                                    - {{ entry.course_code }}
                                </span>
                            </p>
                            <p class="mt-1 text-xs text-slate-700/80">
                                {{ entry.subject_title }}
                            </p>
                            <p class="mt-2 text-xs text-slate-700/80">
                                {{ formatRange(entry) }}
                                <span v-if="entry.location">- {{ entry.location }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
