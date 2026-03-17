<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeroBanner from "@/Components/Dashboard/HeroBanner.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import DashboardChart from "@/Components/Dashboard/DashboardChart.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    role: {
        type: String,
        default: "student",
    },
    stats: {
        type: Object,
        required: true,
    },
    charts: {
        type: Object,
        default: () => ({}),
    },
    insights: {
        type: Object,
        default: () => ({}),
    },
    alertSystemStatus: {
        type: Object,
        default: null,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const insights = computed(() => props.insights ?? {});
const unread = computed(
    () =>
        page.props.unread ?? {
            messages: 0,
            notifications: 0,
            announcements: 0,
        }
);
const userRoleLabel = computed(() => {
    if (!user.value?.role) {
        return "";
    }
    return user.value.role.charAt(0).toUpperCase() + user.value.role.slice(1);
});

const formatNumber = (value) => new Intl.NumberFormat().format(value ?? 0);
const formatCurrency = (value) =>
    new Intl.NumberFormat("en-GB", {
        style: "currency",
        currency: "GBP",
        maximumFractionDigits: 0,
    }).format(value ?? 0);
const hasChartData = (chart) => {
    if (!chart || !Array.isArray(chart.datasets)) {
        return false;
    }

    return chart.datasets.some(
        (dataset) => Array.isArray(dataset?.data) && dataset.data.length > 0
    );
};

const feeStatusByChartIndex = ["pending", "payment_pending", "failed", "paid"];
const enrollmentStatusByChartIndex = [
    "pending",
    "approved",
    "rejected",
    "withdrawal_pending",
];
const formatTrendPercent = (value) => {
    const numeric = Number(value ?? 0);
    if (!Number.isFinite(numeric)) {
        return "0%";
    }
    return `${numeric > 0 ? "+" : ""}${numeric.toFixed(1)}%`;
};
const trendBadgeClass = (direction, variant = "staff") => {
    if (direction === "up") {
        if (variant === "staff") {
            return "bg-emerald-100 text-emerald-700";
        }
        if (variant === "student") {
            return "bg-blue-100 text-blue-700";
        }
        return "bg-indigo-100 text-indigo-700";
    }
    if (direction === "down") {
        return "bg-red-100 text-red-700";
    }
    return "bg-slate-100 text-slate-700";
};
const onStaffFeeStatusClick = (payload) => {
    if (props.role !== "staff") {
        return;
    }

    const status = feeStatusByChartIndex[payload?.dataIndex ?? -1];
    if (!status) {
        return;
    }

    router.get(route("admin.fees.index"), { status });
};
const onStaffEnrollmentStatusClick = (payload) => {
    if (props.role !== "staff") {
        return;
    }

    const status = enrollmentStatusByChartIndex[payload?.dataIndex ?? -1];
    if (!status) {
        return;
    }

    router.get(route("admin.enrollments.index"), { status });
};
const onTeacherGradeStatusClick = () => {
    if (props.role !== "teacher") {
        return;
    }

    router.get(route("teacher.grades.index"));
};
const onTeacherAssignmentsChartClick = () => {
    if (props.role !== "teacher") {
        return;
    }

    router.get(route("teacher.assignments.index"));
};
const onStudentFeeStatusClick = () => {
    if (props.role !== "student") {
        return;
    }

    router.get(route("student.fees.index"));
};

const cards = computed(() => {
    const list = [];
    const s = props.stats || {};

    // Staff cards
    if (props.role === "staff") {
        list.push(
            {
                title: "Students",
                value: formatNumber(s.students),
                helper: "Total registered students",
            },
            {
                title: "Courses",
                value: formatNumber(s.courses),
                helper: "Active course offerings",
            },
            {
                title: "Fees",
                value: formatCurrency(s.feeTotal),
                helper: "Fees recorded in the system",
            },
            {
                title: "Attendance",
                value: `${s.attendanceRate ?? 0}%`,
                helper: "Overall attendance",
            }
        );
    }

    // Teacher cards
    if (props.role === "teacher") {
        list.push(
            {
                title: "My Subjects",
                value: formatNumber(s.teachingSubjects),
                helper: "Subjects you are teaching",
            },
            {
                title: "Students Taught",
                value: formatNumber(s.studentsTaught),
                helper: "Unique students across your courses",
            },
            {
                title: "Grades Recorded",
                value: formatNumber(s.gradesRecorded),
                helper: "Grades entered for your courses",
            },
            {
                title: "Attendance",
                value: `${s.attendanceRate ?? 0}%`,
                helper: "Attendance across your courses",
            }
        );
    }

    // Student cards
    if (props.role === "student") {
        list.push(
            {
                title: "My Courses",
                value: formatNumber(s.myCourses),
                helper: "Courses you are enrolled in",
            },
            {
                title: "Outstanding Fees",
                value: formatCurrency(s.outstandingFees),
                helper: "Unpaid fees for your account",
            },
            {
                title: "Grades",
                value: formatNumber(s.myGrades),
                helper: "Grades available for you",
            },
            {
                title: "Attendance",
                value: `${s.attendanceRate ?? 0}%`,
                helper: "Your attendance rate",
            }
        );
    }

    return list;
});

const quickActions = computed(() => {
    const role = props.role;
    if (role === "staff") {
        return [
            {
                label: "Enrollment Requests",
                href: route("admin.enrollments.index"),
            },
            { label: "Manage Courses", href: route("admin.courses.index") },
            { label: "Manage Subjects", href: route("admin.subjects.index") },
            { label: "Fee Management", href: route("admin.fees.index") },
            {
                label: "Timetable Management",
                href: route("admin.timetables.index"),
            },
            { label: "User Management", href: route("admin.users.index") },
            {
                label: "Announcements",
                href: route("admin.announcements.index"),
            },
            {
                label: "Failed Jobs",
                href: route("admin.failed-jobs.index"),
            },
        ];
    }
    if (role === "teacher") {
        return [
            { label: "My Timetable", href: route("teacher.timetable.index") },
            {
                label: "My Teaching Subjects",
                href: route("teacher.courses.index"),
            },
            {
                label: "Mark Attendance",
                href: route("teacher.attendance.index"),
            },
            { label: "Grades", href: route("teacher.grades.index") },
            { label: "Announcements", href: route("announcements.index") },
            { label: "Messages", href: route("messages.index") },
        ];
    }
    // student
    return [
        { label: "My Courses", href: route("my-courses.index") },
        { label: "Courses Catalog", href: route("courses.index") },
        { label: "Grades", href: route("student.grades.index") },
        { label: "Notifications", href: route("notifications.index") },
        { label: "Timetable", href: route("student.timetable.index") },
        { label: "Fees", href: route("student.fees.index") },
        { label: "My Profile", href: route("student.profile.show") },
        { label: "Announcements", href: route("announcements.index") },
        { label: "Messages", href: route("messages.index") },
    ];
});
import StudentDashboardSection from "@/Components/Dashboard/StudentDashboardSection.vue";
import StaffDashboardSection from "@/Components/Dashboard/StaffDashboardSection.vue";
import TeacherDashboardSection from "@/Components/Dashboard/TeacherDashboardSection.vue";

import { computed } from "vue";

const props = defineProps({
    role: {
        type: String,
        default: "student",
    },
    stats: {
        type: Object,
        required: true,
    },
    charts: {
        type: Object,
        default: () => ({}),
    },
    insights: {
        type: Object,
        default: () => ({}),
    },
    alertSystemStatus: {
        type: Object,
        default: null,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const insights = computed(() => props.insights ?? {});
const unread = computed(
    () =>
        page.props.unread ?? {
            messages: 0,
            notifications: 0,
            announcements: 0,
        }
);
const userRoleLabel = computed(() => {
    if (!user.value?.role) {
        return "";
    }
    return user.value.role.charAt(0).toUpperCase() + user.value.role.slice(1);
});

const formatNumber = (value) => new Intl.NumberFormat().format(value ?? 0);
const formatCurrency = (value) =>
    new Intl.NumberFormat("en-GB", {
        style: "currency",
        currency: "GBP",
        maximumFractionDigits: 0,
    }).format(value ?? 0);
const hasChartData = (chart) => {
    if (!chart || !Array.isArray(chart.datasets)) {
        return false;
    }

    return chart.datasets.some(
        (dataset) => Array.isArray(dataset?.data) && dataset.data.length > 0
    );
};

const feeStatusByChartIndex = ["pending", "payment_pending", "failed", "paid"];
const enrollmentStatusByChartIndex = [
    "pending",
    "approved",
    "rejected",
    "withdrawal_pending",
];
const formatTrendPercent = (value) => {
    const numeric = Number(value ?? 0);
    if (!Number.isFinite(numeric)) {
        return "0%";
    }
    return `${numeric > 0 ? "+" : ""}${numeric.toFixed(1)}%`;
};
const trendBadgeClass = (direction, variant = "staff") => {
    if (direction === "up") {
        if (variant === "staff") {
            return "bg-emerald-100 text-emerald-700";
        }
        if (variant === "student") {
            return "bg-blue-100 text-blue-700";
        }
        return "bg-indigo-100 text-indigo-700";
    }
    if (direction === "down") {
        return "bg-red-100 text-red-700";
    }
    return "bg-slate-100 text-slate-700";
};
const onStaffFeeStatusClick = (payload) => {
    if (props.role !== "staff") {
        return;
    }

    const status = feeStatusByChartIndex[payload?.dataIndex ?? -1];
    if (!status) {
        return;
    }

    router.get(route("admin.fees.index"), { status });
};
const onStaffEnrollmentStatusClick = (payload) => {
    if (props.role !== "staff") {
        return;
    }

    const status = enrollmentStatusByChartIndex[payload?.dataIndex ?? -1];
    if (!status) {
        return;
    }

    router.get(route("admin.enrollments.index"), { status });
};
const onTeacherGradeStatusClick = () => {
    if (props.role !== "teacher") {
        return;
    }

    router.get(route("teacher.grades.index"));
};
const onTeacherAssignmentsChartClick = () => {
    if (props.role !== "teacher") {
        return;
    }

    router.get(route("teacher.assignments.index"));
};
const onStudentFeeStatusClick = () => {
    if (props.role !== "student") {
        return;
    }

    router.get(route("student.fees.index"));
};

const cards = computed(() => {
    const list = [];
    const s = props.stats || {};

    // Staff cards
    if (props.role === "staff") {
        list.push(
            {
                title: "Students",
                value: formatNumber(s.students),
                helper: "Total registered students",
            },
            {
                title: "Courses",
                value: formatNumber(s.courses),
                helper: "Active course offerings",
            },
            {
                title: "Fees",
                value: formatCurrency(s.feeTotal),
                helper: "Fees recorded in the system",
            },
            {
                title: "Attendance",
                value: `${s.attendanceRate ?? 0}%`,
                helper: "Overall attendance",
            }
        );
    }

    // Teacher cards
    if (props.role === "teacher") {
        list.push(
            {
                title: "My Subjects",
                value: formatNumber(s.teachingSubjects),
                helper: "Subjects you are teaching",
            },
            {
                title: "Students Taught",
                value: formatNumber(s.studentsTaught),
                helper: "Unique students across your courses",
            },
            {
                title: "Grades Recorded",
                value: formatNumber(s.gradesRecorded),
                helper: "Grades entered for your courses",
            },
            {
                title: "Attendance",
                value: `${s.attendanceRate ?? 0}%`,
                helper: "Attendance across your courses",
            }
        );
    }

    // Student cards
    if (props.role === "student") {
        list.push(
            {
                title: "My Courses",
                value: formatNumber(s.myCourses),
                helper: "Courses you are enrolled in",
            },
            {
                title: "Outstanding Fees",
                value: formatCurrency(s.outstandingFees),
                helper: "Unpaid fees for your account",
            },
            {
                title: "Grades",
                value: formatNumber(s.myGrades),
                helper: "Grades available for you",
            },
            {
                title: "Attendance",
                value: `${s.attendanceRate ?? 0}%`,
                helper: "Your attendance rate",
            }
        );
    }

    return list;
});

const quickActions = computed(() => {
    const role = props.role;
    if (role === "staff") {
        return [
            {
                label: "Enrollment Requests",
                href: route("admin.enrollments.index"),
            },
            { label: "Manage Courses", href: route("admin.courses.index") },
            { label: "Manage Subjects", href: route("admin.subjects.index") },
            { label: "Fee Management", href: route("admin.fees.index") },
            {
                label: "Timetable Management",
                href: route("admin.timetables.index"),
            },
            { label: "User Management", href: route("admin.users.index") },
            {
                label: "Announcements",
                href: route("admin.announcements.index"),
            },
            {
                label: "Failed Jobs",
                href: route("admin.failed-jobs.index"),
            },
        ];
    }
    if (role === "teacher") {
        return [
            { label: "My Timetable", href: route("teacher.timetable.index") },
            {
                label: "My Teaching Subjects",
                href: route("teacher.courses.index"),
            },
            {
                label: "Mark Attendance",
                href: route("teacher.attendance.index"),
            },
            { label: "Grades", href: route("teacher.grades.index") },
            { label: "Announcements", href: route("announcements.index") },
            { label: "Messages", href: route("messages.index") },
        ];
    }
    // student
    return [
        { label: "My Courses", href: route("my-courses.index") },
        { label: "Courses Catalog", href: route("courses.index") },
        { label: "Grades", href: route("student.grades.index") },
        { label: "Notifications", href: route("notifications.index") },
        { label: "Timetable", href: route("student.timetable.index") },
        { label: "Fees", href: route("student.fees.index") },
        { label: "My Profile", href: route("student.profile.show") },
        { label: "Announcements", href: route("announcements.index") },
        { label: "Messages", href: route("messages.index") },
    ];
});
            <StudentDashboardSection
                v-if="role === ""student"""
                :role="role"
                :stats="stats"
                :charts="charts"
                :insights="insights"
                :unread="unread"
                :quick-actions="quickActions"
                :format-currency="formatCurrency"
                :format-trend-percent="formatTrendPercent"
                :trend-badge-class="trendBadgeClass"
                :has-chart-data="hasChartData"
                :on-student-fee-status-click="onStudentFeeStatusClick"
            />

            <StaffDashboardSection
                v-else-if="role === ""staff"""
                :role="role"
                :stats="stats"
                :charts="charts"
                :insights="insights"
                :unread="unread"
                :quick-actions="quickActions"
                :alert-system-status="alertSystemStatus"
                :format-currency="formatCurrency"
                :format-trend-percent="formatTrendPercent"
                :trend-badge-class="trendBadgeClass"
                :has-chart-data="hasChartData"
                :on-staff-fee-status-click="onStaffFeeStatusClick"
                :on-staff-enrollment-status-click="onStaffEnrollmentStatusClick"
            />

            <TeacherDashboardSection
                v-else
                :role="role"
                :stats="stats"
                :charts="charts"
                :insights="insights"
                :unread="unread"
                :quick-actions="quickActions"
                :format-currency="formatCurrency"
                :format-trend-percent="formatTrendPercent"
                :trend-badge-class="trendBadgeClass"
                :has-chart-data="hasChartData"
                :on-teacher-grade-status-click="onTeacherGradeStatusClick"
                :on-teacher-assignments-chart-click="onTeacherAssignmentsChartClick"
            />
        </div>
    </AuthenticatedLayout>
</template>
