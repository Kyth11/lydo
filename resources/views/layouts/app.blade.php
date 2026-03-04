<!DOCTYPE html>
<html lang="en" x-data="{ open: false }">

<head>


    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('images/LydoLogo.png') }}">
    <title>LYDO Opol KK Profiling System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ================================
   NAVBAR BASE (DESKTOP)
================================ */

        .lydo-navbar {
            position: sticky !important;
            top: 0 !important;
            z-index: 9999 !important;
            background-image: url("{{ asset('images/LydoCover.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            isolation: isolate;
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
        }

        .lydo-navbar>* {
            position: relative;
            z-index: 1;
        }

        /* ================================
   BRANDING
================================ */

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

        /* ================================
   DESKTOP LINKS
================================ */

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
            font-size: 14px;
        }

        .lydo-link:hover {
            background: #facc15 !important;
            color: #111 !important;
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        }

        /* ================================
   DROPDOWN
================================ */

        .lydo-dropdown {
            background: linear-gradient(to bottom, #1e1b4b, #1e3a8a);
            border-radius: 14px;
            padding: 0.5rem;
            min-width: 230px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, .55);
            z-index: 99999 !important;
        }

        .lydo-dropdown-item {
            display: block;
            width: 100%;
            padding: 0.6rem 0.9rem;
            border-radius: 10px;
            color: white !important;
            font-weight: 600;
            text-align: left;
            font-size: 14px;
            transition: all 0.25s ease;
            background: rgba(255, 255, 255, 0.12);
        }

        .lydo-dropdown-item:hover {
            background: #facc15 !important;
            color: #111 !important;
            transform: translateX(2px);
        }

        .lydo-dropdown-item.logout {
            background: rgba(189, 0, 0, 0.53);
        }

        .lydo-dropdown-item.logout:hover {
            background: #dc2626 !important;
            color: white !important;
        }

        .lydo-dropdown-divider {
            height: 2px;
            background: rgba(255, 255, 255, 0.15);
            margin: 0.8rem 0;
        }

        /* ================================
   MOBILE PANEL — UNIFORM SIZE FIX
================================ */

        .lydo-mobile-panel {
            background: linear-gradient(to bottom, #1e1b4b, #1e3a8a);
            padding: 1rem !important;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* FORCE IDENTICAL SIZE FOR ALL ITEMS */
        .lydo-mobile-link,
        .lydo-mobile-link-logout {

            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;

            width: 100% !important;

            height: 52px !important;
            /* fixed identical height */
            padding: 0 16px !important;
            /* horizontal only */

            font-size: 15px !important;
            font-weight: 600 !important;

            border-radius: 14px !important;

            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;

            border: none !important;
            outline: none !important;

            margin-bottom: 10px !important;

            transition: all 0.25s ease !important;
            cursor: pointer !important;

            white-space: nowrap !important;
            /* prevent wrapping */
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* Normal hover */
        .lydo-mobile-link:hover {
            background: #facc15 !important;
            color: #111 !important;
        }

        /* Logout special style */
        .lydo-mobile-link-logout {
            background: #8b1c3b !important;
        }

        .lydo-mobile-link-logout:hover {
            background: #dc2626 !important;
            color: #fff !important;
        }

        /* ================================
   MAIN CONTENT
================================ */

        .main-content {
            padding-top: 2em !important;
        }

        .main-content h1 {
            font-size: 28px;
        }

        .main-content p {
            font-size: 15px;
        }

        /* ================================
   RESPONSIVE
================================ */

        @media (max-width: 768px) {

            .lydo-navbar .h-20 {
                height: 70px !important;
            }

            .lydo-logo {
                width: 42px;
                height: 42px;
            }

            .lydo-title {
                font-size: 1rem;
                letter-spacing: 0.08em;
            }

            .lydo-subtitle {
                font-size: 10px;
            }

            .sm\:hidden button {
                font-size: 1.8rem !important;
            }

            .main-content h1 {
                font-size: 20px !important;
            }

            .main-content p {
                font-size: 13px !important;
            }

            .max-w-7xl {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .main-content {
                padding-top: 1.2rem !important;
            }
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
                        {{-- <a href="/youth/create" class="lydo-link">Add Profile</a> --}}
                        @auth
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('announcements.index') }}" class="lydo-link bg-yellow-400 text-black">
                                    Announcements
                                </a>
                                   <a href="{{ route('events.index') }}" class="lydo-link">Events</a>
                            @endif
                        @endauth

                    </div>
                </div>

                <!-- RIGHT -->
                <div class="hidden sm:flex items-center relative z-50">
                    <x-dropdown align="right" width="56"
                        contentClasses="bg-transparent shadow-none ring-0 p-0 overflow-visible">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/15 backdrop-blur-md text-white font-semibold hover:bg-yellow-400 hover:text-black transition shadow-lg">
                                @auth
                                    {{ Auth::user()->name }}
                                @endauth
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </button>
                        </x-slot> @csrf

                        <x-slot name="content">
                            <div class="lydo-dropdown space-y-1">
                                @auth
                                    @if (Auth::user()->isAdmin())
                                        <button onclick="toggleProtection()" class="lydo-dropdown-item">
                                            {{ \App\Models\User::where('role', 'admin')->value('action_protection') ? '🔐 SK Archive Disabled' : '🔓 SK Archive Enabled' }}
                                        </button>
                                        <button onclick="toggleKKRegister()" class="lydo-dropdown-item">
                                            {{ auth()->user()->kk_register_enabled ? '👁 KK Register Shown ' : '🙈 KK Register Hidden' }}
                                        </button>
                                        <div class="lydo-dropdown-divider"></div>
                                        <a href="{{ route('sk.manage') }}" class="lydo-dropdown-item">
                                            Manage SK Account
                                        </a>

                                        {{-- <a href="{{ route('sk.create') }}" class="lydo-dropdown-item">
                                            Add SK
                                        </a> --}}
                                        <a href="#" class="lydo-dropdown-item">
                                            Reports
                                        </a>
                                    @endif
                                @endauth

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
            <button type="button" onclick="toggleProtection()" class="lydo-mobile-link">
                {{ \App\Models\User::where('role', 'admin')->value('action_protection')
                    ? '🔐 SK Archive Disabled'
                    : '🔓 SK Archive Enabled' }}
            </button>

            <button type="button" onclick="toggleKKRegister()" class="lydo-mobile-link">
                {{ auth()->user()->kk_register_enabled ? '👁 KK Register Shown' : '🙈 KK Register Hidden' }}
            </button>

            <div class="lydo-dropdown-divider"></div>
            <a href="{{ route('dashboard') }}" class="lydo-mobile-link">Dashboard</a>
            <a href="/youth" class="lydo-mobile-link">Youth Profiles</a>

            @auth
                @if (Auth::user()->isAdmin())
                    {{-- <a href="{{ route('sk.create') }}" class="lydo-mobile-link">
                Add SK
            </a> --}}



                    <a href="{{ route('sk.manage') }}" class="lydo-mobile-link">
                        Manage SK Account
                    </a>

                    <a href="#" class="lydo-mobile-link">
                        Reports
                    </a>
                @endif
            @endauth

            <a href="{{ route('account.edit') }}" class="lydo-mobile-link">
                Edit Account
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="lydo-mobile-link lydo-mobile-link-logout">
                    Log Out
                </button>
            </form>

        </div>

    </nav>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-6 py-8 main-content">

        {{-- SweetAlert Flash Messages --}}
        @if (session('success') || session('error') || session('warning') || session('info') || $errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    @if (session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: "{{ session('success') }}",
                            confirmButtonColor: '#4f46e5'
                        });
                    @endif

                    @if (session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: "{{ session('error') }}",
                            confirmButtonColor: '#dc2626'
                        });
                    @endif

                    @if (session('warning'))
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: "{{ session('warning') }}",
                            confirmButtonColor: '#f59e0b'
                        });
                    @endif

                    @if (session('info'))
                        Swal.fire({
                            icon: 'info',
                            title: 'Information',
                            text: "{{ session('info') }}",
                            confirmButtonColor: '#2563eb'
                        });
                    @endif

                    @if ($errors->any())
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
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>

    @auth
        @if (Auth::user()->isAdmin())
            <script>
                function showAdminPasswordModal(title, confirmText, confirmColor, callback) {

                    Swal.fire({
                        title: title,
                        html: `
            <div style="position:relative;margin-top:10px;">
                <input id="swal-password"
                       type="password"
                       class="swal2-input"
                       placeholder="Enter Admin Password"
                       style="padding-right:40px;" />

                <span id="toggle-eye"
                      style="position:absolute;right:30px;top:30px;cursor:pointer;font-size:18px;">
                      🙈
                </span>

                <div id="caps-warning"
                     style="color:#f59e0b;font-size:13px;margin-top:5px;display:none;">
                    ⚠️ Caps Lock is ON
                </div>
            </div>
        `,
                        showCancelButton: true,
                        confirmButtonText: confirmText,
                        confirmButtonColor: confirmColor,
                        focusConfirm: false,
                        preConfirm: () => {
                            return document.getElementById('swal-password').value;
                        },
                        didOpen: () => {

                            const passwordInput = document.getElementById('swal-password');
                            const eye = document.getElementById('toggle-eye');
                            const capsWarning = document.getElementById('caps-warning');

                            // 👁 Toggle visibility
                            eye.addEventListener('click', function() {
                                if (passwordInput.type === "password") {
                                    passwordInput.type = "text";
                                    eye.textContent = "👁";
                                } else {
                                    passwordInput.type = "password";
                                    eye.textContent = "🙈";
                                }
                            });

                            // ⚠️ Caps Lock detection
                            passwordInput.addEventListener('keyup', function(e) {
                                if (e.getModifierState && e.getModifierState('CapsLock')) {
                                    capsWarning.style.display = "block";
                                } else {
                                    capsWarning.style.display = "none";
                                }
                            });

                            passwordInput.addEventListener('keydown', function(e) {
                                if (e.getModifierState && e.getModifierState('CapsLock')) {
                                    capsWarning.style.display = "block";
                                } else {
                                    capsWarning.style.display = "none";
                                }
                            });

                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            callback(result.value);
                        }
                    });
                }
            </script>

            <script>
                // 🔒 SK Archive Protection Toggle
                function toggleProtection() {

                    showAdminPasswordModal(
                        'Admin Verification Required',
                        'Verify SK Archive Protection',
                        '#4f46e5',
                        function(password) {

                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = "{{ route('admin.toggle.protection') }}";

                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = "{{ csrf_token() }}";

                            const pass = document.createElement('input');
                            pass.type = 'hidden';
                            pass.name = 'password';
                            pass.value = password;

                            form.appendChild(csrf);
                            form.appendChild(pass);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    );
                }
            </script>

            <script>
                // 🔓 KK Registration Toggle
                function toggleKKRegister() {

                    showAdminPasswordModal(
                        'Admin Verification Required',
                        'Verify KK Register',
                        '#f59e0b',
                        function(password) {

                            fetch("{{ route('admin.toggle.kk') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        password: password
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {

                                    if (data.success) {

                                        Swal.fire({
                                            icon: 'success',
                                            title: data.enabled ?
                                                'KK Registration Enabled' : 'KK Registration Disabled',
                                            text: data.enabled ?
                                                'The KK registration form is now accessible.' :
                                                'The KK registration form has been disabled.',
                                            confirmButtonColor: '#16a34a'
                                        }).then(() => {
                                            location.reload();
                                        });

                                    } else {

                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Incorrect Password',
                                            text: 'Admin verification failed.',
                                            confirmButtonColor: '#dc2626'
                                        });

                                    }

                                });
                        }
                    );
                }
            </script>
        @endif
    @endauth
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
</body>

</html>
