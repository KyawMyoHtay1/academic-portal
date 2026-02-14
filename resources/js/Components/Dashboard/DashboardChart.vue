<script setup>
import { computed } from "vue";
import {
    Chart as ChartJS,
    ArcElement,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Filler,
    Tooltip,
    Legend,
} from "chart.js";
import { Doughnut, Pie, Bar, Line } from "vue-chartjs";

ChartJS.register(
    ArcElement,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Filler,
    Tooltip,
    Legend
);

const props = defineProps({
    type: {
        type: String,
        default: "bar",
        validator: (v) => ["doughnut", "pie", "bar", "line"].includes(v),
    },
    chartData: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        default: "",
    },
    height: {
        type: Number,
        default: 280,
    },
    yMax: {
        type: Number,
        default: null,
    },
    variant: {
        type: String,
        default: "student",
        validator: (v) => ["student", "teacher", "staff"].includes(v),
    },
    valueFormat: {
        type: String,
        default: "number",
        validator: (v) => ["number", "percent", "currency"].includes(v),
    },
});

const formatValue = (rawValue) => {
    const value = Number(rawValue ?? 0);

    if (props.valueFormat === "percent") {
        return `${value}%`;
    }

    if (props.valueFormat === "currency") {
        return new Intl.NumberFormat("en-GB", {
            style: "currency",
            currency: "GBP",
            maximumFractionDigits: 0,
        }).format(value);
    }

    return new Intl.NumberFormat().format(value);
};

const options = computed(() => {
    const base = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom",
                labels: {
                    color: "#475569",
                },
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        const label = context.dataset?.label
                            ? `${context.dataset.label}: `
                            : "";
                        return `${label}${formatValue(context.parsed?.y ?? context.parsed)}`;
                    },
                },
            },
        },
    };

    if (props.type === "doughnut" || props.type === "pie") {
        return base;
    }

    const yScale = {
        beginAtZero: true,
        grid: {
            color: "rgba(148, 163, 184, 0.18)",
        },
        ticks: {
            color: "#64748b",
            callback: (value) => formatValue(value),
        },
    };

    if (props.yMax != null) {
        yScale.max = props.yMax;
    }

    return {
        ...base,
        scales: {
            x: {
                grid: {
                    display: false,
                },
                ticks: {
                    color: "#64748b",
                },
            },
            y: yScale,
        },
    };
});

const hasData = computed(() => {
    if (!props.chartData || !Array.isArray(props.chartData.datasets)) {
        return false;
    }

    return props.chartData.datasets.some(
        (dataset) => Array.isArray(dataset?.data) && dataset.data.length > 0
    );
});

const chartCardClass = computed(() => {
    if (props.variant === "staff") {
        return "border-emerald-200 bg-gradient-to-br from-emerald-50/60 to-white";
    }

    if (props.variant === "teacher") {
        return "border-indigo-200 bg-gradient-to-br from-indigo-50/60 to-white";
    }

    return "border-blue-200 bg-gradient-to-br from-blue-50/60 to-white";
});

const titleClass = computed(() => {
    if (props.variant === "staff") {
        return "text-emerald-700";
    }

    if (props.variant === "teacher") {
        return "text-indigo-700";
    }

    return "text-blue-700";
});
</script>

<template>
    <div class="rounded-xl border p-4 shadow-sm" :class="chartCardClass">
        <h3
            v-if="title"
            class="mb-3 text-sm font-semibold uppercase tracking-wide"
            :class="titleClass"
        >
            {{ title }}
        </h3>
        <div :style="{ height: `${height}px` }">
            <Doughnut
                v-if="hasData && type === 'doughnut'"
                :data="chartData"
                :options="options"
            />
            <Pie
                v-else-if="hasData && type === 'pie'"
                :data="chartData"
                :options="options"
            />
            <Bar
                v-else-if="hasData && type === 'bar'"
                :data="chartData"
                :options="options"
            />
            <Line
                v-else-if="hasData && type === 'line'"
                :data="chartData"
                :options="options"
            />
            <div
                v-else
                class="flex h-full items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-500"
            >
                No data available
            </div>
        </div>
    </div>
</template>
