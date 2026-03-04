@extends('layouts.app')

@section('page-title', 'Edit Announcement')
@section('page-desc', 'Update announcement details')

@section('content')

    <div class="max-w-6xl mx-auto px-6">




        <div class="bg-white rounded-lg shadow-sm p-4 relative">
            <div class="action-bar">
                <a href="{{ route('announcements.index') }}" class="cancel-btn">
                    Cancel
                </a>

                <button form="announcementForm" type="submit" class="save-btn">
                    Update
                </button>
            </div>


            <form id="announcementForm" method="POST" action="{{ route('announcements.update', $announcement->id) }}"
                class="announcement-form">
                @csrf
                @method('PUT')

                <!-- I. Announcement Details -->
                <h4 class="section-title">Announcement Details</h4>

                <input type="text" name="title" class="form-input" value="{{ old('title', $announcement->title) }}"
                    required>

                <textarea name="description" rows="3" class="form-input"
                    required>{{ old('description', $announcement->description) }}</textarea>

                <!-- II. Schedule -->
                <h4 class="section-title mt-4">Schedule</h4>

                <div class="form-row">
                    <input type="date" name="start_date" class="form-input"
                        value="{{ old('start_date', \Carbon\Carbon::parse($announcement->start_date)->format('Y-m-d')) }}"
                        required>

                    <input type="date" name="end_date" class="form-input"
                        value="{{ $announcement->end_date ? \Carbon\Carbon::parse($announcement->end_date)->format('Y-m-d') : '' }}">
                </div>

                <!-- III. Scope -->
                <h4 class="section-title mt-4">Scope</h4>

                <select name="scope" id="scopeSelect" class="form-input" required>

                    <option value="all" {{ $announcement->for_all_barangays ? 'selected' : '' }}>
                        All Barangays
                    </option>

                    <option value="selected" {{ !$announcement->for_all_barangays ? 'selected' : '' }}>
                        Specific Barangays
                    </option>

                </select>

                <!-- Barangay Dynamic Select -->
                <div id="barangaySection" class="{{ $announcement->for_all_barangays ? 'hidden' : '' }} mt-2">

                    <div id="barangayContainer"></div>

                    <button type="button" id="addBarangayBtn" class="add-btn">
                        + Add Barangay
                    </button>

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

        // Convert string to array safely
        let selectedBarangays = @json(is_array($announcement->barangay)
            ? $announcement->barangay
            : (is_string($announcement->barangay)
                ? [$announcement->barangay]
        : []));

        // Scope change
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

        function addBarangaySelect(selectedValue = null) {

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

                if (selectedValue === brgy) {
                    option.selected = true;
                }

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

        // 🔥 PRELOAD EXISTING BARANGAYS
        if (selectedBarangays.length > 0 && scopeSelect.value === 'selected') {
            selectedBarangays.forEach(function (brgy) {
                addBarangaySelect(brgy);
            });
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/ann-edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">

@endsection
