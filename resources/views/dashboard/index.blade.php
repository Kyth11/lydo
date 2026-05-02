@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-desc', 'Overview of youth profiling statistics')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-index.css') }}">
@endpush

@section('content')

    @php

        $userBarangay = auth()->user()->barangay ?? null;
        $isSK = $userBarangay !== null;

        /*
        |--------------------------------------------------------------------------
        | ANNOUNCEMENTS
        |--------------------------------------------------------------------------
        */

        $announcements = \App\Models\Announcement::when($isSK, function ($q) use ($userBarangay) {
            $q->where(function ($query) use ($userBarangay) {
                $query->whereNull('barangay')->orWhere('barangay', 'All Barangay')->orWhere('barangay', $userBarangay);
            });
        })
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | BARANGAY DATA FILTER
        |--------------------------------------------------------------------------
        */

        $filteredBarangayData = $isSK
            ? $barangayGenderData->where('barangay', $userBarangay)->values()
            : $barangayGenderData;

    @endphp


    {{-- =========================
    ANNOUNCEMENT STRIP
    ========================= --}}

    @if ($announcements->count())

        <div class="announcement-strip" id="announcementStrip">

            <div class="announcement-slider" id="announcementSlider">

                @foreach ($announcements as $a)
                    <div class="announcement-slide" data-start="{{ $a->start_date }}" data-end="{{ $a->end_date }}">

                        <div class="announcement-content">

                            <span class="announcement-icon">📢</span>

                            <div>

                                <strong>{{ $a->title }}</strong>

                                <div class="announcement-date">
                                    {{ \Carbon\Carbon::parse($a->start_date)->format('M d, Y') }}

                                    @if ($a->end_date)
                                        - {{ \Carbon\Carbon::parse($a->end_date)->format('M d, Y') }}
                                    @endif
                                </div>

                                <span class="announcement-text">
                                    {{ $a->description }}
                                </span>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

            <div class="announcement-dots" id="announcementDots"></div>

        </div>

    @endif


    <div class="dashboard-grid">

        <!-- ========================= -->
        <!-- LEFT COLUMN -->
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


                {{-- AGE GROUPS (VISIBLE FOR ADMIN ONLY) --}}


                <div class="event-divider"></div>

                <div class="age-group-section">

                    <h3 class="card-title mt-6">Youth Age Groups</h3>

                    <p class="text-xs text-gray-500 text-center mb-3">
                        Distribution of youth based on SK age brackets
                    </p>

                    <div class="age-group-grid">

                        <div class="stat-box bg-green-50">
                            <div class="stat-text" style="margin-top: 5px !important;">Child Youth</div>
                            <div class="stat-text">Age 15–17</div>
                            <div class="stat-count text-green-600">{{ $ageGroups['15-17'] ?? 0 }}</div>
                        </div>

                        <div class="stat-box bg-blue-50">
                            <div class="stat-text" style="margin-top: 5px !important;">Core Youth</div>
                            <div class="stat-text">Age 18–24</div>
                            <div class="stat-count text-blue-600">{{ $ageGroups['18-24'] ?? 0 }}</div>
                        </div>

                        <div class="stat-box bg-orange-50">
                            <div class="stat-text" style="margin-top: 5px !important;">Young Adult</div>
                            <div class="stat-text">Age 25–30</div>
                            <div class="stat-count text-orange-600">{{ $ageGroups['25-30'] ?? 0 }}</div>
                        </div>

                    </div>

                    <div class="chart-area-sm mt-3">
                        <canvas id="ageGroupChart"></canvas>
                    </div>

                </div>




                <div class="event-divider"></div>

                <h3 class="card-title mt-6 mb-0">
                    YOUTH DISTRIBUTION PER BARANGAY
                </h3>

                <em class="text-xs text-center text-gray-500">
                    Click the bars below to hide gender or total count
                </em>

                <div class="chart-area">
                    <canvas id="barangayChart"></canvas>
                </div>

            </div>



            <div class="event-divider"></div>

            <h3 class="card-title mt-6">
                Municipality Profiling Coverage
            </h3>

            <div class="chart-area">
                <canvas id="coverageChart"></canvas>
            </div>

            <div class="coverage-summary">

                <div class="coverage-box">
                    <div class="coverage-label">Total Youth Population</div>
                    <div class="coverage-value">{{ $totalPopulation }}</div>
                </div>

                <div class="coverage-box">
                    <div class="coverage-label">Profiles Added</div>
                    <div class="coverage-value">{{ $totalProfiles }}</div>
                </div>

                <div class="coverage-box">
                    <div class="coverage-label">Coverage</div>
                    <div class="coverage-value">{{ $coveragePercent }}%</div>
                </div>

            </div>

        </div>


        <!-- ========================= -->
        <!-- RIGHT COLUMN -->
        <!-- ========================= -->

        <div class="card equal-card">

            <h3 class="card-title">
                GENDER % PER BARANGAY
            </h3>

            @if(!$isSK)
                <div class="barangay-row border-bottom-strong">

                    <div class="barangay-name font-bold text-indigo-600">
                        ALL BARANGAY
                    </div>

                    <div class="chart-wrapper">
                        <canvas id="pieChartAll"></canvas>
                    </div>

                </div>
            @endif


            <div class="card-content scroll-area">

                @foreach ($filteredBarangayData as $data)
                    <div class="barangay-row">

                        <div class="barangay-name">
                            {{ strtoupper($data['barangay']) }}
                        </div>

                        <div class="chart-wrapper">
                            <canvas id="pieChart{{ $loop->index }}"></canvas>
                        </div>

                    </div>
                @endforeach

            </div>


            <div class="event-divider"></div>

            <h3 class="card-title">
                BARANGAY PROFILING COVERAGE
            </h3>

            <div class="chart-area-lg">
                <canvas id="barangayCoverageChart"></canvas>
            </div>

        </div>

    </div>

    @push('scripts')
    <script src="{{ asset('js/chart.js') }}"></script>
    <script>
        window.dashboardData = {
            barangayLabels: @json($filteredBarangayData->pluck('barangay')->map(fn($v) => strtoupper($v))),
            maleData: @json($filteredBarangayData->pluck('male')->map(fn($v) => (int) $v)),
            femaleData: @json($filteredBarangayData->pluck('female')->map(fn($v) => (int) $v)),
            totalData: @json($filteredBarangayData->map(fn($v) => (int) $v['male'] + (int) $v['female'])),
            maleTotal: {{ (int) $male }},
            femaleTotal: {{ (int) $female }},
            totalYouth: {{ (int) $total }}
        };
    </script>
    <script src="{{ asset('js/dashboard-index.js') }}"></script>
    @endpush
@endsection
