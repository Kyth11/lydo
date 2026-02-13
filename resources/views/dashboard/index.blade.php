@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-desc', 'Overview of youth profiling statistics')

@section('content')

<div class="dashboard-grid">

    <!-- ========================= -->
    <!-- LEFT CARD -->
    <!-- ========================= -->
    <div class="card equal-card">

        <h3 class="card-title">Youth Summary</h3>

        <div class="card-content">

            <div class="stat-container">

                <div class="stat-box bg-indigo-50">
                    <div class="stat-text">Total Youth</div>
                    <div class="stat-count text-indigo-600">{{ $total }}</div>
                </div>

                <div class="stat-box bg-blue-50">
                    <div class="stat-text">Male</div>
                    <div class="stat-count text-blue-600">{{ $male }}</div>
                </div>

                <div class="stat-box bg-pink-50">
                    <div class="stat-text">Female</div>
                    <div class="stat-count text-pink-600">{{ $female }}</div>
                </div>

            </div>

            <h3 class="card-title mt-6 mb-0">
                Youth Distribution per Barangay
            </h3>
            <em class="text-xs text-center text-gray-500">
                Click the bars below to hide gender or total count
            </em>

            <div class="chart-area">
                <canvas id="barangayChart"></canvas>
            </div>

        </div>

    </div>


<!-- ========================= -->
<!-- RIGHT CARD -->
<!-- ========================= -->
<div class="card equal-card">

    <h3 class="card-title">
        Gender % per Barangay
    </h3>

    <!-- ALL BARANGAY SUMMARY -->
    <div class="barangay-row border-bottom-strong">

        <div class="barangay-name font-bold text-indigo-600">
            All Barangay
        </div>

        <div class="chart-wrapper">
            <canvas id="pieChartAll"></canvas>
        </div>

    </div>

    <!-- SCROLLABLE CONTENT -->
    <div class="card-content scroll-area">

        @foreach($barangayGenderData as $data)

            <div class="barangay-row">

                <div class="barangay-name">
                    {{ $data['barangay'] }}
                </div>

                <div class="chart-wrapper">
                    <canvas id="pieChart{{ $loop->index }}"></canvas>
                </div>

            </div>

        @endforeach

    </div>

</div>


</div>



<!-- ========================= -->
<!-- STYLES -->
<!-- ========================= -->
<style>

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    align-items: stretch; /* makes cards same height */
}

/* CARD */
.card {
    background: white;
    border-radius: 1.25rem;
    padding: 1.75rem;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    display: flex;
    flex-direction: column;
}

.equal-card {
    height: 520px; /* SAME HEIGHT FOR BOTH CARDS */
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 1.2rem;
}

.card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* SUMMARY */
.stat-container {
    display: flex;
    justify-content: center;
    gap: 2.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.stat-box {
    width: 180px;
    height: 90px;
    border-radius: 1.25rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: .2s ease;
}

.stat-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 18px rgba(0, 0, 0, 0.12);
}

.stat-text {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #6b7280;
    font-weight: 700;
    margin-bottom: .3rem;
}

.stat-count {
    font-size: 2rem;
    font-weight: 800;
}

/* BAR CHART AREA */
.chart-area {
    flex: 1;
    position: relative;
}

/* SCROLLABLE RIGHT CARD */
.scroll-area {
    overflow-y: auto;
}

.scroll-area::-webkit-scrollbar {
    width: 6px;
}

.scroll-area::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 6px;
}

.barangay-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f1f1;
}

.barangay-name {
    font-weight: 600;
    color: #4b5563;
}

.chart-wrapper {
    width: 100px;
    height: 100px;
}
.border-bottom-strong {
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
}

</style>



<!-- ========================= -->
<!-- CHARTS -->
<!-- ========================= -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('barangayChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($barangayGenderData->pluck('barangay')) !!},
        datasets: [
            {
                label: 'Male',
                data: {!! json_encode($barangayGenderData->pluck('male')->map(fn($v)=>(int)$v)) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 6
            },
            {
                label: 'Female',
                data: {!! json_encode($barangayGenderData->pluck('female')->map(fn($v)=>(int)$v)) !!},
                backgroundColor: '#ec4899',
                borderRadius: 6
            },
            {
                label: 'Total',
                data: {!! json_encode(
                    $barangayGenderData->map(fn($v)=>(int)$v['male'] + (int)$v['female'])
                ) !!},
                backgroundColor: '#6366f1',
                borderRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    stepSize: 1
                }
            }
        }
    }
});
</script>


{{-- to get rid if the percentage in the bar chart --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    /* =========================
       CENTER TEXT PLUGIN
    ========================= */
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw(chart) {
            const { width, height, ctx } = chart;
            const dataset = chart.data.datasets[0].data;

            const total = dataset.reduce((a, b) => a + b, 0);

            if (!total) return;

            const malePercent = Math.round((dataset[0] / total) * 100);
            const femalePercent = Math.round((dataset[1] / total) * 100);

            ctx.restore();
            ctx.font = "bold 8px sans-serif";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";

            ctx.fillStyle = "#374151";
            ctx.fillText(
                malePercent + "% M / " + femalePercent + "% F",
                width / 2,
                height / 2
            );

            ctx.save();
        }
    };

    Chart.register(centerTextPlugin);



    /* =========================
       ALL BARANGAY PIE
    ========================= */
    new Chart(document.getElementById('pieChartAll'), {
        type: 'doughnut',
        data: {
            labels: ['Male', 'Female'],
            datasets: [{
                data: [{{ (int)$male }}, {{ (int)$female }}],
                backgroundColor: ['#3b82f6', '#ec4899']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false }
            }
        }
    });


    /* =========================
       PIE CHARTS PER BARANGAY
    ========================= */
    @foreach($barangayGenderData as $data)

        new Chart(document.getElementById('pieChart{{ $loop->index }}'), {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [{{ (int)$data['male'] }}, {{ (int)$data['female'] }}],
                    backgroundColor: ['#3b82f6', '#ec4899']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false }
                }
            }
        });

    @endforeach

</script>


@endsection
