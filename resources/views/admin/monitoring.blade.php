@extends('layouts.app')

@section('page-title', 'SK Monitoring (Admin)')
@section('page-desc', 'All Barangay Reports Overview')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/sk-monitoring.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/sk-monitoring.js') }}" defer></script>

    <div class="admin-monitoring">

   <!-- FILTER BAR -->
<form method="GET" id="filterForm" class="dt-filter-bar">

    <!-- HEADER -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h2 style="font-weight:600;">Reports Overview</h2>
    </div>

    <div class="dt-left" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">

        <!-- BARANGAY -->
        <select name="barangay" onchange="autoSubmit()" class="dt-input">
            <option value="">All Barangays</option>
            @foreach ($barangays as $b)
                <option value="{{ $b }}" {{ request('barangay') == $b ? 'selected' : '' }}>
                    {{ $b }}
                </option>
            @endforeach
        </select>

        <!-- CATEGORY -->
        <select name="category" onchange="autoSubmit()" class="dt-input">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        <!-- DURATION -->
@if(isset($durations) && count($durations) > 0)
    <select name="duration" onchange="autoSubmit()" class="dt-input">
        <option value="">All Durations</option>

        @foreach($durations as $duration)
            <option value="{{ $duration }}" {{ request('duration') == $duration ? 'selected' : '' }}>
                {{ $duration }}
            </option>
        @endforeach
    </select>
@endif

        <!-- CATEGORY BUTTON -->
        <a href="{{ route('admin.categories.index') }}" class="save-btn">
            Category
        </a>

        <!-- DATE -->
        <input type="date"
               name="date"
               value="{{ request('date') }}"
               onchange="autoSubmit()"
               class="dt-input">

        <!-- STATUS -->
        <select name="status" onchange="autoSubmit()" class="dt-input">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

    </div>

    <!-- SEARCH -->
    <div class="dt-right" style="margin-top:10px;">
        <input type="text"
               name="search"
               placeholder="Search..."
               value="{{ request('search') }}"
               class="dt-search">
    </div>

</form>
        <!-- TABLE -->
        <div class="datatable-scale">
            <table id="reportTable" class="display w-full">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Deadline</th>
                        <th>Barangay</th>
                        <th>Description</th>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Submission</th> <!-- 🔥 NEW -->
                        <th>View</th>
                        <th>Review</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($reports as $report)

                                        <tr class="
                            {{ $report->status === 'approved' ? 'row-approved' : '' }}
                            {{ $report->status === 'rejected' ? 'row-rejected' : '' }}
                        ">

                               <td>
    @php
        $cat = $report->category ?? null;

        $start = $cat && $cat->start_date ? \Carbon\Carbon::parse($cat->start_date) : null;
        $end = $cat && $cat->end_date ? \Carbon\Carbon::parse($cat->end_date) : null;

        $label = '';

        if ($start && $end) {

            if ($start->year === $end->year) {

                if ($start->month === $end->month) {
                    $label = $start->format('F'); // April
                } else {
                    $label = $start->format('F') . '-' . $end->format('F'); // April-June
                }

            } else {
                $label = $start->format('Y') . '-' . $end->format('Y'); // 2026-2029
            }
        }
    @endphp

    <strong>
        {{ $cat->name ?? '-' }}
        @if($label)
            ({{ $label }})
        @endif
    </strong>
</td>

                                            <!-- DEADLINE -->
                                            <td>
                                                @if($report->category && $report->category->deadline)
                                                    @if($report->is_late)
                                                        <span style="color:#dc2626;font-weight:600;">
                                                            Late
                                                        </span>
                                                    @else
                                                        <span style="color:#16a34a;">
                                                            {{ \Carbon\Carbon::parse($report->category->deadline)->format('M d, Y') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td>{{ $report->barangay }}</td>

                                            <td>{{ \Illuminate\Support\Str::limit($report->description, 50) }}</td>

                                            <!-- VERSION -->
                                            <td>
                                                <span class="status-badge">
                                                    v{{ $report->version }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="status-badge {{ $report->status }}">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                            </td>

                                            <!-- SUBMISSION -->
                                            <td>
                                                <div style="font-size:13px;">
                                                    <div style="font-weight:600; color: {{ $report->is_late ? '#dc2626' : '#16a34a' }}">
                                                        {{ $report->created_at->format('M d, Y') }}
                                                    </div>
                                                    <div style="color: {{ $report->is_late ? '#dc2626' : '#16a34a' }}">
                                                        {{ $report->is_late ? 'Late' : 'On Time' }}
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- VIEW -->
                                            <td>
                                                @if ($report->files && count($report->files))
                                                    <button onclick='openFileModal(@json($report->files ?? []))' class="btn btn-indigo">
                                                        View Files ({{ count($report->files) }})
                                                    </button>
                                                @else
                                                    <span style="color:#9ca3af;">No files</span>
                                                @endif
                                            </td>

                                            <!-- REVIEW -->
                                            <td>
                                                <button onclick='openActionModal(
                                    {{ $report->id }},
                                    @json($report->status),
                                    @json($report->admin_comment)
                                )' class="archive-btn">
                                                    Review
                                                </button>
                                            </td>

                                        </tr>

                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <!-- FILE MODAL -->
    <div id="fileModal" class="modal-overlay">
        <div class="modal-box file-modal-box">

            <div class="modal-header">
                <h3>Attachments</h3>
                <button onclick="closeModal('fileModal')">✕</button>
            </div>

            <div id="filePreview"></div>

        </div>
    </div>

    <!-- REVIEW MODAL -->
    <div id="reviewModal" class="modal-overlay">
        <div class="modal-box modern-modal">

            <h2 class="section-title">Review Report</h2>

            <div class="action-bar">
                <button type="button" onclick="closeModal('reviewModal')" class="remove-btn">
                    Cancel
                </button>

                <button form="reviewForm" type="submit" class="save-btn">
                    Save
                </button>
            </div>

            <form id="reviewForm" method="POST" action="{{ route('admin.report.update') }}" class="announcement-form">
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

    <!-- DATATABLE -->
    <script>
        $(document).ready(function () {
            $('#reportTable').DataTable({
                pageLength: 10,
                lengthChange: false
            });
        });
    </script>

@endsection
