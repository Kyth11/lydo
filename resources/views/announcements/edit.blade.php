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

                <textarea name="description" rows="3" class="form-input" required>{{ old('description', $announcement->description) }}</textarea>

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
        scopeSelect.addEventListener('change', function() {

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

        addBtn.addEventListener('click', function() {
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

            barangays.forEach(function(brgy) {
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

            removeBtn.addEventListener('click', function() {
                wrapper.remove();
            });

            wrapper.appendChild(select);
            wrapper.appendChild(removeBtn);

            barangayContainer.appendChild(wrapper);
        }

        // 🔥 PRELOAD EXISTING BARANGAYS
        if (selectedBarangays.length > 0 && scopeSelect.value === 'selected') {
            selectedBarangays.forEach(function(brgy) {
                addBarangaySelect(brgy);
            });
        }
    </script>

    <style>
        /* SAME STYLE AS CREATE PAGE */

        .announcement-form {
            display: flex !important;
            flex-direction: column !important;
            gap: .6rem !important;
        }

        .section-title {
            font-size: .9rem !important;
            font-weight: 600 !important;
        }

        .form-row {
            display: flex !important;
            gap: .5rem !important;
        }

        .form-input {
            flex: 1 !important;
            padding: .45rem .6rem !important;
            font-size: .85rem !important;
            border-radius: .4rem !important;
            border: 1px solid #d1d5db !important;
        }

        .save-bar {
            position: sticky !important;
            top: 5.2rem !important;
            display: flex !important;
            justify-content: flex-end !important;
            margin-bottom: .5rem !important;
        }

        .save-btn {
            background: #4f46e5 !important;
            color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            padding: .45rem .9rem !important;
            border-radius: .5rem !important;
            margin-right: 0.50rem !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .save-btn:hover {
            background: #4338ca !important;
            color: #ffffff !important;
            text-decoration: none !important;
            translate: 0 -2px !important;
            transition: all 0.50s ease !important;
        }

        .add-btn {
            margin-top: .5rem !important;
            background: #10b981 !important;
            color: white !important;
            padding: .3rem .8rem !important;
            font-size: .75rem !important;
            border-radius: .4rem !important;
            border: none !important;
            cursor: pointer !important;
        }

        .add-btn:hover {
            background: #059669 !important;
            color: #ffffff !important;
            text-decoration: none !important;
            translate: 0 -2px !important;
            transition: all 0.50s ease !important;
        }

        .barangay-row {
            display: flex !important;
            gap: .5rem !important;
            margin-bottom: .4rem !important;
        }

        .remove-btn {
            background: #ef4444 !important;
            color: white !important;
            padding: .3rem .6rem !important;
            font-size: .7rem !important;
            border-radius: .4rem !important;
            border: none !important;
            cursor: pointer !important;
        }

        .remove-btn:hover {
            background: #dc2626 !important;
            transition: .2s ease-in-out !important;
            translate: 0 -2px !important;
        }

        .hidden {
            display: none !important;
        }

        .cancel-btn {
            background: rgb(202, 0, 0) !important;
            color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            padding: .45rem .9rem !important;
            border-radius: .5rem !important;
            margin-right: 0.50rem !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .cancel-btn:hover {
            background: rgb(154, 1, 1) !important;
            color: #ffffff !important;
            text-decoration: none !important;
            translate: 0 -2px !important;
            transition: all 0.50s ease !important;
        }

        .action-bar {
            position: sticky !important;
            top: 5.2rem !important;
            display: flex !important;
            justify-content: flex-end !important;
            gap: .6rem !important;
            margin-bottom: 1rem !important;
            z-index: 10 !important;
            padding: .4rem 0 !important;
        }
    </style>

@endsection
