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
    /** Optional max for Y axis (e.g. 100 for percentage) */
    yMax: {
        type: Number,
        default: null,
    },
});

const options = computed(() => {
    const base = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom",
            },
        },
    };
    if (props.type === "doughnut" || props.type === "pie") {
        return { ...base };
    }
    if (props.type === "line") {
        const yScale = { beginAtZero: true };
        if (props.yMax != null) yScale.max = props.yMax;
        return {
            ...base,
            scales: {
                y: yScale,
            },
        };
    }
    return {
        ...base,
        scales: {
            y: {
                beginAtZero: true,
            },
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

</script>

<template>
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <h3
            v-if="title"
            class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-600"
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
