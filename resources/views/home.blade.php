<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>LYDO Opol Profiling System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/antd@5/dist/reset.css">

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            overflow-x: hidden;
        }

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

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: -1;
        }

        .header-card {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 95%;
            max-width: 1200px;
            background: linear-gradient(to right,
                    rgba(150, 0, 0, 0.88),
                    rgba(45, 35, 130, 0.88),
                    rgba(10, 20, 80, 0.92));
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-radius: 14px;
            padding: 16px 24px;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand img {
            height: 48px;
        }

        .brand h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
        }

        .login-btn {
            padding: 10px 22px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #ffffff;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .login-btn:hover {
            opacity: 0.9;
        }

        .header-spacer {
            height: 120px;
        }

        .content-section {
            padding: 60px 20px 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .announcement-card {
            background: rgba(255, 255, 255, 0.75);
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
            padding: 35px;
            margin-bottom: 60px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        .announcement-card h3 {
            font-size: 18px;
            margin-bottom: 3px;
            color: #dc2626;
        }

        .announcement-card p {
            font-size: 14px;
            color: #4b5563;
            margin: 6px 0;
            line-height: 1.5;
        }

        .auto-scroll {
            max-height: 320px;
            overflow: hidden;
            margin-top: 15px;
            text-align: center;
            position: relative;
        }

        .scroll-content {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .cards {
            max-width: 1100px;
            width: 100%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .info-card {
         background: rgba(255, 255, 255, 0.75);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            transition: all 0.3s ease;
        }

        .info-card h3 {
            margin-top: 0;
            font-size: 22px;
            color: #4f46e5;
            text-align: center;
        }

        .info-card p {
            color: #374151;
            line-height: 1.6;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header-card">
        <div class="brand">
            <img src="{{ asset('images/LydoLogo.png') }}" alt="LYDO Logo">
            <h2>Local Youth Development Office<br>Profiling System</h2>
        </div>
        <a href="{{ route('login') }}" class="login-btn">Login</a>
    </div>

    <div class="header-spacer"></div>

    <section class="content-section">

@php
    use Carbon\Carbon;

    $today = Carbon::today();

    $announcements = \App\Models\Announcement::where(function ($query) use ($today) {

        // Show if:
        // 1. No end date (open-ended)
        // 2. End date is today or future
        $query->whereNull('end_date')
              ->orWhereDate('end_date', '>=', $today);

    })
    ->orderBy('start_date', 'asc') // upcoming first
    ->get();
@endphp


        <div class="announcement-card">

            <h2>Public Announcements</h2>

            <div class="auto-scroll" id="autoScroll">
                <div class="scroll-content" id="scrollContent">

                    @forelse ($announcements as $a)
                        <div style="padding-bottom:15px; border-bottom:1px solid #e5e7eb;">

                            <h3>{{ $a->title }}</h3>

                            <p>{{ $a->description }}</p>

                            <small style="display:block; margin-top:6px; color:#6b7280; font-size:12px;">
                                {{ $a->start_date->format('M d, Y') }}
                                @if ($a->end_date)
                                    - {{ $a->end_date->format('M d, Y') }}
                                @endif
                            </small>

                            <small style="display:block; margin-top:4px; font-size:12px; color:#374151;">
                                <strong>Barangay:</strong>
                                {{ $a->for_all_barangays ? 'All Barangays' : implode(', ', (array) $a->barangay) }}
                            </small>

                        </div>
                    @empty
                        <p style="text-align:center; font-size:14px; color:#6b7280;">
                            No active announcements at the moment.
                        </p>
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
                    participation opportunities, and capacity-building initiatives that nurture leadership, civic
                    engagement, creativity and well-being.
                </p>
            </div>

        </div>

    </section>

    <script>
        const scrollContainer = document.getElementById('autoScroll');

        let scrollSpeed = .5; // Adjust this value to increase/decrease scroll speed
        let scrollPaused = false;

        function autoScroll() {
            if (!scrollPaused) {
                scrollContainer.scrollTop += scrollSpeed;

                if (scrollContainer.scrollTop >= scrollContainer.scrollHeight - scrollContainer.clientHeight) {
                    scrollContainer.scrollTop = 0 // Reset to the top;
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
