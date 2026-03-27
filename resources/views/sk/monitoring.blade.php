@extends('layouts.app')

@section('page-title', 'SK Monitoring')
@section('page-desc', 'Barangay Report Submission')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/sk-monitoring.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <script src="{{ asset('js/sk-monitoring.js') }}" defer></script>

    <div class="admin-monitoring"> {{-- 🔥 reuse same container --}}

        <!-- ADD BUTTON -->
        <button id="openReportModal" class="save-btn" style="margin-bottom:15px;">
            + Add Report
        </button>

        <!-- GROUP BY CATEGORY (LIKE ADMIN CARDS) -->
        @foreach ($reports as $category => $items)
            <div class="barangay-card"> {{-- 🔥 SAME CARD STYLE --}}

                <h2 onclick="toggleBrgy(this)">
                    {{ $category }}
                </h2>

                <div class="barangay-content active"> {{-- 🔥 open by default --}}

                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Attachments</th>
                                <th>Status</th> {{-- NEW --}}
                                <th>Admin Note</th> {{-- NEW --}}
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($items as $report)
                                <tr
                                    class="{{ $report->status === 'approved' ? 'row-approved' : ($report->status === 'rejected' ? 'row-rejected' : '') }}">

                                    <!-- DATE -->
                                    <td>{{ $report->created_at->format('M d, Y') }}</td>

                                    <!-- DESCRIPTION -->
                                    <td class="truncate">
                                        {{ \Illuminate\Support\Str::limit($report->description, 60) }}
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

                                        {{-- 🔔 Notification --}}
                                        @if ($report->status !== 'pending')
                                            <div class="review-note">✔ Reviewed</div>
                                        @endif
                                    </td>

                                    <!-- ADMIN COMMENT -->
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
                                        <div style="display:flex; gap:6px;">
                                            <button onclick='openEditModal(@json($report))' class="edit-btn">
                                                Edit
                                            </button>

                                          <form method="POST"
      action="{{ route('sk.report.delete', $report->id) }}"
      class="delete-form">

    @csrf
    @method('DELETE')

    <button type="submit" class="remove-btn delete-btn">
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
        @endforeach

        <div id="fileModal" class="modal-overlay">
            <div class="modal-box file-modal-box">

                <div class="modal-header">
                    <h3>Attachments</h3>
                    <button onclick="closeModal('fileModal')">✕</button>
                </div>

                <div id="filePreview"></div> <!-- 🔥 important: remove file-grid here -->

            </div>
        </div>
    </div>


    </div>

    @include('sk.report-modal')
    @include('sk.edit-report-modal')

@endsection
