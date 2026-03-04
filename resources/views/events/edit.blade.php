@extends('layouts.app')

@section('page-title', 'Edit Event')
@section('page-desc', 'Update event details')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/event-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cropper.min.css') }}">


    <div class="event-form-container">


        <div class="action-bar">
            <a href="{{ route('events.index') }}" class="remove-btn">
                Cancel
            </a>

            <button form="eventForm" type="submit" class="save-btn">
                Save
            </button>
        </div>

        <form id="eventForm" method="POST" action="{{ route('events.update', $event->id) }}" enctype="multipart/form-data"
            class="event-form">

            @csrf
            @method('PUT')

            {{-- Existing Images --}}
            <div class="form-group">
                <label>Current Images</label>

                <div class="image-preview-grid">
                    @foreach($event->images as $image)
                        <div class="image-wrapper">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="preview-image">

                            <button type="button" class="remove-btn small" onclick="deleteImage({{ $image->id }})">
                                X
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="event-divider"></div>
            {{-- Add More Images --}}
            <div class="form-group">
                <label>Add More Images</label>
                <input type="file" name="images[]" id="imageInput" accept="image/*" multiple>

                <div id="previewContainer" class="image-preview-grid"></div>
            </div>

            {{-- Event Type --}}
            <div class="form-group">
                <label>Event Type</label>
                <select name="status" required>
                    <option value="upcoming" {{ $event->status === 'upcoming' ? 'selected' : '' }}>
                        Upcoming Event
                    </option>
                    <option value="past" {{ $event->status === 'past' ? 'selected' : '' }}>
                        Past Event
                    </option>
                </select>
            </div>

            {{-- Title --}}
            <div class="form-group">
                <label>Event Title</label>
                <input type="text" name="title" value="{{ $event->title }}" required>
            </div>

            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ $event->start_date->format('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ $event->end_date->format('Y-m-d') }}" required>
            </div>
            
            {{-- Location --}}
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" value="{{ $event->location }}" required>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5" required>{{ $event->description }}</textarea>
            </div>

        </form>
    </div>
    {{-- Validation Errors --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `
                                    <ul style="text-align:left;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                `,
                    confirmButtonColor: '#d33'
                });
            });
        </script>
    @endif

    <script>
        function deleteImage(imageId) {
            Swal.fire({
                title: 'Delete image?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(`/event-images/${imageId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'DELETE',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Delete failed');
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'The image has been removed.',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            location.reload();
                        }, 1200);
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not delete the image.'
                        });
                    });
            });
        }
    </script>
@endsection
