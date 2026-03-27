<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('images/LydoLogo.png') }}">
    <title>LYDO Opol Profiling System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <style>
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: url("{{ asset('images/LydoCover.jpg') }}") center center no-repeat;
            background-size: cover;
            background-attachment: fixed;
            filter: blur(8px);
            transform: scale(1.05);
            z-index: -2;
        }

        .event-divider {
            width: 100%;
            margin-top: 40px;
            border: 1px solid #7f7f7f;
            border-style: dashed;
        }
    </style>

</head>

<body>

    {{-- ================= HEADER ================= --}}
    <div class="header-card">

        <div class="brand">
            <img src="{{ asset('images/LydoLogo.png') }}">
            <h2>Local Youth Development Office</h2>
        </div>

        <div style="display:flex; gap:12px;">

            @php
                $admin = \App\Models\User::where('role', 'admin')->first();
            @endphp

            @if($admin && $admin->kk_register_enabled)
                <a href="{{ route('kk.register') }}" class="login-btn"
                    style="background:linear-gradient(135deg,#16a34a,#15803d)">
                    KK Register
                </a>
            @endif

            <a href="{{ route('login') }}" class="login-btn">
                Login
            </a>

        </div>
    </div>

    <div class="header-spacer"></div>


    <section class="content-section">

        {{-- ================= DATA ================= --}}
        @php
            use Carbon\Carbon;

            $today = Carbon::today();

            $upcomingEvents = \App\Models\Event::with('images')
                ->where(function ($q) use ($today) {
                    $q->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $today);
                })
                ->orderBy('start_date', 'asc')
                ->get();

            $pastEvents = \App\Models\Event::with('images')
                ->whereDate('end_date', '<', $today)
                ->orderBy('start_date', 'desc')
                ->limit(6)
                ->get();

            $announcements = \App\Models\Announcement::where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
                ->orderBy('start_date', 'asc')
                ->get();
        @endphp


        {{-- ================= ANNOUNCEMENTS ================= --}}
        <div class="announcement-card">

            <h2 style="color:red; font-weight: bold;">Public Announcements</h2>

            <div class="auto-scroll" id="autoScroll">
                <div class="scroll-content" id="scrollContent">

                    @forelse ($announcements as $a)

                        <div class="announcement-item">

                            <h3>{{ $a->title }}</h3>

                            <p>{{ $a->description }}</p>

                            <small>
                                {{ $a->start_date->format('M d, Y') }}

                                @if ($a->end_date)
                                    - {{ $a->end_date->format('M d, Y') }}
                                @endif
                            </small>

                            <br>

                            <small>
                                <strong>Barangay:</strong>
                                {{ $a->for_all_barangays ? 'All Barangays' : implode(', ', (array) $a->barangay) }}
                            </small>

                        </div>

                    @empty
                        <p>No active announcements.</p>
                    @endforelse

                </div>
            </div>

        </div>
        {{-- ================= EVENTS ================= --}}
        <div class="events-card">

            <h2 style="color:red; font-weight: bold;">Events</h2>

                <div class="event-divider"> </div>

                <h2 style="margin-top:30px; margin-bottom:30px;">Upcoming Events</h2>

                <div class="auto-scroll" id="eventScroll">
                    <div class="scroll-content" id="eventScrollContent">

                        @forelse($upcomingEvents as $event)

                            <div class="event-item" onclick="openEventModal({{ $event->id }})">

                                <div class="event-collage">

                                    @php
                                        $images = $event->images->take(4);
                                    @endphp

                                    @forelse($images as $img)
                                        <img src="{{ asset('storage/' . $img->image_path) }}">
                                    @empty
                                        <img src="{{ asset('images/Events.png') }}">
                                    @endforelse

                                </div>

                                <div class="event-content">

                                    <h4>{{ $event->title }}</h4>

                                    <p class="event-date">
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}

                                        @if($event->end_date && $event->end_date !== $event->start_date)
                                            – {{ \Carbon\Carbon::parse($event->end_date)->format('F d, Y') }}
                                        @endif
                                    </p>

                                    <p class="event-location">
                                        📍 {{ $event->location }}
                                    </p>

                                    <p class="event-desc">
                                        {{ \Illuminate\Support\Str::limit($event->description, 90) }}
                                    </p>

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

                        @empty
                            <p>No upcoming events.</p>
                        @endforelse

                    </div>
                </div>

                <div class="event-divider"> </div>


                {{-- ================= PAST EVENTS ================= --}}
                {{-- <div class="events-card"> --}}

                    <h2 style="margin-top:30px; margin-bottom:30px;">Past Events</h2>

                    <div class="auto-scroll" id="pastEventScroll">
                        <div class="scroll-content" id="pastEventScrollContent">

                            @forelse($pastEvents as $event)

                                <div class="event-item" onclick="openEventModal({{ $event->id }})">

                                    <div class="event-collage">

                                        @php
                                            $images = $event->images->take(4);
                                        @endphp

                                        @forelse($images as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}">
                                        @empty
                                            <img src="{{ asset('images/Events.png') }}">
                                        @endforelse

                                    </div>

                                    <div class="event-content">

                                        <h4>{{ $event->title }}</h4>

                                        <p class="event-date">
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}
                                        </p>

                                        <p class="event-location">
                                            📍 {{ $event->location }}
                                        </p>

                                        <p class="event-desc">
                                            {{ \Illuminate\Support\Str::limit($event->description, 90) }}
                                        </p>

                                    </div>

                                </div>

                                {{-- HIDDEN MODAL DATA --}}
                                <div id="event-data-{{ $event->id }}" class="hidden">

                                    <h2>{{ $event->title }}</h2>

                                    <p class="modal-date">
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}

                                        @if($event->end_date && $event->end_date !== $event->start_date)
                                            – {{ \Carbon\Carbon::parse($event->end_date)->format('F d, Y') }}
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
                                            <img src="{{ asset('storage/' . $img->image_path) }}">
                                        @endforeach
                                    </div>

                                </div>

                            @empty
                                <p>No past events.</p>
                            @endforelse

                        </div>
                    </div>

                </div>
                {{-- ================= MISSION VISION ================= --}}
                <div class="cards">

                    <div class="info-card">
                        <h3>Our Mission</h3>
                        <p>
                            An empowered, inclusive, and socially responsible youth sector actively shaping a
                            progressive,
                            resilient, and sustainable community.
                        </p>
                    </div>

                    <div class="info-card">
                        <h3>Our Vision</h3>
                        <p>
                            To promote the holistic development of youth by providing inclusive programs,
                            meaningful participation opportunities, and capacity-building initiatives.
                        </p>
                    </div>

                </div>

    </section>


    {{-- ================= ANNOUNCEMENT SCROLL ================= --}}
    <script>

        const scrollContainer = document.getElementById('autoScroll');
        const scrollContent = document.getElementById('scrollContent');

        let scrollSpeed = 0.5;
        let scrollPaused = false;

        if (scrollContent.children.length > 4) {
            scrollContent.innerHTML += scrollContent.innerHTML;
        }

        function autoScroll() {

            if (!scrollPaused) {

                scrollContainer.scrollLeft += scrollSpeed;

                if (scrollContainer.scrollLeft >= scrollContent.scrollWidth / 2) {
                    scrollContainer.scrollLeft = 0;
                }

            }

            requestAnimationFrame(autoScroll);

        }

        scrollContainer.addEventListener("mouseenter", () => scrollPaused = true);
        scrollContainer.addEventListener("mouseleave", () => scrollPaused = false);

        autoScroll();

    </script>




    {{-- ================= EVENT SLIDER (5s) ================= --}}
    <script>
        const eventScroll = document.getElementById('eventScroll');
        const eventContent = document.getElementById('eventScrollContent');

        let eventPaused = false;
        let eventSpeed = 0.5;

        if (eventContent && eventContent.children.length > 4) {
            eventContent.innerHTML += eventContent.innerHTML;
        }

        function autoScrollEvents() {

            if (!eventPaused) {

                eventScroll.scrollLeft += eventSpeed;

                if (eventScroll.scrollLeft >= eventContent.scrollWidth / 2) {
                    eventScroll.scrollLeft = 0;
                }

            }

            requestAnimationFrame(autoScrollEvents);

        }

        if (eventScroll) {
            eventScroll.addEventListener("mouseenter", () => eventPaused = true);
            eventScroll.addEventListener("mouseleave", () => eventPaused = false);
            autoScrollEvents();
        }


        const pastEventScroll = document.getElementById('pastEventScroll');
        const pastEventContent = document.getElementById('pastEventScrollContent');

        let pastPaused = false;
        let pastSpeed = 0.5;

        if (pastEventContent && pastEventContent.children.length > 4) {
            pastEventContent.innerHTML += pastEventContent.innerHTML;
        }

        function autoScrollPast() {

            if (pastEventScroll && !pastPaused) {

                pastEventScroll.scrollLeft += pastSpeed;

                if (pastEventScroll.scrollLeft >= pastEventContent.scrollWidth / 2) {
                    pastEventScroll.scrollLeft = 0;
                }

            }

            requestAnimationFrame(autoScrollPast);
        }

        if (pastEventScroll) {
            pastEventScroll.addEventListener("mouseenter", () => pastPaused = true);
            pastEventScroll.addEventListener("mouseleave", () => pastPaused = false);
            autoScrollPast();
        }
    </script>


    {{-- ================= EVENT MODAL ================= --}}
    <script>

        function openEventModal(id) {

            const content = document.getElementById("event-data-" + id).innerHTML;

            Swal.fire({
                html: content,
                width: 900,
                showCloseButton: true,
                showConfirmButton: false
            });

        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
