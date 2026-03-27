<!DOCTYPE html>
<html lang="en" x-data="{ open: false }">

<head>


    <meta charset="UTF-8">
    <link rel="preload" href="{{ asset('images/LydoLoading.png') }}" as="image" type="image/png">
    <link rel="icon" href="{{ asset('images/LydoLogo.png') }}">
    <title>LYDO Opol KK Profiling System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <!-- TOP PAGE LOADER -->
    <div id="topLoader">
        <div class="loader-bar"></div>
    </div>

    <!-- PAGE LOADER -->
    <div id="pageLoader"
        style="position:fixed;inset:0;background:#fff;display:flex;align-items:center;justify-content:center;z-index:99998;">

        <div class="loader-center" style="text-align:center;">

            <div class="logo-wrapper"
                style="position:relative;width:120px;height:120px;display:flex;align-items:center;justify-content:center;">

                <img src="{{ asset('images/LydoLoading.png') }}" class="loader-logo" width="82" height="82"
                    style="width:82px;height:82px;object-fit:contain;opacity:0;transform:scale(.92);">

                <svg viewBox="0 0 120 120" class="loader-progress"
                    style="position:absolute;inset:0;transform:rotate(-90deg);">

                    <circle cx="60" cy="60" r="54" stroke="#4f46e5" stroke-width="6" fill="none"
                        stroke-linecap="round" style="stroke-dasharray:339;stroke-dashoffset:339;opacity:0;">
                    </circle>

                </svg>

            </div>

            <p class="loader-text">Loading...</p>

        </div>

    </div>
    <!-- NAVBAR -->
    <nav class="lydo-navbar shadow-xl border-b border-black/30" x-data="{ open: false }">

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
                    @auth
                        <div class="hidden sm:flex items-center ml-6">
                            <a href="{{ route('dashboard') }}" class="lydo-link">Dashboard</a>
                            <a href="/youth" class="lydo-link">Youth Profiles</a>

                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('announcements.index') }}" class="lydo-link">Announcements</a>
                                <a href="{{ route('events.index') }}" class="lydo-link">Events</a>
                            @endif
                            @auth
                                @if (Auth::user()->role === 'sk')
                                    <a href="{{ route('sk.monitoring') }}" class="lydo-link">SK Monitoring</a>
                                @endif

                                @if (Auth::user()->isAdmin())
                                    <a href="{{ route('admin.monitoring') }}" class="lydo-link">SK Monitoring</a>
                                @endif
                            @endauth
                        </div>
                    @endauth
                </div>

                <!-- RIGHT -->
                <div class="hidden sm:flex items-center">
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
                        </x-slot>

                        <x-slot name="content">
                            <div class="lydo-dropdown space-y-1">

                                @auth
                                    @if (Auth::user()->isAdmin())
                                        <button onclick="toggleProtection()" class="lydo-dropdown-item">
                                            {{ \App\Models\User::where('role', 'admin')->value('action_protection')
                                                ? '🔐 SK Archive Disabled'
                                                : '🔓 SK Archive Enabled' }}
                                        </button>

                                        <button onclick="toggleKKRegister()" class="lydo-dropdown-item">
                                            {{ auth()->user()->kk_register_enabled ? '👁 KK Register Shown' : '🙈 KK Register Hidden' }}
                                        </button>

                                        <div class="lydo-dropdown-divider"></div>

                                        <a href="{{ route('sk.manage') }}" class="lydo-dropdown-item">
                                            Manage SK Account
                                        </a>
                                    @endif
                                @endauth

                                <a href="{{ route('account.edit') }}" class="lydo-dropdown-item">
                                    Edit Account
                                </a>
                                <div class="lydo-dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="lydo-dropdown-item logout">
                                        Log Out
                                    </button>
                                </form>

                            </div>
                        </x-slot>

                    </x-dropdown>
                </div>

                <!-- BURGER -->
                <div class="sm:hidden">
                    <button @click="open = !open"
                        class="text-white text-3xl p-2 rounded-lg hover:bg-white/20 transition">
                        ☰
                    </button>
                </div>

            </div>
        </div>

        <!-- MOBILE MENU -->
        <div x-show="open" x-transition @click.away="open=false" class="sm:hidden lydo-mobile-panel">




            @auth
                @if (Auth::user()->isAdmin())
                    <a onclick="toggleProtection()" class="lydo-mobile-link">
                        {{ \App\Models\User::where('role', 'admin')->value('action_protection')
                            ? '🔐 SK Archive Disabled'
                            : '🔓 SK Archive Enabled' }}
                    </a>

                    <a onclick="toggleKKRegister()" class="lydo-mobile-link">
                        {{ auth()->user()->kk_register_enabled ? '👁 KK Register Shown' : '🙈 KK Register Hidden' }}
                    </a>
                @endif
            @endauth

            <div class="lydo-dropdown-divider"></div>
            <a href="{{ route('dashboard') }}" class="lydo-mobile-link" @click="open=false">
                Dashboard
            </a>

            <a href="/youth" class="lydo-mobile-link" @click="open=false">
                Youth Profiles
            </a>

            @auth
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('sk.manage') }}" class="lydo-mobile-link">
                        Manage SK Account
                    </a>

                    <a href="{{ route('events.index') }}" class="lydo-mobile-link">
                        Events
                    </a>
                @endif
            @endauth

            @auth
                @if (Auth::user()->role === 'sk')
                    <a href="{{ route('sk.monitoring') }}" class="lydo-mobile-link">
                        SK Monitoring
                    </a>
                @endif

                @if (Auth::user()->isAdmin())
                    <a href="{{ route('admin.monitoring') }}" class="lydo-mobile-link">
                        SK Monitoring
                    </a>
                @endif
            @endauth

            <a href="{{ route('account.edit') }}" class="lydo-mobile-link">
                Edit Account
            </a>

            <div class="lydo-dropdown-divider"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a type="submit" class="lydo-mobile-link lydo-mobile-link-logout">
                    Log Out
                </a>
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

    @auth
        @if (Auth::user()->isAdmin())
            <script>
                function showAdminPasswordModal(title, confirmText, confirmColor, callback) {

                    const isMobile = window.innerWidth < 640;

                    Swal.fire({
                        title: title,
                        width: isMobile ? '90%' : '420px',
                        padding: isMobile ? '1.4rem' : '2rem',
                        confirmButtonText: confirmText,
                        confirmButtonColor: confirmColor,
                        showCancelButton: true,
                        focusConfirm: false,

                        html: `
                                                                                                                                                                <div style="
                                                                                                                                                                    display:flex;
                                                                                                                                                                    flex-direction:column;
                                                                                                                                                                    align-items:center;
                                                                                                                                                                    justify-content:center;
                                                                                                                                                                    margin-top:10px;
                                                                                                                                                                    overflow:hidden;
                                                                                                                                                                ">

                                                                                                                                                                    <div style="
                                                                                                                                                                        position:relative;
                                                                                                                                                                        width:100%;
                                                                                                                                                                        max-width:280px;
                                                                                                                                                                    ">

                                                                                                                                                                        <input
                                                                                                                                                                            id="swal-password"
                                                                                                                                                                            type="password"
                                                                                                                                                                            class="swal2-input"
                                                                                                                                                                            placeholder="Enter Admin Password"
                                                                                                                                                                            style="
                                                                                                                                                                                width:100%;
                                                                                                                                                                                margin:0 auto;
                                                                                                                                                                                padding-right:42px;
                                                                                                                                                                                font-size:${isMobile ? '16px' : '15px'};
                                                                                                                                                                                height:42px;
                                                                                                                                                                                border-radius:10px;
                                                                                                                                                                                box-sizing:border-box;
                                                                                                                                                                            "
                                                                                                                                                                        />

                                                                                                                                                                        <span id="toggle-eye"
                                                                                                                                                                            style="
                                                                                                                                                                                position:absolute;
                                                                                                                                                                                right:12px;
                                                                                                                                                                                top:50%;
                                                                                                                                                                                transform:translateY(-50%);
                                                                                                                                                                                cursor:pointer;
                                                                                                                                                                                font-size:18px;
                                                                                                                                                                                opacity:0.75;
                                                                                                                                                                                line-height:1;
                                                                                                                                                                            ">
                                                                                                                                                                            🙈
                                                                                                                                                                        </span>

                                                                                                                                                                    </div>

                                                                                                                                                                    <div id="caps-warning"
                                                                                                                                                                        style="
                                                                                                                                                                            color:#f59e0b;
                                                                                                                                                                            font-size:13px;
                                                                                                                                                                            margin-top:8px;
                                                                                                                                                                            display:none;
                                                                                                                                                                            text-align:center;
                                                                                                                                                                            width:100%;
                                                                                                                                                                        ">
                                                                                                                                                                        ⚠️ Caps Lock is ON
                                                                                                                                                                    </div>

                                                                                                                                                                </div>
                                                                                                                                                                `,

                        preConfirm: () => {
                            return document.getElementById('swal-password').value;
                        },

                        didOpen: () => {

                            const passwordInput = document.getElementById('swal-password');
                            const eye = document.getElementById('toggle-eye');
                            const capsWarning = document.getElementById('caps-warning');

                            passwordInput.focus();

                            // 👁 Toggle visibility
                            eye.addEventListener('click', () => {
                                if (passwordInput.type === "password") {
                                    passwordInput.type = "text";
                                    eye.textContent = "👁";
                                } else {
                                    passwordInput.type = "password";
                                    eye.textContent = "🙈";
                                }
                            });

                            // ⚠️ Caps lock detection
                            passwordInput.addEventListener('keyup', (e) => {
                                capsWarning.style.display =
                                    e.getModifierState && e.getModifierState('CapsLock') ?
                                    "block" :
                                    "none";
                            });

                            passwordInput.addEventListener('keydown', (e) => {
                                capsWarning.style.display =
                                    e.getModifierState && e.getModifierState('CapsLock') ?
                                    "block" :
                                    "none";
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const loader = document.getElementById("topLoader");

            function startLoader() {
                loader.classList.add("active");
            }

            function stopLoader() {
                loader.classList.remove("active");
            }

            /* Stop loader when page finishes loading */
            window.addEventListener("load", stopLoader);

            /* Links */
            document.querySelectorAll("a[href]").forEach(link => {

                const href = link.getAttribute("href");

                if (
                    href &&
                    !href.startsWith("#") &&
                    !href.startsWith("javascript") &&
                    !link.hasAttribute("target")
                ) {
                    link.addEventListener("click", () => {

                        if (document.querySelector(".swal2-container")) return;

                        startLoader();

                    });
                }

            });

            /* Forms */
            document.querySelectorAll("form").forEach(form => {

                form.addEventListener("submit", () => {

                    if (document.querySelector(".swal2-container")) return;

                    startLoader();

                });

            });

        });
    </script>

    <link rel="stylesheet" href="{{ asset('css/app-layout.css') }}">
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/chart.js') }}"></script>

    <script>
        window.addEventListener("load", () => {

            const loader = document.getElementById("pageLoader");

            setTimeout(() => {
                loader.classList.add("hide");
            }, 500);

        });
    </script>

</body>

</html>
