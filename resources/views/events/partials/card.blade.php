@php
    use Carbon\Carbon;

    $isPast = Carbon::parse($event->end_date)->lt(Carbon::today());
@endphp

<div class="event-card {{ $isPast ? 'past' : '' }}">

    {{-- IMAGE COLLAGE (ONLY THIS OPENS MODAL) --}}
    <div class="event-collage"
         onclick="openEventModal({{ $event->id }})">

        @php
            $images = $event->images->take(4);
        @endphp

        @forelse($images as $img)
            <img src="{{ asset('storage/' . $img->image_path) }}" alt="Event image">
        @empty
            <img src="{{ asset('images/Events.png') }}" alt="Default image">
        @endforelse
    </div>

    {{-- CONTENT --}}
    <div class="event-content">
        <h4>{{ $event->title }}</h4>

        {{-- DATE RANGE --}}
        <p class="event-date">
            {{ Carbon::parse($event->start_date)->format('F d, Y') }}
            @if($event->end_date && $event->end_date !== $event->start_date)
                – {{ Carbon::parse($event->end_date)->format('F d, Y') }}
            @endif
        </p>

        <p class="event-location">
            📍 {{ $event->location }}
        </p>

        {{-- ACTIONS --}}
        <div class="event-actions" onclick="event.stopPropagation()">
            <a href="{{ route('events.edit', $event->id) }}"
               class="edit-btn">
                Edit
            </a>

            <form id="delete-form-{{ $event->id }}"
                  action="{{ route('events.destroy', $event->id) }}"
                  method="POST">
                @csrf
                @method('DELETE')

                <button type="button"
                        class="delete-btn"
                        onclick="confirmDelete({{ $event->id }}); event.stopPropagation();">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- HIDDEN MODAL DATA --}}
    <div id="event-data-{{ $event->id }}" class="hidden">
        <h2>{{ $event->title }}</h2>

        <p class="modal-date">
            {{ Carbon::parse($event->start_date)->format('F d, Y') }}
            @if($event->end_date && $event->end_date !== $event->start_date)
                – {{ Carbon::parse($event->end_date)->format('F d, Y') }}
            @endif
        </p>

        <p class="modal-location">
            📍 {{ $event->location }}
        </p>

        <p class="modal-description">
            {{ $event->description }}
        </p>

        <div class="modal-gallery">
            @foreach($event->images as $img)
                <img src="{{ asset('storage/' . $img->image_path) }}" alt="">
            @endforeach
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDelete(eventId) {
        Swal.fire({
            title: 'Delete this event?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + eventId).submit();
            }
        });
    }
</script>
