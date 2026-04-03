import { router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

export function useStaffAttendanceReport(props) {
const searchStudents = ref("");
const searchCourses = ref("");
const searchSubjects = ref("");
const searchRecent = ref("");
const searchSessionRecords = ref("");
const programmeFilter = ref(props.filters?.programme || "all");
const intakeYearFilter = ref(props.filters?.intake_year || "all");
const semesterFilter = ref(props.filters?.semester || "all");
const courseFilter = ref(
    props.filters?.course_id ? String(props.filters.course_id) : "all"
);
const subjectFilter = ref(
    props.filters?.subject_id ? String(props.filters.subject_id) : "all"
);
const dateFrom = ref(props.filters?.date_from || "");
const dateTo = ref(props.filters?.date_to || "");
const defaultThreshold = Number(props.defaults?.threshold ?? 75);
const defaultCooldownDays = Number(props.defaults?.cooldown_days ?? 7);
const thresholdFilter = ref(Number(props.filters?.threshold ?? defaultThreshold));
const courseThresholdFilter = ref(
    props.filters?.course_threshold ?? ""
);
const subjectThresholdFilter = ref(
    props.filters?.subject_threshold ?? ""
);
const alertCooldownDays = ref(Number(props.filters?.cooldown_days ?? defaultCooldownDays));
const trendMode = ref(props.filters?.trend_mode || "weekly");
const sendingAlerts = ref(false);
const selectedSessionDate = ref(
    props.filters?.session_date ||
        props.sessionDrilldown?.selected_date ||
        ""
);

const normalizedThreshold = () => {
    const value = Number(thresholdFilter.value);
    return Number.isFinite(value) && value >= 1 && value <= 100 ? value : undefined;
};

const normalizedCooldownDays = () => {
    const value = Number(alertCooldownDays.value);
    return Number.isFinite(value) && value >= 0 && value <= 90 ? value : undefined;
};

const normalizedScopedThreshold = (rawValue) => {
    const value = Number(rawValue);
    return Number.isFinite(value) && value >= 1 && value <= 100 ? value : undefined;
};

const normalizedCourseThreshold = () =>
    normalizedScopedThreshold(courseThresholdFilter.value);
const normalizedSubjectThreshold = () =>
    normalizedScopedThreshold(subjectThresholdFilter.value);
const configuredThreshold = (rawValue) =>
    normalizedScopedThreshold(rawValue);

const subjectOptions = computed(() => {
    const allSubjects = props.options?.subjects ?? [];
    if (courseFilter.value === "all") {
        return allSubjects;
    }

    return allSubjects.filter(
        (subject) => String(subject.course_id) === String(courseFilter.value)
    );
});

const selectedCourseOption = computed(() => {
    if (courseFilter.value === "all") {
        return null;
    }

    return (
        (props.options?.courses ?? []).find(
            (course) => String(course.id) === String(courseFilter.value)
        ) ?? null
    );
});

const selectedSubjectOption = computed(() => {
    if (subjectFilter.value === "all") {
        return null;
    }

    return (
        (props.options?.subjects ?? []).find(
            (subject) => String(subject.id) === String(subjectFilter.value)
        ) ?? null
    );
});

const effectiveThreshold = computed(() => {
    const subjectValue = normalizedSubjectThreshold();
    if (subjectFilter.value !== "all" && subjectValue !== undefined) {
        return {
            value: subjectValue,
            source: "subject_override",
            label: "subject override",
        };
    }

    const subjectConfigured = configuredThreshold(
        selectedSubjectOption.value?.attendance_threshold
    );
    if (subjectFilter.value !== "all" && subjectConfigured !== undefined) {
        return {
            value: subjectConfigured,
            source: "subject_configured",
            label: "subject configured",
        };
    }

    const courseValue = normalizedCourseThreshold();
    if (courseFilter.value !== "all" && courseValue !== undefined) {
        return {
            value: courseValue,
            source: "course_override",
            label: "course override",
        };
    }

    const courseConfigured = configuredThreshold(
        selectedCourseOption.value?.attendance_threshold
    );
    if (courseFilter.value !== "all" && courseConfigured !== undefined) {
        return {
            value: courseConfigured,
            source: "course_configured",
            label: "course configured",
        };
    }

    const globalValue = normalizedThreshold();
    return {
        value: globalValue ?? defaultThreshold,
        source: "global",
        label: "global",
    };
});

const thresholdLabel = computed(() => {
    return Number(effectiveThreshold.value.value).toFixed(1);
});

const thresholdScopeLabel = computed(() => effectiveThreshold.value.label);

const cooldownLabel = computed(() => {
    const value = normalizedCooldownDays();
    return String(value ?? defaultCooldownDays);
});

const thresholdValue = computed(() => Number(effectiveThreshold.value.value));
const thresholdGap = (rate) => {
    const numericRate = Number(rate ?? 0);
    return Math.max(Number((thresholdValue.value - numericRate).toFixed(2)), 0);
};
const isBelowThreshold = (rate) => Number(rate ?? 0) < thresholdValue.value;

const applyFilters = () => {
    router.get(
        route("admin.attendance.report"),
        {
            programme:
                programmeFilter.value !== "all"
                    ? programmeFilter.value
                    : undefined,
            intake_year:
                intakeYearFilter.value !== "all"
                    ? intakeYearFilter.value
                    : undefined,
            semester:
                semesterFilter.value !== "all"
                    ? semesterFilter.value
                    : undefined,
            course_id:
                courseFilter.value !== "all"
                    ? Number(courseFilter.value)
                    : undefined,
            subject_id:
                subjectFilter.value !== "all"
                    ? Number(subjectFilter.value)
                    : undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
            session_date: selectedSessionDate.value || undefined,
            threshold: normalizedThreshold(),
            course_threshold: normalizedCourseThreshold(),
            subject_threshold: normalizedSubjectThreshold(),
            cooldown_days: normalizedCooldownDays(),
            trend_mode: trendMode.value !== "weekly" ? trendMode.value : undefined,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    );
};

const clearReportFilters = () => {
    programmeFilter.value = "all";
    intakeYearFilter.value = "all";
    semesterFilter.value = "all";
    courseFilter.value = "all";
    subjectFilter.value = "all";
    dateFrom.value = "";
    dateTo.value = "";
    selectedSessionDate.value = "";
    thresholdFilter.value = defaultThreshold;
    courseThresholdFilter.value = "";
    subjectThresholdFilter.value = "";
    alertCooldownDays.value = defaultCooldownDays;
    trendMode.value = "weekly";
    applyFilters();
};

const runLowAttendanceAlerts = () => {
    sendingAlerts.value = true;
    const cooldownOverride = normalizedCooldownDays();
    const payload = {
        programme:
            programmeFilter.value !== "all"
                ? programmeFilter.value
                : undefined,
        intake_year:
            intakeYearFilter.value !== "all"
                ? intakeYearFilter.value
                : undefined,
        semester:
            semesterFilter.value !== "all"
                ? semesterFilter.value
                : undefined,
        course_id:
            courseFilter.value !== "all"
                ? Number(courseFilter.value)
                : undefined,
        subject_id:
            subjectFilter.value !== "all"
                ? Number(subjectFilter.value)
                : undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
        threshold: normalizedThreshold(),
        course_threshold: normalizedCourseThreshold(),
        subject_threshold: normalizedSubjectThreshold(),
        bypass_cooldown: cooldownOverride === undefined || cooldownOverride === defaultCooldownDays,
    };

    if (
        cooldownOverride !== undefined &&
        cooldownOverride !== defaultCooldownDays
    ) {
        payload.cooldown_days = cooldownOverride;
    }

    router.post(
        route("admin.attendance.alerts.run"),
        payload,
        {
            preserveScroll: true,
            onFinish: () => {
                sendingAlerts.value = false;
            },
        }
    );
};

const exportUrl = (format) =>
    route("admin.attendance.report.export", {
        format,
        programme:
            programmeFilter.value !== "all" ? programmeFilter.value : undefined,
        intake_year:
            intakeYearFilter.value !== "all"
                ? intakeYearFilter.value
                : undefined,
        semester:
            semesterFilter.value !== "all" ? semesterFilter.value : undefined,
        course_id:
            courseFilter.value !== "all"
                ? Number(courseFilter.value)
                : undefined,
        subject_id:
            subjectFilter.value !== "all"
                ? Number(subjectFilter.value)
                : undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    });

watch(courseFilter, () => {
    if (
        subjectFilter.value !== "all" &&
        !subjectOptions.value.some(
            (subject) => String(subject.id) === String(subjectFilter.value)
        )
    ) {
        subjectFilter.value = "all";
    }
});

watch(
    () => props.sessionDrilldown?.selected_date,
    (nextDate) => {
        selectedSessionDate.value = nextDate ?? "";
    }
);

watch([programmeFilter, intakeYearFilter, semesterFilter, courseFilter, subjectFilter], () => {
    applyFilters();
});

const trendRows = computed(() =>
    trendMode.value === "monthly"
        ? props.trend?.monthly ?? []
        : props.trend?.weekly ?? []
);

const trendChartData = computed(() => ({
    labels: trendRows.value.map((row) => row.label),
    datasets: [
        {
            label: "Attendance rate",
            data: trendRows.value.map((row) => Number(row.rate ?? 0)),
            borderColor: "#0f766e",
            backgroundColor: "rgba(15, 118, 110, 0.18)",
            fill: true,
            tension: 0.35,
            pointRadius: 3,
        },
    ],
}));

const filteredStudents = computed(() => {
    const q = searchStudents.value.trim().toLowerCase();
    if (!q) return props.lowAttendanceStudents;
    return props.lowAttendanceStudents.filter((s) => {
        const hay = `${s.student_no} ${s.full_name} ${s.email} ${s.programme ?? ""}`.toLowerCase();
        return hay.includes(q);
    });
});

const filteredCourses = computed(() => {
    const q = searchCourses.value.trim().toLowerCase();
    if (!q) return props.byCourse;
    return props.byCourse.filter((c) => {
        const hay = `${c.course_code} ${c.title}`.toLowerCase();
        return hay.includes(q);
    });
});

const filteredSubjects = computed(() => {
    const q = searchSubjects.value.trim().toLowerCase();
    if (!q) return props.bySubject;
    return props.bySubject.filter((s) => {
        const hay = `${s.subject_code} ${s.title} ${s.course_code}`.toLowerCase();
        return hay.includes(q);
    });
});

const filteredRecent = computed(() => {
    const q = searchRecent.value.trim().toLowerCase();
    const recentRows = props.recentRecords?.data ?? [];
    if (!q) return recentRows;
    return recentRows.filter((r) => {
        const hay = `${r.student_no} ${r.student_name} ${r.subject_code} ${r.course_code} ${r.date}`.toLowerCase();
        return hay.includes(q);
    });
});

const sessionDates = computed(() => props.sessionDrilldown?.sessions ?? []);
const sessionSummary = computed(() => {
    const fallback = { total: 0, present: 0, absent: 0, rate: 0 };
    return props.sessionDrilldown?.summary ?? fallback;
});
const sessionRecords = computed(() => props.sessionDrilldown?.records ?? []);
const filteredSessionRecords = computed(() => {
    const q = searchSessionRecords.value.trim().toLowerCase();
    const rows = sessionRecords.value;
    if (!q) return rows;

    return rows.filter((row) => {
        const haystack = `${row.student_no} ${row.student_name} ${row.programme} ${row.subject_code} ${row.course_code} ${row.status}`.toLowerCase();
        return haystack.includes(q);
    });
});
const absentInSession = computed(() =>
    filteredSessionRecords.value.filter((row) => row.status === "absent")
);

const selectSessionDate = (date) => {
    if (!date || selectedSessionDate.value === date) {
        return;
    }

    selectedSessionDate.value = date;
    applyFilters();
};

    return {
        searchStudents,
        searchCourses,
        searchSubjects,
        searchRecent,
        searchSessionRecords,
        programmeFilter,
        intakeYearFilter,
        semesterFilter,
        courseFilter,
        subjectFilter,
        dateFrom,
        dateTo,
        defaultThreshold,
        defaultCooldownDays,
        thresholdFilter,
        courseThresholdFilter,
        subjectThresholdFilter,
        alertCooldownDays,
        trendMode,
        sendingAlerts,
        selectedSessionDate,
        normalizedThreshold,
        normalizedCooldownDays,
        normalizedCourseThreshold,
        normalizedSubjectThreshold,
        subjectOptions,
        selectedCourseOption,
        selectedSubjectOption,
        effectiveThreshold,
        thresholdLabel,
        thresholdScopeLabel,
        cooldownLabel,
        thresholdValue,
        thresholdGap,
        isBelowThreshold,
        applyFilters,
        clearReportFilters,
        runLowAttendanceAlerts,
        exportUrl,
        trendRows,
        trendChartData,
        filteredStudents,
        filteredCourses,
        filteredSubjects,
        filteredRecent,
        sessionDates,
        sessionSummary,
        sessionRecords,
        filteredSessionRecords,
        absentInSession,
        selectSessionDate,
    };
}
