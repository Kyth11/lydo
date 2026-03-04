<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('images/LydoLogo.png') }}">
    <title>LYDO Opol KK Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
</head>

<body class="bg-gray-100">

    <main class="min-h-screen flex items-center justify-center max-w-7xl mx-auto px-6 py-8">
        <div class="w-full max-w-6xl">
            <div class="mb-6 bg-white p-6 rounded-xl shadow-md text-center mt-8">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <!-- Left Logo -->
                    <img src="{{ asset('images/LydoLogo.png') }}" alt="LYDO Logo" class="h-16 w-auto object-contain">
                    <!-- Title Center -->
                    <div class="text-center flex-1">
                        <h1 class="text-3xl font-extrabold text-gray-800">
                            @yield('page-title')
                        </h1>
                        <p class="text-gray-500 mt-1"> @yield('page-desc')
                        </p>
                    </div>
                    <!-- Right Logo -->
                    <img src="{{ asset('images/OpolLogo.png') }}" alt="Opol Logo" class="h-16 w-auto object-contain">

                </div>

            </div>

            @yield('content')

        </div>
    </main>
    <style>
        .main-content {
            padding-top: 2em !important;
        }
    </style>
</body>

</html>
