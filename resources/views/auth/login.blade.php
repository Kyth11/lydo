<x-guest-layout>
    <!-- FIXED IMAGE BACKGROUND -->
    <div class="bg-fixed-layer"></div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="login-form space-y-6 relative">
        @csrf

        <h1 class="text-2xl h1 font-bold text-center text-black">
            Local Youth Development Office
        </h1>
        <h1 class="text-xl h2 font-bold text-center text-black">
            Opol Profiling System
        </h1>
        <p class="text-center text-black text-sm">
            Administrator Login
        </p>

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Email" class="text-gray-200" />
            <x-text-input id="email" class="block mt-1 w-full bg-white/90" type="email" name="email" {{--
                :value="old('email')" --}} required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="relative">
            <x-input-label for="password" value="Password" class="text-gray-200" />

            <div class="password-wrapper mt-1">
                <input id="password" name="password" type="password" required onkeyup="checkCapsLock(event)"
                    onkeydown="checkCapsLock(event)"
                    class="password-input block w-full rounded-md border-gray-300 bg-white/90 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <span onclick="togglePassword('password', this)" class="password-eye">🙈</span>
            </div>

            <div id="caps-toast" class="caps-toast hidden">
                ⚠️ Caps Lock is ON
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between">
            <label class="flex items-center text-black">
                <input type="checkbox" id="remember" name="remember" class="rounded border-gray-400">

                <span class="ms-2 text-sm">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-500 hover:underline" href="{{ route('password.request') }}">
                Forgot password?
                </a>
            @endif
        </div>
        <!-- Register + Login -->
        {{-- <div class="flex justify-between items-center">
            <a href="{{ route('register') }}"
                class="inline-flex items-center px-4 py-2 bg-white/80 border border-black shadow-lg rounded-md text-sm">
                Register
            </a>

            <x-primary-button>
                Log in
            </x-primary-button>
        </div> --}}

        <!-- Buttons -->
        <div class="flex justify-center items-center !important">
              <x-primary-button class="!mx-auto !block !text-center !important">
                Log in
            </x-primary-button>
        </div>

    </form>

    <!-- STYLES -->
    <style>
        html,
        body {
            height: 100% !important;
            margin: 0 !important;
            overflow: hidden !important;
        }

        /* FULLSCREEN IMAGE BACKGROUND */
        .bg-fixed-layer {
            position: fixed;
            inset: 0;
            z-index: -2;
            background-image: url('/images/LydoCover.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            transform: scale(1.05);
        }

        /* BLUR + DARK OVERLAY */
        .bg-fixed-layer::after {
            content: '';
            position: absolute;
            inset: 0;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(0, 0, 0, 0.25);
        }

        /* Override Breeze guest layout */
        .min-h-screen {
            background: transparent !important;
        }

        /* REMOVE CARD BACKGROUND COMPLETELY */
        .login-form {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            margin: auto;
        }

        /* Password UI */
        .password-wrapper {
            position: relative;
        }

        .password-input {
            padding-right: 3rem;
        }

        .password-eye {
            position: absolute;
            top: 50%;
            right: 0.75rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
        }

        .password-eye:hover {
            color: #4f46e5;
        }

        .h1 {
            color: black !important;
            text-align: center !important;
            font-size: 20px !important;
            font-weight: bold !important;
        }

        .h2 {
            color: black !important;
            text-align: center !important;
            font-size: 18px !important;
            font-weight: bold !important;
        }

        /* Caps Lock Toast */
        .caps-toast {
            position: absolute;
            right: 0;
            top: -2.5rem;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 0.4rem 0.6rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            opacity: 0;
            transform: translateY(6px);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .caps-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .hidden {
            display: none;
        }
    </style>

    <!-- JS -->
    <script>
        function togglePassword(inputId, el) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                el.textContent = "👁";
            } else {
                input.type = "password";
                el.textContent = "🙈";
            }
        }

        function checkCapsLock(event) {
            const toast = document.getElementById('caps-toast');
            if (event.getModifierState && event.getModifierState('CapsLock')) {
                toast.classList.add('show');
                toast.classList.remove('hidden');
            } else {
                toast.classList.remove('show');
                setTimeout(() => toast.classList.add('hidden'), 200);
            }
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const emailInput = document.getElementById("email");
            const rememberCheckbox = document.getElementById("remember");

            // Load saved email
            const savedEmail = localStorage.getItem("rememberedEmail");

            if (savedEmail) {
                emailInput.value = savedEmail;
                rememberCheckbox.checked = true;
            }

            // Save email when typing
            emailInput.addEventListener("input", function () {
                if (rememberCheckbox.checked) {
                    localStorage.setItem("rememberedEmail", emailInput.value);
                }
            });

            // Remove email if unchecked
            rememberCheckbox.addEventListener("change", function () {
                if (!this.checked) {
                    localStorage.removeItem("rememberedEmail");
                } else {
                    localStorage.setItem("rememberedEmail", emailInput.value);
                }
            });

        });
    </script>

</x-guest-layout>
