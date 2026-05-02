@extends('layouts.app')

@section('page-title', 'SK Monitoring (Admin)')
@section('page-desc', 'All Barangay Reports Overview')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/sk-monitoring.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="admin-monitoring">

        <!-- HEADER -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="font-weight:600;">Reports Overview</h2>
            <div style="display:flex; gap:10px;">
                <!-- NUDGE BUTTON -->
                <button onclick="openModal('nudgeModal')" class="save-btn">
                    📢 Nudge/Poke
                </button>
                <!-- CATEGORY BUTTON -->
                <a href="{{ route('admin.categories.index') }}" class="save-btn">
                    Category
                </a>
            </div>
        </div>
        <!-- FILTER BAR -->
        <form method="GET" id="filterForm" class="dt-filter-bar">


            <!-- FILTER CONTROLS -->
            <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:20px;">

                <!-- BARANGAY -->
                <select name="barangay" class="dt-input">
                    <option value="">All Barangays</option>
                    @foreach ($barangays as $b)
                        <option value="{{ $b }}" {{ request('barangay') == $b ? 'selected' : '' }}>
                            {{ strtoupper($b) }}
                        </option>
                    @endforeach
                </select>

                <!-- CATEGORY -->
                <select name="category" class="dt-input">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        @php
                            $start = $cat->start_date ? \Carbon\Carbon::parse($cat->start_date) : null;
                            $end = $cat->end_date ? \Carbon\Carbon::parse($cat->end_date) : null;
                            $durationLabel = '';

                            if ($start && $end) {
                                if ($start->year === $end->year) {
                                    if ($start->month === $end->month) {
                                        $durationLabel = $start->format('F Y');
                                    } else {
                                        $durationLabel = $start->format('F Y') . ' - ' . $end->format('F Y');
                                    }
                                } else {
                                    $durationLabel = $start->format('Y') . ' - ' . $end->format('Y');
                                }
                            }
                        @endphp
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}@if ($durationLabel)
                                ({{ $durationLabel }})
                            @endif
                            @if (!$cat->is_active)
                                (Closed)
                            @endif
                        </option>
                    @endforeach
                </select>

                <!-- MONTH -->
                <select name="month" class="dt-input">
                    <option value="">All Months</option>
                    <option value="1" {{ request('month') == '1' ? 'selected' : '' }}>January</option>
                    <option value="2" {{ request('month') == '2' ? 'selected' : '' }}>February</option>
                    <option value="3" {{ request('month') == '3' ? 'selected' : '' }}>March</option>
                    <option value="4" {{ request('month') == '4' ? 'selected' : '' }}>April</option>
                    <option value="5" {{ request('month') == '5' ? 'selected' : '' }}>May</option>
                    <option value="6" {{ request('month') == '6' ? 'selected' : '' }}>June</option>
                    <option value="7" {{ request('month') == '7' ? 'selected' : '' }}>July</option>
                    <option value="8" {{ request('month') == '8' ? 'selected' : '' }}>August</option>
                    <option value="9" {{ request('month') == '9' ? 'selected' : '' }}>September</option>
                    <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>October</option>
                    <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>November</option>
                    <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>December</option>
                </select>

                <!-- YEAR -->
                <select name="year" class="dt-input">
                    <option value="">All Years</option>
                    @for($y = 2025; $y <= 2035; $y++)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <!-- STATUS -->
                <select name="status" class="dt-input">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <!-- BUTTONS -->
                <div style="display:flex; gap:10px;">
                    <button type="button" onclick="applyFilters()" class="apply-btn">Apply</button>
                    <button type="button" onclick="clearFilters()" class="clear-btn">Clear</button>
                </div>
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
                        <th>Status</th>
                        <th>Submission</th> <!-- 🔥 NEW -->
                        <th>View</th>
                        <th>Review</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($reports as $report)
                        <tr
                            class="
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
                                    @if ($label)
                                        ({{ $label }})
                                    @endif
                                </strong>
                            </td>

                            <!-- DEADLINE -->
                            <td>
                                @if ($report->category && $report->category->deadline)
                                    @if ($report->is_late)
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

                            <td>{{ strtoupper($report->barangay) }}</td>

                            <td>{{ \Illuminate\Support\Str::limit($report->description, 50) }}</td>


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
                                <button
                                    onclick='openActionModal(
                                    {{ $report->id }},
                                    @json($report->status),
                                    @json($report->admin_comment)
                                )'
                                    class="archive-btn">
                                    Review
                                </button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <!-- NUDGE MODAL -->
    <div id="nudgeModal" class="modal-overlay">
        <div class="modal-box modern-modal">

            <h2 class="section-title">Send Nudge/Poke Notification</h2>

            <div class="action-bar">
                <button type="button" onclick="closeModal('nudgeModal')" class="remove-btn">
                    Cancel
                </button>

                <button form="nudgeForm" type="submit" class="save-btn">
                    Send Nudge
                </button>
            </div>

            <form id="nudgeForm" method="POST" action="{{ route('admin.nudge.send') }}" class="announcement-form">
                @csrf

                <!-- BARANGAYS AND CATEGORIES SIDE BY SIDE -->
                <div style="display:flex; gap:15px; margin-bottom:15px;">

                    <!-- BARANGAYS -->
                    <div style="flex:1;">
                        <label style="font-weight:600; margin-bottom:8px; display:block;">SELECT BARANGAYS</label>
                        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px; max-height:200px; overflow-y:auto;">
                            @foreach ($barangays->sort() as $b)
                                <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; cursor:pointer;">
                                    <input type="checkbox" name="barangays[]" value="{{ $b }}" class="barangay-checkbox">
                                    <span>{{ strtoupper($b) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- CATEGORIES -->
                    <div style="flex:1;">
                        <label style="font-weight:600; margin-bottom:8px; display:block;">Select Categories</label>
                        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px; max-height:200px; overflow-y:auto;">
                            @foreach ($categories as $cat)
                                @php
                                    $start = $cat->start_date ? \Carbon\Carbon::parse($cat->start_date) : null;
                                    $end = $cat->end_date ? \Carbon\Carbon::parse($cat->end_date) : null;
                                    $durationLabel = '';

                                    if ($start && $end) {
                                        if ($start->year === $end->year) {
                                            if ($start->month === $end->month) {
                                                $durationLabel = $start->format('F Y');
                                            } else {
                                                $durationLabel = $start->format('F Y') . ' - ' . $end->format('F Y');
                                            }
                                        } else {
                                            $durationLabel = $start->format('Y') . ' - ' . $end->format('Y');
                                        }
                                    }
                                @endphp
                                <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; cursor:pointer;">
                                    <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" class="category-checkbox">
                                    <span>{{ $cat->name }}@if ($durationLabel) ({{ $durationLabel }})@endif @if (!$cat->is_active) (Closed)@endif</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- MESSAGE (OPTIONAL) -->
                <div>
                    <label style="font-weight:600; margin-bottom:8px; display:block;">Message (Optional)</label>
                    <textarea name="message" class="form-input" placeholder="Optional message to send with nudge..." style="min-height:100px;"></textarea>
                </div>
            </form>

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

                    <textarea name="admin_comment" id="reviewComment" class="form-input" placeholder="Admin comment..."></textarea>
                </div>
            </form>

        </div>
    </div>

    <!-- DATATABLE -->
    @push('scripts')
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sk-monitoring.js') }}" defer></script>
    <script>
        $(document).ready(function() {
            $('#reportTable').DataTable({
                pageLength: 10,
                lengthChange: false
            });
        });
    </script>
    @endpush
@endsection
