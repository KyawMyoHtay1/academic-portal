<script setup>
import { Link } from "@inertiajs/vue3";
import DashboardChart from "@/Components/Dashboard/DashboardChart.vue";

defineProps({
    role: {
        type: String,
        required: true,
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
    unread: {
        type: Object,
        default: () => ({
            messages: 0,
            notifications: 0,
            announcements: 0,
        }),
    },
    quickActions: {
        type: Array,
        default: () => [],
    },
    alertSystemStatus: {
        type: Object,
        default: null,
    },
    formatCurrency: {
        type: Function,
        required: true,
    },
    formatTrendPercent: {
        type: Function,
        required: true,
    },
    trendBadgeClass: {
        type: Function,
        required: true,
    },
    hasChartData: {
        type: Function,
        required: true,
    },
    onStudentFeeStatusClick: {
        type: Function,
        default: () => {},
    },
    onStaffFeeStatusClick: {
        type: Function,
        default: () => {},
    },
    onStaffEnrollmentStatusClick: {
        type: Function,
        default: () => {},
    },
    onTeacherGradeStatusClick: {
        type: Function,
        default: () => {},
    },
    onTeacherAssignmentsChartClick: {
        type: Function,
        default: () => {},
    },
});
</script>

<template>
    <div class="space-y-6">
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
</template>
