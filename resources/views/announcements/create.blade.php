@extends('layouts.app')

@section('page-title', 'Add Announcement')
@section('page-desc', 'Create a new public announcement')

@section('content')

    <div class="max-w-4xl mx-auto px-3">

        <div class="bg-white rounded-lg shadow-sm p-4 relative">

            <div class="action-bar">
                <a href="{{ route('announcements.index') }}" class="remove-btn">
                    Cancel
                </a>

                <button form="announcementForm" type="submit" class="save-btn">
                    Save
                </button>
            </div>

            <form id="announcementForm" method="POST" action="{{ route('announcements.store') }}" class="announcement-form">
                @csrf

                <!-- I. Announcement Details -->
                <h4 class="section-title">Announcement Details</h4>

                <div class="form-grid">
                    <input type="text" name="title" class="form-input" placeholder="Title" required>

                    <textarea name="description" rows="3" class="form-input" placeholder="Description" required></textarea>
                </div>

                <!-- II. Schedule -->
                <h4 class="section-title mt-4">Schedule</h4>

                <div class="form-grid">
                    <input type="date" name="start_date" class="form-input" required>
                    <input type="date" name="end_date" class="form-input">
                </div>

                <!-- III. Scope -->
                <h4 class="section-title mt-4">Scope</h4>

                <div class="form-grid">
                    <select name="scope" id="scopeSelect" class="form-input" required>
                        <option value="all" selected>All Barangays</option>
                        <option value="selected">Specific Barangays</option>
                    </select>

                    <!-- Barangay Dynamic Select -->
                    <div id="barangaySection" class="hidden">

                        <div id="barangayContainer"></div>

                        <button type="button" id="addBarangayBtn" class="add-btn">
                            + Add Barangay
                        </button>

                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        const scopeSelect = document.getElementById('scopeSelect');
        const barangaySection = document.getElementById('barangaySection');
        const barangayContainer = document.getElementById('barangayContainer');
        const addBtn = document.getElementById('addBarangayBtn');

        const barangays = @json($barangays);

        scopeSelect.addEventListener('change', function () {
            if (this.value === 'selected') {
                barangaySection.classList.remove('hidden');
                if (barangayContainer.children.length === 0) {
                    addBarangaySelect();
                }
            } else {
                barangaySection.classList.add('hidden');
                barangayContainer.innerHTML = '';
            }
        });

        addBtn.addEventListener('click', function () {
            addBarangaySelect();
        });

        function addBarangaySelect() {

            const wrapper = document.createElement('div');
            wrapper.classList.add('barangay-row');

            const select = document.createElement('select');
            select.name = "barangays[]";
            select.classList.add('form-input');
            select.required = true;

            const defaultOption = document.createElement('option');
            defaultOption.value = "";
            defaultOption.textContent = "Select Barangay";
            select.appendChild(defaultOption);

            barangays.forEach(function (brgy) {
                const option = document.createElement('option');
                option.value = brgy;
                option.textContent = brgy;
                select.appendChild(option);
            });

            const removeBtn = document.createElement('button');
            removeBtn.type = "button";
            removeBtn.textContent = "Remove";
            removeBtn.classList.add('remove-btn');

            removeBtn.addEventListener('click', function () {
                wrapper.remove();
            });

            wrapper.appendChild(select);
            wrapper.appendChild(removeBtn);

            barangayContainer.appendChild(wrapper);
        }
    </script>


    <link rel="stylesheet" href="{{ asset('css/ann-create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
@endsection
