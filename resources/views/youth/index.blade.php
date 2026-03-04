@extends('layouts.app')

@section('page-title', request('archived') ? 'Archived Youth Profiles' : 'Active Youth Profiles')
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

                        @if(!$isSK)
                            <option value="">All Barangay</option>
                        @endif

                        @foreach (['Awang','Bagocboc','Barra','Bonbon','Cauyunan','Igpit','Limunda','Luyong Bonbon','Malanang','Nangcaon','Patag','Poblacion','Taboc','Tingalan'] as $b)
                            @if(!$isSK || $user->barangay === $b)
                                <option value="{{ $b }}"
                                    {{ request('barangay', $isSK ? $user->barangay : '') === $b ? 'selected' : '' }}>
                                    {{ $b }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    @if($isSK)
                        <input type="hidden" name="barangay" value="{{ $user->barangay }}">
                    @endif

                    <select name="sex" class="form-input" onchange="this.form.submit()">
                        <option value="">All Sex</option>
                        <option value="Male" {{ request('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ request('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </form>

                <div class="form-row">
                    <a href="/youth/create" class="save-btn">Add Profile</a>
                    <a href="/youth" class="active-btn border-btn {{ !request('archived') ? '' : 'opacity-50' }}">
                        Active
                    </a>
                    <a href="/youth?archived=1"
                       class="archive-btn border-btn {{ request('archived') ? '' : 'opacity-50' }}">
                        Archived
                    </a>
                </div>

            </div>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table id="youthTable" class="display w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Barangay</th>
                        <th>Municipality</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($youths as $y)
                        <tr>
                            <td>{{ $y->last_name }}, {{ $y->first_name }}</td>
                            <td>{{ $y->sex }}</td>
                            <td>{{ $y->age }}</td>
                            <td>{{ $y->barangay }}</td>
                            <td>{{ $y->municipality }}</td>
                            <td>
                                <div class="action-group">

                                    @if(!request('archived'))

                                        <button type="button" class="btn btn-indigo"
                                            onclick="openPrintOptions({{ $y->id }})">
                                            Print
                                        </button>

                                        <button type="button" class="btn btn-green"
                                            onclick='openEditModal(@json($y))'>
                                            Edit
                                        </button>

                                        @if($isSK && $protectionEnabled)
                                            <button class="btn btn-red disabled-btn" disabled>Archive</button>
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

                                        @if($isSK && $protectionEnabled)
                                            <button class="btn btn-yellow disabled-btn" disabled>Restore</button>
                                            <button class="btn btn-red disabled-btn" disabled>Delete</button>
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
</div>

{{-- ✅ EDIT MODAL IS NOW SEPARATED --}}
@include('youth.partials.edit-modal')



    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
            <link rel="stylesheet" href="{{ asset('css/youth-index.css') }}">
    <!-- ========================= -->
    <!-- SCRIPTS -->
    <!-- ========================= -->

<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('js/youth-index.js') }}"></script>
            <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
            <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>


    <script src="{{ asset('js/youth-index.js') }}" defer></script>
<script>
    window.csrfToken = "{{ csrf_token() }}";
</script>

@endsection
