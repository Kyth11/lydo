<!DOCTYPE html>
<html lang="en" x-data="{ open: false }">

<head>
    <meta charset="UTF-8">
    <title>KK Profiling System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ===== LYDO NAVBAR BACKGROUND ===== */
        .lydo-navbar {
            position: sticky !important;
            top: 0 !important;
            z-index: 9999 !important;
            background-image: url("{{ asset('images/LydoCover.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            isolation: isolate;
            /* create stacking context */
        }

        .lydo-navbar::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to right,
                    rgba(150, 0, 0, 0.88),
                    rgba(45, 35, 130, 0.88),
                    rgba(10, 20, 80, 0.92));
            z-index: 0;
            /* behind content */
        }

        /* Ensure all navbar content is above overlay */
        .lydo-navbar>* {
            position: relative;
            z-index: 1;
        }

        .lydo-title {
            color: #ffffff;
            font-size: 1.3rem;
            font-weight: 900;
            letter-spacing: 0.15em;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
        }

        .lydo-subtitle {
            color: rgba(255, 210, 210, 0.85);
            letter-spacing: 0.08em;
        }

        .lydo-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            filter: drop-shadow(0 6px 10px rgba(0, 0, 0, .5));
        }

        .lydo-link {
            color: white !important;
            background: rgba(255, 255, 255, 0.15);
            padding: 0.55rem 1.2rem;
            border-radius: 14px;
            font-weight: 600;
            backdrop-filter: blur(6px);
            transition: all 0.25s ease;
            white-space: nowrap;
            margin-right: 0.5rem;
        }

        .lydo-link:hover {
            background: #facc15 !important;
            color: #111 !important;
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        }

        /* MOBILE PANEL */
        .lydo-mobile-panel {
            background: linear-gradient(to bottom, #1e1b4b, #1e3a8a);
            padding: 1rem !important;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            z-index: 9999;
        }

        .lydo-mobile-link {
            display: block !important;
            width: 100% !important;
            padding: 0.75rem 1rem !important;
            color: white !important;
            border-radius: 10px !important;
            background: rgba(255, 255, 255, 0.12) !important;
            margin-bottom: 0.5rem !important;
            font-weight: 600 !important;
            transition: 0.25s ease !important;
        }

        .lydo-mobile-link-logout {
            display: block !important;
            width: 100% !important;
            padding: 0.75rem 1rem !important;
            color: white !important;
            border-radius: 10px !important;
            background: rgba(255, 255, 255, 0.12) !important;
            margin-bottom: 0.5rem !important;
            font-weight: 600 !important;
            transition: 0.25s ease !important;
        }

        .lydo-mobile-link-logout:hover {
            background: #ff0000 !important;
            color: #ffffff !important;
        }

        .lydo-mobile-link:hover {
            background: #facc15 !important;
            color: #111 !important;
        }

        .lydo-dropdown {
            background: linear-gradient(to bottom, #1e1b4b, #1e3a8a);
            border-radius: 14px;
            padding: 0.5rem;
            min-width: 220px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, .55);
            z-index: 99999 !important;
        }

        .lydo-dropdown-item {
            display: block;
            width: 100%;
            padding: 0.65rem 0.9rem;
            border-radius: 10px;
            color: white !important;
            font-weight: 600;
            text-align: left;
            transition: all 0.25s ease;
            background: rgba(255, 255, 255, 0.12);
        }

        .lydo-dropdown-item:hover {
            background: #facc15 !important;
            color: #111 !important;
            transform: translateX(2px);
        }

        .lydo-dropdown-item.logout {
            background: rgba(255, 0, 0, 0.25);
        }

        .lydo-dropdown-item.logout:hover {
            background: #dc2626 !important;
            color: white !important;
        }

/* space between navbar and main content  */
        .main-content {
            padding-top: 2em !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">

    <!-- NAVBAR -->
    <nav class="lydo-navbar shadow-xl border-b border-black/30">

        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between h-20 items-center">

                <!-- LEFT -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/LydoLogo.png') }}" class="lydo-logo" />
                        <div class="hidden sm:block">
                            <h1 class="lydo-title">LYDO OPOL</h1>
                            <p class="lydo-subtitle text-xs">Youth Profiling System</p>
                        </div>
                    </a>

                    <!-- DESKTOP LINKS -->
                    <div class="hidden sm:flex sm:ms-10 space-x-3">
                        <a href="{{ route('dashboard') }}" class="lydo-link">Dashboard</a>
                        <a href="/youth" class="lydo-link">Youth Profiles</a>
                        <a href="/youth/create" class="lydo-link">Add Profile</a>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="hidden sm:flex items-center relative z-50">
                    <x-dropdown align="right" width="56"
                        contentClasses="bg-transparent shadow-none ring-0 p-0 overflow-visible">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/15 backdrop-blur-md text-white font-semibold hover:bg-yellow-400 hover:text-black transition shadow-lg">
                                {{ Auth::user()->name }}
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="lydo-dropdown space-y-1">
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('sk.create') }}" class="lydo-dropdown-item">Add SK</a>
                                    <button onclick="toggleProtection()" class="lydo-dropdown-item">
                                        {{ Auth::user()->action_protection ? 'Disable Protection' : 'Enable Protection' }}
                                    </button>

                                @endif
                                <a href="{{ route('account.edit') }}" class="lydo-dropdown-item">Edit Account</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="lydo-dropdown-item logout">Log Out</button>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- BURGER -->
                <div class="sm:hidden relative z-50">
                    <button @click="open = !open"
                        class="text-white text-3xl p-2 rounded-lg hover:bg-white/20 transition">
                        ☰
                    </button>
                </div>

            </div>
        </div>

        <!-- MOBILE PANEL -->
        <div x-show="open" x-transition @click.away="open=false" class="sm:hidden lydo-mobile-panel">
            <a href="{{ route('dashboard') }}" class="lydo-mobile-link">Dashboard</a>
            <a href="/youth" class="lydo-mobile-link">Youth Profiles</a>
            <a href="/youth/create" class="lydo-mobile-link">Add Profile</a>

            @if(Auth::user()->isAdmin())
                <a href="{{ route('sk.create') }}" class="lydo-mobile-link">Add SK</a>
            @endif

            <a href="{{ route('account.edit') }}" class="lydo-mobile-link">Edit Account</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="lydo-mobile-link-logout bg-red-600">
                    Log Out
                </button>
            </form>
        </div>

    </nav>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-6 py-8 main-content">

        {{-- SweetAlert Flash Messages --}}
        @if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    @if(session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: "{{ session('success') }}",
                            confirmButtonColor: '#4f46e5'
                        });
                    @endif

                    @if(session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: "{{ session('error') }}",
                            confirmButtonColor: '#dc2626'
                        });
                    @endif

                    @if(session('warning'))
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: "{{ session('warning') }}",
                            confirmButtonColor: '#f59e0b'
                        });
                    @endif

                    @if(session('info'))
                        Swal.fire({
                            icon: 'info',
                            title: 'Information',
                            text: "{{ session('info') }}",
                            confirmButtonColor: '#2563eb'
                        });
                    @endif

                    @if($errors->any())
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: `{!! implode('<br>', $errors->all()) !!}`,
                            confirmButtonColor: '#dc2626'
                        });
                    @endif

                });
            </script>
        @endif
        <div class="mb-6 bg-white p-6 rounded-xl shadow-md">
            <h1 class="text-3xl font-extrabold text-gray-800">
                @yield('page-title')
            </h1>
            <p class="text-gray-500 mt-1">
                @yield('page-desc')
            </p>
        </div>

        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(Auth::user()->isAdmin())
<script>
function toggleProtection() {
    Swal.fire({
        title: 'Admin Verification Required',
        input: 'password',
        inputLabel: 'Enter your password',
        inputPlaceholder: 'Password',
        inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Verify',
        confirmButtonColor: '#4f46e5'
    }).then((result) => {
        if (result.isConfirmed && result.value) {

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('admin.toggle.protection') }}";

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";

            const password = document.createElement('input');
            password.type = 'hidden';
            password.name = 'password';
            password.value = result.value;

            form.appendChild(csrf);
            form.appendChild(password);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endif

</body>

</html>
