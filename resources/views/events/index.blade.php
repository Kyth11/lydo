@extends('layouts.app')

@section('page-title', 'Events Management')
@section('page-desc', 'Manage and view events')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
@endpush

@section('content')
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        <div class="bg-white rounded-xl shadow p-6 relative">
            <div class="events-container">

                <div class="events-header">
                    <a href="{{ route('events.create') }}" class="save-btn">
                        + Create Event
                    </a>
                </div>

                <div class="event-divider"></div>

                <div class="events-side-by-side">
                    {{-- ================= UPCOMING EVENTS ================= --}}
                    <div class="event-section">
                        <h3 class="section-title">Upcoming Events</h3>

                        <div class="event-grid">
                            @foreach($upcomingEvents as $event)
                                @include('events.partials.card', ['event' => $event])
                            @endforeach
                        </div>
                    </div>

                    {{-- ================= PAST EVENTS ================= --}}
                    <div class="event-section">
                        <h3 class="section-title">Past Events</h3>

                        <div class="event-grid">
                            @foreach($pastEvents as $event)
                                @include('events.partials.card', ['event' => $event])
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- EVENT MODAL --}}
    <div id="eventModal" class="event-modal">
        <div class="event-modal-content">
            <span class="modal-close" onclick="closeEventModal()">×</span>
            <div id="eventModalBody"></div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/events-modal.js') }}"></script>
    @endpush
@endsection
