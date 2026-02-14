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
                v-if="type === 'doughnut'"
                :data="chartData"
                :options="options"
            />
            <Pie
                v-else-if="type === 'pie'"
                :data="chartData"
                :options="options"
            />
            <Bar v-else-if="type === 'bar'" :data="chartData" :options="options" />
            <Line v-else-if="type === 'line'" :data="chartData" :options="options" />
        </div>
    </div>
</template>
