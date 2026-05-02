@extends('layouts.app')

@section('page-title', 'Category Management')
@section('page-desc', 'Manage report categories and deadlines')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/sk-monitoring.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/category.css') }}">
@endpush

@section('content')
    <div class="admin-monitoring">

        <!-- HEADER -->
        <div class="dt-filter-bar">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2 style="font-weight:600;">Category Management</h2>


            </div>
            <button onclick="openModal('categoryModal')" class="save-btn">
                + Category
            </button>
        </div>

        <!-- TABLE -->
        <div class="datatable-scale">
            <table id="categoryTable" class="display w-full">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Duration</th>
                        <th>Deadline</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($categories as $cat)
                        @php
                            $start = $cat->start_date ? \Carbon\Carbon::parse($cat->start_date) : null;
                            $end = $cat->end_date ? \Carbon\Carbon::parse($cat->end_date) : null;

                            $sameNameCount = $categories->where('name', $cat->name)->count();

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

                        <tr>

                            <!-- NAME -->
                            <td>
                                <strong>
                                    {{ $cat->name }}

                                    @if ($sameNameCount > 1 && $durationLabel)
                                        <span style="font-size:12px;color:#6b7280;">
                                            ({{ $durationLabel }})
                                        </span>
                                    @endif
                                </strong>
                            </td>

                            <!-- DURATION -->
                            <td>
                                @if ($start && $end)
                                    <span style="color:#6b7280; font-size:13px;">
                                        {{ $start->format('M Y') }} → {{ $end->format('M Y') }}
                                    </span>
                                @else
                                    <span style="color:#9ca3af;">No duration</span>
                                @endif
                            </td>

                            <!-- DEADLINE -->
                            <td>
                                @if ($cat->deadline)
                                    @php $isLate = \Carbon\Carbon::parse($cat->deadline)->isPast(); @endphp

                                    <span style="color: {{ $isLate ? '#dc2626' : '#16a34a' }}">
                                        {{ \Carbon\Carbon::parse($cat->deadline)->format('M d, Y') }}
                                        @if ($isLate)
                                            (Old)
                                        @endif
                                    </span>
                                @else
                                    <span style="color:#9ca3af;">No deadline</span>
                                @endif
                            </td>

                            <!-- ACTIONS -->
                            <td style="display:flex; gap:6px;">

                                <button class="btn btn-indigo" onclick='openEditModal(@json($cat))'>
                                    Edit
                                </button>

                                <form method="POST" action="{{ route('admin.categories.toggle', $cat->id) }}" style="display:inline; margin-right: 15px !important;">
                                    @csrf
                                    <button type="submit" class="{{ $cat->is_active ? 'clear-btn' : 'apply-btn' }}">
                                        {{ $cat->is_active ? 'Close' : 'Open' }}
                                    </button>
                                </form>

                                <button type="button" class="archive-btn" onclick="confirmArchive({{ $cat->id }})">
                                    Archive
                                </button>

                                <button class="remove-btn" onclick="confirmDelete({{ $cat->id }})">
                                    Delete
                                </button>

                                <form id="delete-form-{{ $cat->id }}" method="POST"
                                    action="{{ route('admin.categories.delete', $cat->id) }}">
                                    @csrf
                                    @method('DELETE')
                                </form>

                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

    <!-- CATEGORY MODAL -->
    <div id="categoryModal" class="modal-overlay">
        <div class="modal-box modern-modal">

            <h2 class="section-title">Add Category</h2>

            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf

                <div style="display:flex; flex-direction:column; gap:15px;">

                    <div>
                        <label style="font-size:12px; color:#6b7280; display:block; margin-bottom:5px; margin-top:15px !important;">Category Name</label>
                        <input type="text" name="name" class="form-input" placeholder="Category Name" required>
                    </div>

                    <div>
                        <label style="font-size:12px; color:#6b7280; display:block; margin-bottom:5px;">Start Date</label>
                        <input type="date" name="start_date" class="form-input">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#6b7280; display:block; margin-bottom:5px;">End Date</label>
                        <input type="date" name="end_date" class="form-input">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#6b7280; display:block; margin-bottom:5px;">Deadline</label>
                        <input type="date" name="deadline" class="form-input">
                    </div>

                </div>

                <div class="action-bar" style="margin-top: 15px !important;">
                    <button type="button" onclick="closeModal('categoryModal')" class="remove-btn">
                        Cancel
                    </button>

                    <button type="submit" class="save-btn">
                        Save
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-box modern-modal">

            <h2 class="section-title">Edit Category</h2>

            <form method="POST" action="{{ route('admin.categories.update') }}">
                @csrf

                <input type="hidden" name="id" id="edit_id">

                <div style="display:flex; flex-direction:column; gap:15px;">

                    <div>
                        <label style="font-size:12px; color:#6b7280; display:block; margin-bottom:5px; margin-top:15px !important;">Category Name</label>
                        <input type="text" name="name" id="edit_name" class="form-input">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#6b7280; display:block; margin-bottom:5px;">Start Date</label>
                        <input type="date" name="start_date" id="edit_start" class="form-input">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#6b7280; display:block; margin-bottom:5px;">End Date</label>
                        <input type="date" name="end_date" id="edit_end" class="form-input">
                    </div>

                    <div>
                        <label style="font-size:12px; color:#6b7280; display:block; margin-bottom:5px;">Deadline</label>
                        <input type="date" name="deadline" id="edit_deadline" class="form-input">
                    </div>

                </div>

                <div class="action-bar" style="margin-top: 15px !important;">
                    <button type="button" onclick="closeModal('editModal')" class="remove-btn">
                        Cancel
                    </button>

                    <button type="submit" class="save-btn">
                        Save
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- JS -->
    <script>
        $(document).ready(function() {
            $('#categoryTable').DataTable({
                pageLength: 10,
                lengthChange: false
            });
        });

        function openModal(id) {
            document.getElementById(id).classList.add("active");
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove("active");
        }

        function openEditModal(cat) {
            document.getElementById('edit_id').value = cat.id;
            document.getElementById('edit_name').value = cat.name ?? "";
            document.getElementById('edit_start').value = cat.start_date ?? "";
            document.getElementById('edit_end').value = cat.end_date ?? "";
            document.getElementById('edit_deadline').value = cat.deadline ?? "";

            openModal("editModal");
        }

        function confirmArchive(id) {
            Swal.fire({
                title: "Archive Category?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, archive",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/admin/categories/archive/${id}`;
                }
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: "Delete Category?",
                text: "You can archive instead of deleting.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Delete",
                denyButtonText: "Archive",
                showDenyButton: true
            }).then((result) => {

                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }

                if (result.isDenied) {
                    window.location.href = `/admin/categories/archive/${id}`;
                }
            });
        }
    </script>

    @push('scripts')
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/category.js') }}"></script>
    @endpush
@endsection
