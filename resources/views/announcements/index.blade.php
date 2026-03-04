@extends('layouts.app')

@section('page-title', 'Announcements')
@section('page-desc', 'Manage announcements')

@section('content')

    <div class="max-w-7xl mx-auto px-4">

        <div class="bg-white rounded-xl shadow-sm p-6">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h4 class="section-title"></h4>

                <a href="{{ route('announcements.create') }}" class="save-btn">
                    + Add Announcement
                </a>
            </div>

            <!-- Custom Barangay Filter -->
            <div class="mb-4 flex items-center gap-3">
                <label class="font-semibold text-sm">Filter by Barangay:</label>
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

                                        <a href="{{ route('announcements.edit', $a->id) }}" class="btn btn-green">
                                            Edit
                                        </a>

                                        <form action="{{ route('announcements.destroy', $a->id) }}" method="POST"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-red delete-btn-trigger">
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
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ann-index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>

    <!-- DataTables JS -->


    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/youth-index.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>


    <script>
        $(document).ready(function () {

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
            $('#barangayFilter').on('change', function () {
                var selected = $(this).val();

                if (selected === "") {
                    table.column(1).search('').draw();
                } else {
                    table.column(1).search(selected, true, false).draw();
                }
            });

        });

        // SweetAlert Delete Confirmation
        $(document).on('click', '.delete-btn-trigger', function () {

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

@endsection
