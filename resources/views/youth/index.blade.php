@extends('layouts.app')

@section('page-title',
    request('archived')
    ? 'Archived Youth Profiles'
    : (request('transferred')
    ? 'Transferred Youth
    Profiles'
    : 'Active Youth Profiles'))
@section('page-desc', 'Manage and view registered youth profiles')

@section('content')

    @php
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
        $isSK = $user && $user->role === 'sk';
        $protectionEnabled = $isSK && \App\Models\User::where('role', 'admin')->value('action_protection');
    @endphp

    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
    <div class="max-w-6xl mx-auto px-4 space-y-6">


        <div class="bg-white rounded-xl shadow p-6 relative">

            <!-- FILTER BAR -->
            <div class="save-bar mb-6">
                <div class="flex justify-between w-full flex-wrap gap-4">

                    <form method="GET" action="{{ url('/youth') }}" class="form-row">
                        <input type="hidden" name="archived" value="{{ request('archived') }}">

                        <select name="barangay" class="form-input" onchange="this.form.submit()"
                            {{ $isSK ? 'disabled' : '' }}>

                            @if (!$isSK)
                                <option value="">All Barangay</option>
                            @endif

                            @foreach (['Awang', 'Bagocboc', 'Barra', 'Bonbon', 'Cauyunan', 'Igpit', 'Limunda', 'Luyong Bonbon', 'Malanang', 'Nangcaon', 'Patag', 'Poblacion', 'Taboc', 'Tingalan'] as $b)
                                @if (!$isSK || $user->barangay === $b)
                                    <option value="{{ $b }}"
                                        {{ request('barangay', $isSK ? $user->barangay : '') === $b ? 'selected' : '' }}>
                                        {{ $b }}
                                    </option>
                                @endif
                            @endforeach
                        </select>

                        @if ($isSK)
                            <input type="hidden" name="barangay" value="{{ $user->barangay }}">
                        @endif

                        <select name="sex" class="form-input" onchange="this.form.submit()">
                            <option value="">All Sex</option>
                            <option value="Male" {{ request('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ request('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </form>


                    <div class="form-row">
                        <a href="/youth/create" class="save-btn">+ Add Profile</a>
                        <a href="/youth"
                            class="active-btn border-btn {{ !request('archived') && !request('transferred') ? '' : 'opacity-50' }}">
                            Active
                        </a>
                        <a href="/youth?transferred=1"
                            class="transfer-btn border-btn {{ request('transferred') ? '' : 'opacity-50' }}">
                            Transferred
                        </a>
                        <a href="/youth?archived=1"
                            class="archive-btn border-btn {{ request('archived') ? '' : 'opacity-50' }}">
                            Archived
                        </a>


                    </div>

                </div>
            </div>

            <!-- TABLE -->
            <div class="datatable-scale overflow-x-auto">
                <table id="youthTable" class="display w-full">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Sex</th>
                            <th>Age</th>

                            @if (!request('archived') && !request('transferred'))
                                <th>SK Voter</th>
                            @endif
                            @if (request('transferred'))
                                <th>Previous Barangay</th>
                                <th>Current Barangay</th>
                            @else
                                <th>Barangay</th>
                            @endif
                            <th>Municipality</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($youths as $y)
                            <tr>
                                <td>{{ $y->last_name }}, {{ $y->first_name }}</td>
                                <td>{{ $y->sex }}</td>
                                <td>{{ $y->age }}</td>

                                {{-- SK VOTER COLUMN (ACTIVE ONLY) --}}
                                @if (!request('archived') && !request('transferred'))
                                    <td class="text-center">
                                        @if ($y->is_sk_voter === 'Yes')
                                            <span class="text-green-600 font-bold">✓</span>
                                        @else
                                            <span class="text-red-600 font-bold">✕</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- BARANGAY / TRANSFERRED COLUMNS --}}
                                @if (request('transferred'))
                                    <td>{{ $y->previous_barangay }}</td>
                                    <td>{{ $y->barangay }}</td>
                                @else
                                    <td>{{ $y->barangay }}</td>
                                @endif

                                <td>{{ $y->municipality }}</td>

                                <td>
                                    <div class="action-group">

                                        @if (!request('archived'))
                                            <button type="button" class="btn btn-indigo"
                                                onclick="openPrintOptions({{ $y->id }})">
                                                Print
                                            </button>

                                            <button type="button" class="btn btn-green"
                                                onclick='openEditModal(@json($y))'>
                                                Edit
                                            </button>

                                            @if ($isSK && $protectionEnabled)
                                                <button class="btn btn-red disabled-btn" disabled>
                                                    Archive
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-red"
                                                    onclick="handleArchive({{ $y->id }}, {{ $protectionEnabled ? 'true' : 'false' }})">
                                                    Archive
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-green"
                                                onclick='openEditModal(@json($y))'>
                                                Edit
                                            </button>

                                            @if ($isSK && $protectionEnabled)
                                                <button class="btn btn-yellow disabled-btn" disabled>
                                                    Restore
                                                </button>

                                                <button class="btn btn-red disabled-btn" disabled>
                                                    Delete
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-yellow"
                                                    onclick="handleRestore({{ $y->id }}, {{ $protectionEnabled ? 'true' : 'false' }})">
                                                    Restore
                                                </button>

                                                <button type="button" class="btn btn-red"
                                                    onclick="handleDelete({{ $y->id }}, {{ $protectionEnabled ? 'true' : 'false' }})">
                                                    Delete
                                                </button>
                                            @endif
                                        @endif

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>



        {{-- =========================================
BARANGAY POPULATION CARD
========================================= --}}
        <div class="bg-white rounded-xl shadow p-6">
            {{-- MUNICIPALITY COVERAGE SUMMARY --}}

            @php
                $totalPopulation = array_sum($barangayPopulation);
                $totalProfiles = array_sum($barangayProfiles);

                $municipalityPercent = $totalPopulation > 0 ? round(($totalProfiles / $totalPopulation) * 100, 1) : 0;
            @endphp

            <div class="municipality-summary">

                <div class="summary-header">
                    <span>Municipality Profiling Coverage</span>
                    <strong>{{ $municipalityPercent }}%</strong>
                </div>

                <div class="summary-bar">
                    <div class="summary-fill" style="width: {{ $municipalityPercent }}%">
                    </div>
                </div>

                <div class="summary-meta">
                    {{ $totalProfiles }} profiles out of {{ $totalPopulation }} youth
                </div>

            </div>
            <div class="flex justify-between items-center mb-4">

                <h2 class="text-lg font-bold text-gray-800">
                    Barangay Youth Population
                </h2>

                @if (!$isSK)
                    <div class="flex gap-2">

                        <button id="editAllPopulation" class="btn btn-indigo">
                            Edit All
                        </button>

                        <button id="saveAllPopulation" class="btn btn-green hidden">
                            Save All
                        </button>

                    </div>
                @endif

            </div>
            <div class="overflow-x-auto barangay-scroll">

                <table class="barangay-table w-full">

                    <thead>
                        <tr>
                            <th>Barangay</th>
                            <th>Total Youth Population</th>
                            <th>Profiles Added</th>
                            <th>Coverage</th>
                            @if (!$isSK)
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>

                        @php

                            $barangays = $isSK ? [$user->barangay] : $allBarangays;

                        @endphp

                        @php

                            $barangayStats = [];

                            foreach ($barangays as $b) {
                                $total = $barangayPopulation[$b] ?? 0;
                                $profiles = $barangayProfiles[$b] ?? 0;

                                $percent = $total > 0 ? round(($profiles / $total) * 100, 1) : 0;

                                $barangayStats[] = [
                                    'name' => $b,
                                    'total' => $total,
                                    'profiles' => $profiles,
                                    'percent' => $percent,
                                ];
                            }

                            usort($barangayStats, function ($a, $b) {
                                return $a['percent'] <=> $b['percent']; // lowest first
                            });

                        @endphp


                        @foreach ($barangayStats as $stat)
                            @php
                                $b = $stat['name'];
                                $total = $stat['total'];
                                $profiles = $stat['profiles'];
                                $percent = $stat['percent'];
                            @endphp

                            @php
                                $total = $barangayPopulation[$b] ?? 0;
                                $profiles = $barangayProfiles[$b] ?? 0;
                                $percent = $total > 0 ? round(($profiles / $total) * 100, 1) : 0;
                            @endphp

                            <tr>

                                <td class="font-semibold">{{ $b }}</td>

                                <td>

                                    <div class="population-display" data-barangay="{{ $b }}">
                                        <span class="population-value">{{ $total }}</span>

                                        <input type="number" class="population-input hidden" value="{{ $total }}"
                                            data-barangay="{{ $b }}">
                                    </div>

                                </td>

                                <td>
                                    <span class="profiles-count">
                                        {{ $profiles }}
                                    </span>
                                </td>

                                <td>

                                    <div class="coverage">

                                        <div class="coverage-bar">
                                            <div class="coverage-fill
{{ $percent <= 30 ? 'coverage-red' : ($percent <= 60 ? 'coverage-yellow' : 'coverage-green') }}"
                                                style="width: {{ $percent }}%">
                                            </div>
                                        </div>

                                        <span class="coverage-text">
                                            {{ $percent }}%
                                        </span>

                                    </div>

                                    @if (!$isSK)
                                <td>

                                    <button class="btn btn-indigo edit-population" data-barangay="{{ $b }}">
                                        Edit
                                    </button>

                                    <button type="submit" class="btn btn-green save-population hidden"
                                        data-barangay="{{ $b }}">
                                        Save
                                    </button>

                                </td>
                        @endif
                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>

    </div>

    {{-- ✅ EDIT MODAL IS NOW SEPARATED --}}
    @include('youth.partials.edit-modal')



    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/youth-index.css') }}">
    <!-- ========================= -->
    <!-- SCRIPTS -->
    <!-- ========================= -->

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>

    <script>
        window.csrfToken = "{{ csrf_token() }}";
    </script>

    <script src="{{ asset('js/youth-index.js') }}"></script>

@endsection
