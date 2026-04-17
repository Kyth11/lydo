@extends('layouts.app')

@section('page-title', 'SK Monitoring')
@section('page-desc', 'Barangay Report Submission')

@section('content')

<link rel="stylesheet" href="{{ asset('css/sk-monitoring.css') }}">
<link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
<script src="{{ asset('js/sk-monitoring.js') }}" defer></script>

<div class="admin-monitoring">

    <button id="openReportModal" class="save-btn" style="margin-bottom:15px;">
        + Add Report
    </button>

    @foreach ($reports as $category => $items)
        <div class="barangay-card">

            <h2 onclick="toggleBrgy(this)">
                @php
                    $first = $items->first();
                    $start = optional($first->category)->start_date;
                    $end = optional($first->category)->end_date;
                @endphp

                {{ $category }}
                @if($start && $end)
                    ({{ \Carbon\Carbon::parse($start)->format('M Y') }} - {{ \Carbon\Carbon::parse($end)->format('M Y') }})
                @endif
            </h2>

            <div class="barangay-content active">

                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Deadline</th>
                            <th>Version</th>
                            <th>Attachments</th>
                            <th>Status</th>
                            <th>Admin Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items->sortByDesc('version') as $report)

                            @php
                                $latestVersion = $items->max('version');
                            @endphp

                            <tr class="{{ $report->status === 'approved' ? 'row-approved' : ($report->status === 'rejected' ? 'row-rejected' : '') }}">

                                <td>{{ $report->created_at->format('M d, Y') }}</td>

                                <td class="truncate">
                                    {{ \Illuminate\Support\Str::limit($report->description, 60) }}
                                </td>

                                <!-- DEADLINE -->
                                <td>
                                    @if($report->category && $report->category->deadline)
                                        @if($report->is_late)
                                            <span style="color:#dc2626;">Late</span>
                                        @else
                                            <span style="color:#16a34a;">
                                                {{ \Carbon\Carbon::parse($report->category->deadline)->format('M d, Y') }}
                                            </span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>

                                <!-- VERSION -->
                                <td>
                                    <span class="status-badge pending" style="font-size:12px;">
                                        v{{ $report->version }}
                                        @if($report->version == $latestVersion)
                                            (Latest)
                                        @endif
                                    </span>
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

                                <!-- STATUS -->
                                <td>
                                    <span class="status-badge {{ $report->status }}">
                                        {{ ucfirst($report->status) }}
                                    </span>

                                    @if ($report->status !== 'pending')
                                        <div class="review-note">✔ Reviewed</div>
                                    @endif
                                </td>

                                <!-- COMMENT -->
                                <td>
                                    @if ($report->admin_comment)
                                        <div class="admin-comment">
                                            {{ $report->admin_comment }}
                                        </div>
                                    @else
                                        <span class="text-muted">No comment</span>
                                    @endif
                                </td>

                                <!-- ACTIONS -->
                                <td>
                                    @if($report->version == $latestVersion)
                                        <div style="display:flex; gap:6px;">

                                            <button onclick='openEditModal(@json($report))' class="edit-btn">
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('sk.report.delete', $report->id) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="remove-btn">
                                                    Delete
                                                </button>
                                            </form>

                                        </div>
                                    @else
                                        <span style="color:#9ca3af;">Locked</span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    @endforeach

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

</div>

@include('sk.report-modal')
@include('sk.edit-report-modal')

@endsection
