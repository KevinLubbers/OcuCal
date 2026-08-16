<?php

use Livewire\Volt\Component;

new class extends Component {

}; ?>

<div>
    <div class="ml-4 mt-4">
    <div class="flex flex-row">
        <div class="flex flex-col mr-4">
            <x-label for="range" value="{{ __('Select a Year') }}" />
            <select wire:model.live="range" class="mt-1 block mb-2 rounded-md text-gray-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                @foreach(range(now()->year - 5, now()->year + 5) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
                <option value="all">All Time</option>
            </select>
        </div>
        <div class="flex flex-col ml-4">
            <x-label for="selectedQuery" value="{{ __('Select a Query') }}" />
            <select wire:model.live="selectedQuery" class="mt-1 block mb-2 rounded-md text-gray-600 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                <option value="">(Count) Tickets Resolved per Specialist</option>
                <option value="">(Count) Tickets Submitted per Division</option>
                <option value="">(Count) Tickets Submitted per ISR</option>
                <option value="">(Average) Response Time per Specialist</option>
            </select>
        </div>
    </div>


    <div class="relative">
        <div wire:loading.flex
            wire:target="range, selectedQuery"
            class="absolute inset-0 z-10 items-center justify-center bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm">
            
            <div class="text-gray-900 dark:text-white justify-center items-center flex flex-col">
                Loading chart...
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50">
                    <circle cx="25" cy="25" r="20" stroke="#ddd" stroke-width="5" fill="none" />
                    <circle cx="25" cy="25" r="20" stroke="#2d7356" stroke-width="5" stroke-linecap="round" fill="none" stroke-dasharray="126" stroke-dashoffset="30">
                    <animate attributeName="stroke-dashoffset" from="126" to="0" dur="1.5s" repeatCount="indefinite" />
                    </circle>
                </svg>
            </div>
        </div>
    <!-- chart starts here -->
        <div wire:ignore
        x-data="chartComponent()"
        x-init="requestAnimationFrame(() => initChart())"
        x-on:chart-data-updated.window="updateChart($event.detail.data)"
        x-on:update-chart-meta.window="updateMeta($event.detail.label, $event.detail.title)"
        class="min-h-[400px] h-96">
            <canvas id="analyticsChart" class="w-full h-full"></canvas>
        </div>
    </div>
</div>
<!-- load chart.js via cdn and chart js logic here -->
@push('scripts')
    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
    @endonce

    <script>
    function chartComponent() {
        return {
            chart: null,
            chartLabel: '',
            chartTitle: '',
            getChartColors() {
                const isDark = document.documentElement.classList.contains('dark');

                return {
                    text: isDark ? '#f9fafb' : '#1f2937',
                    grid: isDark ? '#374151' : '#e5e7eb',
                    legend: isDark ? '#f9fafb' : '#1f2937',
                    title: isDark ? '#f9fafb' : '#111827',
                    tooltipBG: isDark ? '#374151' : '#f9fafb',
                };
            },

            getChartConfig(data = { labels: [], values: [] }) {
                const colors = this.getChartColors();

                return {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: this.chartLabel,
                            data: data.values,
                            backgroundColor: '#158e87ff',
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                labels: { color: colors.legend }
                            },
                            title: {
                                display: true,
                                text: this.chartTitle,
                                color: colors.title
                            },
                            tooltip: {
                                backgroundColor: colors.tooltipBG,
                                titleColor: colors.text,
                                bodyColor: colors.text
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: colors.text },
                                grid: { color: colors.grid }
                            },
                            y: {
                                ticks: { color: colors.text },
                                grid: { color: colors.grid }
                            }
                        }
                    }
                };
            },

            updateMeta(label, title) {
                this.chartLabel = label;
                this.chartTitle = title;

                this.rebuildChart();
            },
            initChart() {
                const ctx = document.getElementById('analyticsChart');

                this.chart = new Chart(ctx, this.getChartConfig());

                const observer = new MutationObserver(() => {
                    this.rebuildChart();
                });

                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            },

            rebuildChart() {
                if (!this.chart) return;

                const ctx = document.getElementById('analyticsChart');

                const data = {
                    labels: this.chart.data.labels,
                    values: this.chart.data.datasets[0].data
                };

                this.chart.destroy();

                this.chart = new Chart(ctx, this.getChartConfig(data));
            },

            updateChart(data) {
                if (!this.chart) return;

                this.chart.data.labels = Object.keys(data);
                this.chart.data.datasets[0].data = Object.values(data);

                this.chart.update();
            }
        }
    }
    </script>
    @endpush

</div>
