@extends('layouts.app')

@section('page-title', 'SK Monitoring')
@section('page-desc', 'Barangay Report Submission')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/sk-monitoring.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="admin-monitoring">
        <!-- HEADER -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="font-weight:600;">My Reports</h2>
            <button type="button" id="openReportModal" class="save-btn">
                + Add Report
            </button>
        </div>
        <!-- FILTER BAR -->
        <form method="GET" id="filterForm" class="dt-filter-bar">



            <!-- FILTER CONTROLS -->
            <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:20px;">

                <!-- STATUS -->
                <select name="status" class="dt-input">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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

                <!-- BUTTONS -->
                <button type="button" onclick="applyFilters()" class="apply-btn">Apply</button>
                <button type="button" onclick="clearFilters()" class="clear-btn">Clear</button>

            </div>

        </form>

        <!-- TABLE -->
        <div class="datatable-scale">
            <table id="reportTable" class="display w-full">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Submission</th>
                        <th>Attachments</th>
                        <th>Admin Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        // Flatten the reports structure for DataTable
                        $allReports = [];
                        foreach ($reports as $category => $items) {
                            foreach ($items as $report) {
                                $allReports[] = $report;
                            }
                        }
                    @endphp

                    @foreach ($allReports as $report)
                        <tr
                            class="{{ $report->status === 'approved' ? 'row-approved' : ($report->status === 'rejected' ? 'row-rejected' : '') }}">

                            <!-- CATEGORY -->
                            <td>
                                @php
                                    $cat = $report->category ?? null;
                                    $start = $cat && $cat->start_date ? \Carbon\Carbon::parse($cat->start_date) : null;
                                    $end = $cat && $cat->end_date ? \Carbon\Carbon::parse($cat->end_date) : null;
                                    $label = '';

                                    if ($start && $end) {
                                        if ($start->year === $end->year) {
                                            if ($start->month === $end->month) {
                                                $label = $start->format('F');
                                            } else {
                                                $label = $start->format('F') . '-' . $end->format('F');
                                            }
                                        } else {
                                            $label = $start->format('Y') . '-' . $end->format('Y');
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

                            <!-- DESCRIPTION -->
                            <td>{{ \Illuminate\Support\Str::limit($report->description, 50) }}</td>

                            <!-- DEADLINE -->
                            <td>
                                @if ($report->category && $report->category->deadline)
                                    @if ($report->is_late)
                                        <span style="color:#dc2626;font-weight:600;">Late</span>
                                    @else
                                        <span style="color:#16a34a;">
                                            {{ \Carbon\Carbon::parse($report->category->deadline)->format('M d, Y') }}
                                        </span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <!-- STATUS -->
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

                            <!-- FILES -->
                            <td>
                                @if ($report->files && count($report->files))
                                    <button onclick='openFileModal(@json($report->files))' class="btn btn-indigo">
                                        View Files ({{ count($report->files) }})
                                    </button>
                                @else
                                    <span style="color:#9ca3af;">No files</span>
                                @endif
                            </td>

                            <!-- ADMIN NOTE -->
                            <td>
                                @if ($report->admin_comment)
                                    <div class="admin-comment">
                                        {{ $report->admin_comment }}
                                    </div>
                                @else
                                    <span style="color:#9ca3af;">No comment</span>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td>
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">

                                    <button type="button" onclick='openEditModal(@json($report))'
                                        class="edit-btn">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('sk.report.delete', $report->id) }}"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="remove-btn">
                                            Delete
                                        </button>
                                    </form>
                                </div>
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

    @include('sk.report-modal')
    @include('sk.edit-report-modal')

    <!-- DATATABLE INITIALIZATION -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#reportTable').DataTable({
                pageLength: 10,
                lengthChange: false,
                order: [
                    [0, 'desc']
                ], // Sort by first column (Category/Date) descending
                columnDefs: [{
                    orderable: false,
                    targets: 7 // Disable sorting on Actions column (index 7 after removing Date column)
                }]
            });

            // Auto submit function for filters
            window.autoSubmit = function() {
                var form = $('#filterForm');
                var url = new URL(window.location.href);
                var params = new URLSearchParams(url.search);

                // Update URL parameters based on form values
                form.find('select, input').each(function() {
                    var name = $(this).attr('name');
                    var value = $(this).val();
                    if (value) {
                        params.set(name, value);
                    } else {
                        params.delete(name);
                    }
                });

                // Reload page with new parameters
                window.location.href = url.pathname + '?' + params.toString();
            };

            // Handle delete confirmation
            $('.remove-btn').on('click', function(e) {
                var form = $(this).closest('form');

                // Only show confirmation if button is inside a form (delete action)
                if (form.length === 0) {
                    return;
                }

                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This report will be permanently deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Handle nudge notification - pre-select category and open modal
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const nudgeCategory = urlParams.get('nudge_category');

            if (nudgeCategory) {
                const categorySelect = document.getElementById('editCategory');

                if (categorySelect) {
                    // Set the category as selected
                    categorySelect.value = nudgeCategory;

                    // Open the add report modal
                    setTimeout(function() {
                        openModal('reportModal');

                        // Remove the nudge_category from URL to avoid reopening modal on refresh
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }, 500);
                }
            }
        });
    </script>

    @push('scripts')
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sk-monitoring.js') }}" defer></script>
    @endpush
@endsection
