@extends('layouts.app')

@section('page-title', 'Create Event')
@section('page-desc', 'Add a new event')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/event-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="event-form-container">

        <div class="action-bar">
            <a href="{{ route('events.index') }}" class="remove-btn">
                Cancel
            </a>

            <button form="eventForm" type="submit" class="save-btn">
                Save
            </button>
        </div>

        <form id="eventForm" method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data"
            class="event-form">

            @csrf

            {{-- IMAGE UPLOAD --}}
            <div class="form-group">
                <label>Event Images</label>
                <input type="file" id="imageInput" name="images[]" accept="image/*" multiple>

                <div id="previewContainer" class="preview-grid"></div>
            </div>

            <div class="event-divider"></div>

            {{-- Event Type --}}
            <div class="form-group">
                <label>Event Type</label>
                <select name="status" required>
                    <option value="upcoming">Upcoming Event</option>
                    <option value="past">Past Event</option>
                </select>
            </div>

            {{-- Title --}}
            <div class="form-group">
                <label>Event Title</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" required>
            </div>

            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" required>
            </div>

            {{-- Location --}}
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" required>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5" required></textarea>
            </div>

        </form>
    </div>

    {{-- ============================= --}}
    {{-- IMAGE PREVIEW + REMOVE --}}
    {{-- ============================= --}}
    <script>
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');

        let selectedFiles = [];

        imageInput.addEventListener('change', function (e) {
            selectedFiles = Array.from(e.target.files);
            renderPreviews();
        });

        function renderPreviews() {
            previewContainer.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function (e) {
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('image-wrapper');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('preview-image');

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.classList.add('remove-btn', 'small');
                    removeBtn.innerText = 'X';

                    removeBtn.onclick = function () {
                        confirmRemove(index);
                    };

                    wrapper.appendChild(img);
                    wrapper.appendChild(removeBtn);
                    previewContainer.appendChild(wrapper);
                };

                reader.readAsDataURL(file);
            });

            syncInputFiles();
        }

        function confirmRemove(index) {
            Swal.fire({
                title: 'Remove image?',
                text: 'This image will not be uploaded.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove'
            }).then((result) => {
                if (!result.isConfirmed) return;

                selectedFiles.splice(index, 1);
                renderPreviews();

                Swal.fire({
                    icon: 'success',
                    title: 'Image removed',
                    timer: 900,
                    showConfirmButton: false
                });
            });
        }

        function syncInputFiles() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            imageInput.files = dataTransfer.files;
        }
    </script>

    {{-- ============================= --}}
    {{-- VALIDATION ERRORS --}}
    {{-- ============================= --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#ef4444'
                });
            });
        </script>
    @endif

@endsection
