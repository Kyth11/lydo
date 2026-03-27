@extends('layouts.app')

@section('page-title', 'SK Monitoring (Admin)')
@section('page-desc', 'All Barangay Reports Overview')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/sk-monitoring.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <script src="{{ asset('js/sk-monitoring.js') }}" defer></script>

    <div class="admin-monitoring">
        <form method="GET" class="filter-bar" id="filterForm">
            <form method="GET" id="filterForm" class="dt-filter-bar">

                <!-- LEFT SIDE -->
                <div class="dt-left">

                    <select name="barangay" onchange="autoSubmit()" class="dt-input">
                        <option value="">All Barangays</option>
                        @foreach ($barangays as $b)
                            <option value="{{ $b }}" {{ request('barangay') == $b ? 'selected' : '' }}>
                                {{ $b }}
                            </option>
                        @endforeach
                    </select>

                    <select name="category" onchange="autoSubmit()" class="dt-input">
                        <option value="">All Categories</option>
                        <option {{ request('category') == 'CBYDP (Comprehensive Barnagay Youth Development Plan)' ? 'selected' : '' }}>
                            CBYDP (Comprehensive Barnagay Youth Development Plan)
                        </option>
                        <option {{ request('category') == 'ABYIP (Annual Barangay Youth Improvement Plan)' ? 'selected' : '' }}>
                            ABYIP (Annual Barangay Youth Improvement Plan)
                        </option>
                        <option {{ request('category') == 'SK Annual Budget' ? 'selected' : '' }}>SK Annual Budget</option>
                        <option {{ request('category') == 'Statement of Receipts' ? 'selected' : '' }}>Statement of Receipts
                        </option>
                        <option {{ request('category') == 'Katipunan ng Kabataan (KK) Assembly Reports' ? 'selected' : '' }}>
                            Katipunan ng Kabataan (KK) Assembly Reports
                        </option>
                        <option {{ request('category') == 'Linggo ng Kabataan Reports' ? 'selected' : '' }}>
                            Linggo ng Kabataan Reports
                        </option>
                        <option {{ request('category') == 'Accomplishment Reports' ? 'selected' : '' }}>
                            Accomplishment Reports
                        </option>
                        <option {{ request('category') == 'SK Resolution and Ordinances' ? 'selected' : '' }}>
                            SK Resolution and Ordinances
                        </option>
                        <option {{ request('category') == 'Attendance and Minutes of SK Meetings' ? 'selected' : '' }}>
                            Attendance and Minutes of SK Meetings
                        </option>
                        <option {{ request('category') == 'M & E' ? 'selected' : '' }}>M & E</option>
                        <option {{ request('category') == 'Special Reports' ? 'selected' : '' }}>Special Reports</option>
                    </select>

                    <input type="date" name="date" value="{{ request('date') }}" onchange="autoSubmit()" class="dt-input">
                </div>

                <!-- RIGHT SIDE -->
                <div class="dt-right">
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                        id="searchInput" class="dt-search">
                </div>

            </form>
            <!-- GROUP BY BARANGAY -->
            @foreach ($reports as $barangay => $categories)
                        <div class="barangay-card">

                            <h2 onclick="toggleBrgy(this)">
                                {{ $barangay }}
                            </h2>

                            <div class="barangay-content">

                                @foreach ($categories as $category => $items)
                                    <h4 style="margin-top:20px; font-weight: bold;">{{ $category }}</h4>

                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Attachment</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($items as $report)
                                                <tr class="
                                                                            {{ $report->updated_at > $report->created_at ? 'row-edited' : '' }}
                                                                            {{ $report->status === 'approved' ? 'row-approved' : '' }}
                                                                            {{ $report->status === 'rejected' ? 'row-rejected' : '' }}
                                                                        ">
                                                    <td>{{ $report->created_at->format('M d, Y') }}</td>
                                                    <td style="font-style:bold;">{{ $report->description }}</td>
                                                    <td>
                                                        @if ($report->files && count($report->files))
                                                            <button onclick='openFileModal(@json($report->files))' class="btn btn-indigo">
                                                                View Files ({{ count($report->files) }})
                                                            </button>
                                                        @else
                                                            <span style="color:#9ca3af;">No files</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $report->status }}</td>

                                                    <td>
                                                        <div style="display:flex; flex-direction:column; gap:4px;">

                                                            <button onclick='openActionModal(
                                                                                    {{ $report->id }},
                                                                                    "{{ $report->status }}",
                                                                                    @json($report->admin_comment)
                                                                                )' class="archive-btn">
                                                                Review
                                                            </button>

                                                            {{-- 🔔 RECENTLY EDITED --}}
                                                            @if ($report->is_edited)
                                                                <span class="edit-note">✏️ Recently edited</span>
                                                            @endif

                                                            {{-- 🔔 OPTIONAL: JUST REVIEWED --}}
                                                            @if ($report->status !== 'pending')
                                                                <span class="review-note">✔ Reviewed</span>
                                                            @endif

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endforeach

                            </div>

                        </div>


                </div>
            @endforeach
    <div id="reviewModal" class="modal-overlay">
        <div class="modal-box modern-modal">

            <h2 class="section-title">Review Details</h4>
                <div class="action-bar">
                    <button type="button" onclick="closeModal('reviewModal')" class="remove-btn">
                        Cancel
                    </button>

                    <button form="reviewForm" type="submit" class="save-btn">
                        Save
                    </button>
                </div>

                <form id="reviewForm" method="POST" class="announcement-form">
                    @csrf

                    <input type="hidden" name="report_id" id="reviewReportId">


                    <div class="form-grid">
                        <select name="status" id="reviewStatus" class="form-input" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>

                        <textarea name="admin_comment" id="reviewComment" class="form-input"
                            placeholder="Admin comment..."></textarea>
                    </div>

                </form>
        </div>
    </div>


    <div id="fileModal" class="modal-overlay">
        <div class="modal-box file-modal-box">

            <div class="modal-header">
                <h3>Attachments</h3>
                <button onclick="closeModal('fileModal')">✕</button>
            </div>

            <div id="filePreview"></div>

        </div>
    </div>

    @include('sk.report-modal')
    @include('sk.edit-report-modal')

@endsection
