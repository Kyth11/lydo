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
    </style>
</head>

<body>

    <div class="header-card">
        <div class="brand">
            <img src="{{ asset('images/LydoLogo.png') }}" alt="LYDO Logo">
            <h2>Local Youth Development Office</h2>
        </div>
        <div style="display:flex; gap:12px;">

            @php
                $admin = \App\Models\User::where('role', 'admin')->first();
            @endphp

            @if ($admin && $admin->kk_register_enabled)
                <a href="{{ route('kk.register') }}" class="login-btn"
                    style="background: linear-gradient(135deg,#16a34a,#15803d);">
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

        @php
            use Carbon\Carbon;

            $today = Carbon::today();

            $announcements = \App\Models\Announcement::where(function ($query) use ($today) {
                $query->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            })
                ->orderBy('start_date', 'asc')
                ->get();
        @endphp

        <div class="announcement-card">
            <h2>Public Announcements</h2>

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
                        <p>No active announcements at the moment.</p>
                    @endforelse

                </div>
            </div>
        </div>

        <div class="cards">
            <div class="info-card">
                <h3>Our Mission</h3>
                <p>
                    An empowered, inclusive, and socially responsible youth sector actively shaping a progressive,
                    resilient, and sustainable community.
                </p>
            </div>

            <div class="info-card">
                <h3>Our Vision</h3>
                <p>
                    To promote the holistic development of youth by providing inclusive programs, meaningful
                    participation opportunities, and capacity-building initiatives.
                </p>
            </div>
        </div>

    </section>

    <script>
        const scrollContainer = document.getElementById('autoScroll');
        const scrollContent = document.getElementById('scrollContent');

        let scrollSpeed = 0.5; // slow speed
        let scrollPaused = false;

        const items = scrollContent.children.length;

        // Only clone if more than 1 item (for smooth looping)
        if (items > 4) {
            scrollContent.innerHTML += scrollContent.innerHTML;
        }

        function autoScroll() {
            if (!scrollPaused) {
                scrollContainer.scrollLeft += scrollSpeed;

                // When scrolled halfway (original content width),
                // reset back smoothly
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

</body>

</html>
