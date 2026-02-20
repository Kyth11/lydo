@extends('layouts.app')
@section('page-title', request('archived') ? 'Archived Youth Profiles' : 'Youth Profiles')
@section('page-desc', 'Manage and view registered youth profiles')

@section('content')

    @php
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
        $isSK = $user && $user->role === 'sk';

        // 🔥 Protection only applies to SK users
        $protectionEnabled = $isSK && \App\Models\User::where('role', 'admin')->value('action_protection');
    @endphp


    <!-- DATATABLE CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <div class="max-w-6xl mx-auto px-4 space-y-6">

        <div class="bg-white rounded-xl shadow p-6 relative">

            <!-- FILTER BAR -->
            <div class="save-bar mb-6">
                <div class="flex justify-between w-full flex-wrap gap-4">

                    <!-- FILTERS -->
                    <form method="GET" action="{{ url('/youth') }}" class="form-row">
                        <input type="hidden" name="archived" value="{{ request('archived') }}">

                        @php
                            $user = auth()->user();
                        @endphp

                        <select name="barangay" class="form-input" onchange="this.form.submit()"
                            {{ $user && $user->role === 'sk' ? 'disabled' : '' }}>

                            @if (!$user || $user->role !== 'sk')
                                <option value="">All Barangay</option>
                            @endif

                            @foreach (['Awang', 'Bagocboc', 'Barra', 'Bonbon', 'Cauyunan', 'Igpit', 'Limunda', 'Luyong Bonbon', 'Malanang', 'Nangcaon', 'Patag', 'Poblacion', 'Taboc', 'Tingalan'] as $b)
                                @if (!$user || $user->role !== 'sk' || $user->barangay === $b)
                                    <option value="{{ $b }}"
                                        {{ request('barangay', $user->role === 'sk' ? $user->barangay : '') === $b ? 'selected' : '' }}>
                                        {{ $b }}
                                    </option>
                                @endif
                            @endforeach
                        </select>

                        @if ($user && $user->role === 'sk')
                            <input type="hidden" name="barangay" value="{{ $user->barangay }}">
                        @endif


                        <select name="sex" class="form-input" onchange="this.form.submit()">
                            <option value="">All Sex</option>
                            <option value="Male" {{ request('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ request('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </form>

                    <!-- ACTIVE / ARCHIVED -->
                    <div class="form-row">
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
                        @foreach ($youths as $y)
                            <tr>
                                <td>{{ $y->last_name }}, {{ $y->first_name }}</td>
                                <td>{{ $y->sex }}</td>
                                <td>{{ $y->age }}</td>
                                <td>{{ $y->barangay }}</td>
                                <td>{{ $y->municipality }}</td>
                                <td>
                                    <div class="action-group">

                                        @if (!request('archived'))
                                            <!-- ACTIVE VIEW -->

                                            <a href="/youth/{{ $y->id }}/pdf" class="btn btn-indigo">
                                                PDF
                                            </a>

                                            <button type="button" class="btn btn-green"
                                                onclick='openEditModal(@json($y))'>
                                                Edit
                                            </button>

                                            @if ($isSK && $protectionEnabled)
                                                <button class="btn btn-red disabled-btn" disabled>
                                                    Archive
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-red"
                                                    onclick="handleArchive({{ $y->id }}, {{ $protectionEnabled ? 'true' : 'false' }})">
                                                    Archive
                                                </button>
                                            @endif
                                        @else
                                            <!-- ARCHIVED VIEW -->

                                            @if ($isSK && $protectionEnabled)
                                                <button type="button" class="btn btn-green"
                                                    onclick='openEditModal(@json($y))'>
                                                    Edit
                                                </button>

                                                <button class="btn btn-yellow disabled-btn" disabled>
                                                    Restore
                                                </button>
                                                <button class="btn btn-red disabled-btn" disabled>
                                                    Delete
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-green"
                                                    onclick='openEditModal(@json($y))'>
                                                    Edit
                                                </button>

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

    <!-- ========================= -->
    <!-- EDIT MODAL -->
    <!-- ========================= -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content animated-modal">
            <div class="save-bar">
                <div style="display:flex; gap:.75rem;">
                    <button type="button" class="cancel-btn" onclick="closeEditModal()">
                        Cancel
                    </button>

                    <button form="editForm" type="submit" class="save-btn">
                        Update Profile
                    </button>
                </div>
            </div>


            <form id="editForm" method="POST" class="youth-form">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <input id="edit_first_name" name="first_name" class="form-input" required>
                    <input id="edit_middle_name" name="middle_name" class="form-input">
                    <input id="edit_last_name" name="last_name" class="form-input" required>
                </div>

                <div class="form-row">
                    <select id="edit_sex" name="sex" class="form-input" required>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                    <input id="edit_birthday" type="date" name="birthday" class="form-input" required>
                    <input id="edit_age" type="number" name="age" class="form-input" readonly>
                </div>

                <div class="form-row">
                    <input id="edit_region" name="region" class="form-input">
                    <input id="edit_province" name="province" class="form-input">
                    <input id="edit_municipality" name="municipality" class="form-input">
                </div>

                <div class="form-row">

                    <select id="edit_barangay" name="barangay" class="form-input">
                        <option value="">Select Barangay</option>

                        @foreach (['Awang', 'Bagocboc', 'Barra', 'Bonbon', 'Cauyunan', 'Igpit', 'Limunda', 'Luyong Bonbon', 'Malanang', 'Nangcaon', 'Patag', 'Poblacion', 'Taboc', 'Tingalan'] as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>

                    <input id="edit_home_address" name="home_address" class="form-input" readonly>

                </div>


                <div class="form-row">
                    <input id="edit_religion" name="religion" class="form-input">
                    <input id="edit_contact_number" name="contact_number" class="form-input">
                </div>

                <h4 class="bold mt-6">Family Composition</h4>

                <div class="overflow-x-auto">
                    <table class="w-full border mt-2">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Relationship</th>
                                <th>Education</th>
                                <th>Occupation</th>
                                <th>Income</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="editFamilyBody"></tbody>
                    </table>

                    <button type="button" class="save-btn mt-3" onclick="addEditFamilyRow()">
                        + Add Family Member
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- ========================= -->
    <!-- SCRIPTS -->
    <!-- ========================= -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#youthTable').DataTable({
                order: [
                    [0, 'asc']
                ],
                pageLength: 10,
                lengthChange: false,
                columnDefs: [{
                    orderable: false,
                    targets: 5
                }]
            });
        });
    </script>

    <script>
        function confirmArchive(id) {
            Swal.fire({
                title: 'Archive profile?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, archive'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/youth/' + id + '/archive';
                }
            });
        }

        function confirmRestore(id) {
            Swal.fire({
                title: 'Restore profile?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                confirmButtonText: 'Yes, restore'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/youth/' + id + '/restore';
                }
            });
        }
    </script>
    <script>
        function handleArchive(id, protectedMode) {

            if (!protectedMode) {
                window.location.href = '/youth/' + id + '/archive';
                return;
            }

            Swal.fire({
                title: 'Admin Verification Required',
                input: 'password',
                inputLabel: 'Enter your password to archive',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Confirm'
            }).then(result => {
                if (result.isConfirmed && result.value) {
                    submitProtectedAction('/youth/' + id + '/archive', result.value);
                }
            });
        }

        function handleRestore(id, protectedMode) {

            if (!protectedMode) {
                window.location.href = '/youth/' + id + '/restore';
                return;
            }

            Swal.fire({
                title: 'Admin Verification Required',
                input: 'password',
                inputLabel: 'Enter your password to restore',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                confirmButtonText: 'Confirm'
            }).then(result => {
                if (result.isConfirmed && result.value) {
                    submitProtectedAction('/youth/' + id + '/restore', result.value);
                }
            });
        }

        function submitProtectedAction(action, password = null) {

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";

            form.appendChild(csrf);

            if (password !== null) {
                const pass = document.createElement('input');
                pass.type = 'hidden';
                pass.name = 'password';
                pass.value = password;
                form.appendChild(pass);
            }

            document.body.appendChild(form);
            form.submit();
        }

        function handleDelete(id, protectedMode) {

            Swal.fire({
                title: 'Permanently delete this profile?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete permanently'
            }).then(result => {

                if (!result.isConfirmed) return;

                if (!protectedMode) {
                    submitProtectedAction('/youth/' + id + '/delete', null);
                    return;
                }

                Swal.fire({
                    title: 'Admin Verification Required',
                    input: 'password',
                    inputLabel: 'Enter your password to permanently delete',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Confirm'
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        submitProtectedAction('/youth/' + id + '/delete', result.value);
                    }
                });
            });
        }
    </script>


    <script>
        function openEditModal(youth) {

            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const familyBody = document.getElementById('editFamilyBody');

            // Set form action
            form.action = '/youth/' + youth.id;

            // Fill basic inputs
            document.getElementById('edit_first_name').value = youth.first_name ?? '';
            document.getElementById('edit_middle_name').value = youth.middle_name ?? '';
            document.getElementById('edit_last_name').value = youth.last_name ?? '';
            document.getElementById('edit_sex').value = youth.sex ?? '';

            if (youth.birthday) {
                document.getElementById('edit_birthday').value = youth.birthday.split('T')[0];
            }

            document.getElementById('edit_age').value = youth.age ?? '';
            document.getElementById('edit_region').value = youth.region ?? '';
            document.getElementById('edit_province').value = youth.province ?? '';
            document.getElementById('edit_municipality').value = youth.municipality ?? '';
            document.getElementById('edit_barangay').value = youth.barangay ?? '';
            document.getElementById('edit_home_address').value = youth.home_address ?? '';
            document.getElementById('edit_religion').value = youth.religion ?? '';
            document.getElementById('edit_contact_number').value = youth.contact_number ?? '';

            /* =========================
               FAMILY MEMBERS FIX
            ========================= */

            familyBody.innerHTML = ''; // clear old rows

            if (youth.family_members && youth.family_members.length > 0) {

                youth.family_members.forEach((member, index) => {

                    const row = document.createElement('tr');

                    row.innerHTML = `
                                <td><input class="form-input" name="family_members[${index}][name]" value="${member.name ?? ''}"></td>
                                <td><input class="form-input" name="family_members[${index}][age]" value="${member.age ?? ''}"></td>
                                <td>
                                    <input class="form-input" name="family_members[${index}][relationship]" value="${member.relationship ?? ''}">
                                </td>
                                <td>
                                    <input class="form-input" name="family_members[${index}][education]" value="${member.education ?? ''}">
                                </td>
                                <td><input class="form-input" name="family_members[${index}][occupation]" value="${member.occupation ?? ''}"></td>
                                <td><input class="form-input" name="family_members[${index}][income]" value="${member.income ?? ''}"></td>
                                <td>
                                    <button type="button" class="save-btn" onclick="this.closest('tr').remove()">Remove</button>
                                </td>
                            `;

                    familyBody.appendChild(row);
                });

            } else {

                // If no family members, show empty row
                familyBody.innerHTML = `
                            <tr>
                                <td><input class="form-input" name="family_members[0][name]"></td>
                                <td><input class="form-input" name="family_members[0][age]"></td>
                                <td><input class="form-input" name="family_members[0][relationship]"></td>
                                <td><input class="form-input" name="family_members[0][education]"></td>
                                <td><input class="form-input" name="family_members[0][occupation]"></td>
                                <td><input class="form-input" name="family_members[0][income]"></td>
                                <td></td>
                            </tr>
                        `;
            }

            modal.style.display = 'flex';
        }


        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
        document.addEventListener('DOMContentLoaded', function() {

            const editBirthday = document.getElementById('edit_birthday');
            const editAge = document.getElementById('edit_age');
            const editRegion = document.getElementById('edit_region');
            const editProvince = document.getElementById('edit_province');
            const editMunicipality = document.getElementById('edit_municipality');
            const editBarangay = document.getElementById('edit_barangay');
            const editHomeAddress = document.getElementById('edit_home_address');

            function buildEditHomeAddress() {
                const parts = [
                    editBarangay.value,
                    editMunicipality.value,
                    editProvince.value,
                    editRegion.value
                ].filter(Boolean);

                editHomeAddress.value = parts.join(', ');
            }

            [editRegion, editProvince, editMunicipality, editBarangay].forEach(el => {
                if (el) {
                    el.addEventListener('input', buildEditHomeAddress);
                    el.addEventListener('change', buildEditHomeAddress);
                }
            });

            function calcAge(v) {
                if (!v) return '';
                const d = new Date(v + 'T00:00:00');
                const t = new Date();
                let a = t.getFullYear() - d.getFullYear();
                const m = t.getMonth() - d.getMonth();
                if (m < 0 || (m === 0 && t.getDate() < d.getDate())) a--;
                return a;
            }

            function updateAge() {
                editAge.value = calcAge(editBirthday.value);
            }

            editBirthday.addEventListener('change', updateAge);
            editBirthday.addEventListener('input', updateAge);

        });
        // Build address immediately
        setTimeout(() => {
            const event = new Event('input');
            document.getElementById('edit_barangay').dispatchEvent(event);
        }, 100);
    </script>



    <!-- ========================= -->
    <!-- CLEAN CSS -->
    <!-- ========================= -->
    <style>
        .cancel-btn {
            background: #ef4444;
            color: white;
            padding: .55rem 1.5rem;
            border-radius: .5rem;
            font-weight: 600;
            border: none;
        }

        .cancel-btn:hover {
            background: #dc2626;
        }

        .action-group {
            display: flex;
            gap: .4rem;
            flex-wrap: wrap
        }

        .btn {
            padding: .45rem .9rem !important;
            border-radius: .5rem !important;
            font-size: .8rem !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            transition: .2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, .1)
        }

        .btn-indigo {
            background: #4f46e5;
            color: white !important
        }

        .btn-green {
            background: #22c55e;
            color: white !important
        }

        .btn-red {
            background: #ef4444;
            color: white !important
        }

        .btn-yellow {
            background: #f59e0b;
            color: white !important
        }

        .modal-overlay {
            position: fixed;
            top: 80px;
            /* height of navbar (h-20 = 80px) */
            left: 0;
            right: 0;
            bottom: 0;

            background: rgba(0, 0, 0, .5);
            display: none;
            align-items: flex-start;
            /* start below navbar */
            justify-content: center;

            z-index: 9000;
            /* below navbar (9999) */
            padding-top: 2rem;
        }


        .modal-content {
            background: white;
            width: 95%;
            max-width: 1000px;
            border-radius: 1rem;
            padding: 2rem;
            max-height: 90vh;
            overflow: auto
        }

        .animated-modal {
            animation: fadeIn .25s ease
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(.95)
            }

            to {
                opacity: 1;
                transform: scale(1)
            }
        }

        .youth-form {
            display: flex;
            flex-direction: column;
            gap: 1rem
        }

        .form-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap
        }

        .form-input {
            flex: 1;
            min-width: 160px;
            padding: .6rem;
            border: 1px solid #d1d5db;
            border-radius: .5rem;
            transition: .2s ease
        }

        .form-input:hover {
            border-color: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(79, 70, 229, .1)
        }

        .save-bar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1rem
        }

        .save-btn {
            background: #4f46e5;
            color: white;
            padding: .55rem 1.5rem;
            border-radius: .5rem
        }

        .save-btn:hover {
            background: #4338ca
        }

        .archive-btn {
            background: #00b6b9;
            color: white;
            padding: .55rem 1.5rem;
            border-radius: .5rem;
            transition: .2s ease
        }

        .active-btn {
            background: #22c55e;
            color: white;
            padding: .55rem 1.5rem;
            border-radius: .5rem;
            transition: .2s ease
        }

        .archive-btn:hover {
            transform: translateY(-2px);
            background: #009a9d
        }

        .active-btn:hover {
            transform: translateY(-2px);
            background: #16a34a
        }

        .border-btn {
            border: .5 px solid #949494 !important;
            box-shadow: 0 4px 4px rgba(107, 107, 107, 0.1) !important;
        }

        .disabled-btn {
            background: #9ca3af !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }
    </style>

@endsection
