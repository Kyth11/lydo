<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>LYDO Opol Profiling System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Ant Design Reset -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/antd@5/dist/reset.css">

    <style>
        /* ===== HARD RESET / BREEZE OVERRIDE ===== */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            overflow: hidden !important; /* stop background scrolling */
            font-family: 'Segoe UI', Tahoma, sans-serif !important;
        }

        /* ===== FIXED GRADIENT BACKGROUND ===== */
        .bg-fixed-layer {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: linear-gradient(
                to bottom,
                rgba(150, 0, 0, 0.65),
                rgba(45, 35, 130, 0.65),
                rgba(10, 20, 80, 0.65)
            );
        }

        /* ===== PAGE WRAPPER (SCROLLS INSTEAD) ===== */
        .page-wrapper {
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* ===== HEADER CARD ===== */
        .header-card {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 95%;
            max-width: 1200px;
            background: linear-gradient(
                to right,
                rgba(150, 0, 0, 0.88),
                rgba(45, 35, 130, 0.88),
                rgba(10, 20, 80, 0.92)
            );
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.10);
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

        /* ===== SPACER FOR FIXED HEADER ===== */
        .header-spacer {
            height: 120px;
        }

        /* ===== COVER ===== */
        .cover {
            height: 100vh;
            width: 100%;
            background: url("{{ asset('images/LydoCover.jpg') }}") center center no-repeat;
            background-size: 100%;
            border-radius: %;

        }

        /* ===== CONTENT ===== */
        .content-section {
            padding: 80px 20px 100px;
        }

        .cards {
            max-width: 1100px;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .info-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .info-card h3 {
            margin-top: 0;
            font-size: 22px;
            color: #4f46e5;
        }

        .info-card p {
            color: #374151;
            line-height: 1.6;
        }
    </style>
</head>

<body>

    <!-- FIXED BACKGROUND -->
    <div class="bg-fixed-layer"></div>

    <!-- HEADER -->
    <div class="header-card">
        <div class="brand">
            <img src="{{ asset('images/LydoLogo.png') }}" alt="LYDO Logo">
            <h2>Local Youth Development Office<br>Profiling System</h2>
        </div>

        <a href="{{ route('login') }}" class="login-btn">Login</a>
    </div>

    <!-- SCROLLABLE CONTENT -->
    <div class="page-wrapper">

        <div class="header-spacer"></div>

        <!-- COVER -->
        <section class="cover"></section>

        <!-- CONTENT -->
        <section class="content-section">
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
                        engagement, creativity and well-being, in partnership with the community and other stakeholders.
                    </p>
                </div>

            </div>
        </section>

    </div>

</body>

</html>
