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
    decimals: {
        type: Number,
        default: null,
    },
    interactive: {
        type: Boolean,
        default: false,
    },
});
const emit = defineEmits(["chart-click"]);

const formatValue = (rawValue) => {
    const value = Number(rawValue ?? 0);
    const fractionDigits = props.decimals ?? 0;

    if (props.valueFormat === "percent") {
        return `${value.toFixed(fractionDigits)}%`;
    }

    if (props.valueFormat === "currency") {
        return new Intl.NumberFormat("en-GB", {
            style: "currency",
            currency: "GBP",
            minimumFractionDigits: fractionDigits,
            maximumFractionDigits: fractionDigits,
        }).format(value);
    }

    return new Intl.NumberFormat(undefined, {
        minimumFractionDigits: fractionDigits,
        maximumFractionDigits: fractionDigits,
    }).format(value);
};

const handleChartClick = (_event, elements) => {
    if (!props.interactive || !Array.isArray(elements) || elements.length === 0) {
        return;
    }

    const target = elements[0];
    const datasetIndex = target?.datasetIndex ?? 0;
    const dataIndex = target?.index;

    if (dataIndex === undefined || dataIndex === null) {
        return;
    }

    const label = props.chartData?.labels?.[dataIndex] ?? null;
    const rawValue = props.chartData?.datasets?.[datasetIndex]?.data?.[dataIndex] ?? null;

    emit("chart-click", {
        datasetIndex,
        dataIndex,
        label,
        value: rawValue === null ? null : Number(rawValue),
        rawValue,
    });
};

const options = computed(() => {
    const base = {
        responsive: true,
        maintainAspectRatio: false,
        onClick: handleChartClick,
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
        return "border-emerald-200/80 bg-white shadow-md ring-1 ring-slate-900/5 hover:shadow-lg hover:ring-emerald-200/50";
    }

    if (props.variant === "teacher") {
        return "border-indigo-200/80 bg-white shadow-md ring-1 ring-slate-900/5 hover:shadow-lg hover:ring-indigo-200/50";
    }

    return "border-blue-200/80 bg-white shadow-md ring-1 ring-slate-900/5 hover:shadow-lg hover:ring-blue-200/50";
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

const iconClass = computed(() => {
    if (props.variant === "staff") return "text-emerald-600";
    if (props.variant === "teacher") return "text-indigo-600";
    return "text-blue-600";
});

const iconBgClass = computed(() => {
    if (props.variant === "staff") return "bg-emerald-100";
    if (props.variant === "teacher") return "bg-indigo-100";
    return "bg-blue-100";
});

const accentDotClass = computed(() => {
    if (props.variant === "staff") return "bg-emerald-400";
    if (props.variant === "teacher") return "bg-indigo-400";
    return "bg-blue-400";
});
</script>

<template>
    <div
        class="relative rounded-2xl border p-5 transition-all duration-300"
        :class="[chartCardClass, interactive ? 'cursor-pointer' : '']"
    >
        <span
            class="absolute right-4 top-4 h-2 w-2 rounded-full"
            :class="accentDotClass"
            aria-hidden="true"
        />
        <div
            v-if="title"
            class="mb-4 flex items-center gap-2.5"
        >
            <span
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                :class="[iconBgClass, iconClass]"
                aria-hidden="true"
            >
                <svg
                    v-if="type === 'doughnut' || type === 'pie'"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"
                    />
                </svg>
                <svg
                    v-else-if="type === 'bar'"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                    />
                </svg>
                <svg
                    v-else-if="type === 'line'"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                    />
                </svg>
            </span>
            <h3
                class="text-sm font-semibold uppercase tracking-[0.08em]"
                :class="titleClass"
            >
                {{ title }}
            </h3>
        </div>
        <div :style="{ height: `${height}px` }" class="min-h-[200px]">
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
                class="flex h-full min-h-[180px] flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/80 px-4 py-6"
            >
                <svg
                    class="h-10 w-10 text-slate-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                    />
                </svg>
                <p class="text-center text-sm font-medium text-slate-500">
                    No data available
                </p>
                <p class="text-center text-xs text-slate-400">
                    Data will appear when available
                </p>
            </div>
        </div>
    </div>
</template>
