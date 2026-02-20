@extends('layouts.app')

@section('page-title', 'Add Announcement')
@section('page-desc', 'Create a new public announcement')

@section('content')

<div class="max-w-4xl mx-auto px-3">

    <div class="bg-white rounded-lg shadow-sm p-4 relative">

        <div class="action-bar">
            <a href="{{ route('announcements.index') }}" class="cancel-btn">
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

<style>

    /* FORM GRID (2 COLUMNS) */
    .form-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: .6rem !important;
    }

    /* Mobile fallback */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* Layout */
    .announcement-form {
        display: flex !important;
        flex-direction: column !important;
        gap: .8rem !important;
    }

    .section-title {
        font-size: .9rem !important;
        font-weight: 600 !important;
    }

    .form-input {
        width: 100% !important;
        padding: .45rem .6rem !important;
        font-size: .85rem !important;
        border-radius: .4rem !important;
        border: 1px solid #d1d5db !important;
    }

    .form-input:focus {
        outline: none !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 1px #6366f1 !important;
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
        translate: 0 -2px !important;
        transition: all 0.3s ease !important;
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
        translate: 0 -2px !important;
        transition: .2s ease-in-out !important;
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
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .cancel-btn:hover {
        background: rgb(154, 1, 1) !important;
        translate: 0 -2px !important;
        transition: all 0.3s ease !important;
    }

    .save-btn {
        background: #4f46e5 !important;
        color: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        padding: .45rem .9rem !important;
        border-radius: .5rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .save-btn:hover {
        background: #4338ca !important;
        translate: 0 -2px !important;
        transition: all 0.3s ease !important;
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
