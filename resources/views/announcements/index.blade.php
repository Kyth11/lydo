@extends('layouts.app')

@section('page-title', 'Announcements')
@section('page-desc', 'Manage announcements')

@section('content')

    <div class="max-w-7xl mx-auto px-4">

        <div class="bg-white rounded-xl shadow-sm p-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h4 class="section-title">Announcements</h4>

                <a href="{{ route('announcements.create') }}" class="add-btn">
                    + Add Announcement
                </a>
            </div>

            <!-- Custom Barangay Filter -->
            <div class="mb-4 flex items-center gap-3">
                {{-- <label class="font-semibold text-sm">Filter by Barangay:</label> --}}
                <select id="barangayFilter" style="padding-right: 50px" class="border rounded-md px-3 py-2 text-sm">
                    <option value="">All Barangays</option>
                    @php
                        $allBarangays = collect($announcements)->pluck('barangay')->flatten()->unique()->sort();
                    @endphp

                    @foreach ($allBarangays as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <!-- DataTable -->
            <div class="overflow-x-auto">
                <table id="announcementTable" class="display w-full announcement-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Barangay</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($announcements as $a)
                            <tr>
                                <td class="title-cell">
                                    {{ $a->title }}
                                </td>

                                <td>
                                    @if ($a->for_all_barangays)
                                        All Barangays
                                    @elseif(is_array($a->barangay))
                                        {{ implode(', ', $a->barangay) }}
                                    @else
                                        {{ $a->barangay }}
                                    @endif
                                </td>

                                <td>{{ \Carbon\Carbon::parse($a->start_date)->format('M d, Y') }}</td>

                                <td>
                                    {{ $a->end_date ? \Carbon\Carbon::parse($a->end_date)->format('M d, Y') : '-' }}
                                </td>

                                <td>{{ $a->created_at->format('M d, Y') }}</td>

                                <td>
                                    <div class="flex gap-3">

                                        <a href="{{ route('announcements.edit', $a->id) }}" class="edit-btn">
                                            Edit
                                        </a>

                                        <form action="{{ route('announcements.destroy', $a->id) }}" method="POST"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="delete-btn delete-btn-trigger">
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

    </div>


    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            var table = $('#announcementTable').DataTable({
                pageLength: 10,
                lengthChange: false, // removes "Show entries"
                order: [
                    [4, 'desc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 5
                }]
            });

            // Custom Barangay Filter
            $('#barangayFilter').on('change', function() {
                var selected = $(this).val();

                if (selected === "") {
                    table.column(1).search('').draw();
                } else {
                    table.column(1).search(selected, true, false).draw();
                }
            });

        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.delete-btn-trigger', function() {

            let form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "This announcement will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });
    </script>

    <style>
        .section-title {
            font-size: 1.2rem !important;
            font-weight: 600 !important;
        }

        .announcement-table thead th {
            font-size: 1rem !important;
            padding: 16px 14px !important;
        }

        .announcement-table tbody td {
            font-size: 1rem !important;
            padding: 16px 14px !important;
        }

        .title-cell {
            font-weight: 600 !important;
        }

        .add-btn {
            background: #4f46e5 !important;
            color: white !important;
            padding: .6rem 1.2rem !important;
            font-size: .9rem !important;
            border-radius: .5rem !important;
            text-decoration: none !important;
        }

        .add-btn:hover {
            background: #4338ca !important;
            transition: .2s ease-in-out !important;
            translate: 0 -2px !important;
        }

        .edit-btn {
            background: #10b981 !important;
            color: white !important;
            padding: .5rem 1rem !important;
            font-size: .85rem !important;
            border-radius: .45rem !important;
            text-decoration: none !important;
        }

        .edit-btn:hover {
            background: #059669 !important;
            transition: .2s ease-in-out !important;
            translate: 0 -2px !important;
        }

        .delete-btn {
            background: #ef4444 !important;
            color: white !important;
            padding: .5rem 1rem !important;
            font-size: .85rem !important;
            border-radius: .45rem !important;
            border: none !important;
        }

        .delete-btn:hover {
            background: #dc2626 !important;
            transition: .2s ease-in-out !important;
            translate: 0 -2px !important;
        }
    </style>

@endsection
