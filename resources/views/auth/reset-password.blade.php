<x-guest-layout>

    <!-- FIXED IMAGE BACKGROUND -->
    <div class="bg-fixed-layer"></div>

    <form method="POST" action="{{ route('password.store') }}" class="login-form space-y-6 relative">
        @csrf

        <!-- Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <h1 class="text-2xl h1 font-bold text-center text-black">
            Local Youth Development Office
        </h1>
        <h1 class="text-xl h2 font-bold text-center text-black">
            Opol Profiling System
        </h1>
        <p class="text-center text-black text-sm">
            Reset Your Password
        </p>

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Email" class="text-gray-200" />
            <x-text-input id="email"
                class="block mt-1 w-full bg-white/90"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="relative">
            <x-input-label for="password" value="New Password" class="text-gray-200" />

            <div class="password-wrapper mt-1">
                <input id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    onkeyup="checkCapsLock(event)"
                    onkeydown="checkCapsLock(event)"
                    class="password-input block w-full rounded-md border-gray-300 bg-white/90 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                <span onclick="togglePassword('password', this)" class="password-eye">🙈</span>
            </div>

            <div id="caps-toast" class="caps-toast hidden">
                ⚠️ Caps Lock is ON
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="relative">
            <x-input-label for="password_confirmation" value="Confirm Password" class="text-gray-200" />

            <div class="password-wrapper mt-1">
                <input id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="password-input block w-full rounded-md border-gray-300 bg-white/90 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                <span onclick="togglePassword('password_confirmation', this)" class="password-eye">🙈</span>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Buttons -->
        <div class="flex justify-between items-center">
            <a href="{{ route('login') }}"
                class="inline-flex items-center px-4 py-2 bg-white/80 border border-black shadow-lg rounded-md text-sm">
                Back to Login
            </a>

            <x-primary-button>
                Reset Password
            </x-primary-button>
        </div>
    </form>

    <style>
        html, body {
            height: 100% !important;
            margin: 0 !important;
            overflow: hidden !important;
        }

        /* IMAGE BACKGROUND */
        .bg-fixed-layer {
            position: fixed !important;
            inset: 0 !important;
            z-index: -1 !important;
            background-image: url('/images/LydoCover.jpg') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            transform: scale(1.05);
        }

        .bg-fixed-layer::after {
            content: '' !important;
            position: absolute !important;
            inset: 0 !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            background: rgba(0,0,0,0.25) !important;
        }

        /* Remove Breeze card */
        .min-h-screen {
            background: transparent !important;
        }

        .login-form {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 2.5rem !important;
            max-width: 420px !important;
            width: 100% !important;
            margin: auto !important;
        }

        .h1 { color: black !important; font-size: 20px !important; font-weight: bold !important; }
        .h2 { color: black !important; font-size: 18px !important; font-weight: bold !important; }

        /* Password UI */
        .password-wrapper { position: relative !important; }
        .password-input { padding-right: 3rem !important; }

        .password-eye {
            position: absolute !important;
            top: 50% !important;
            right: 0.75rem !important;
            transform: translateY(-50%) !important;
            cursor: pointer !important;
            color: #6b7280 !important;
        }

        .password-eye:hover { color: #4f46e5 !important; }

        /* Caps Lock Toast */
        .caps-toast {
            position: absolute !important;
            right: 0 !important;
            top: -2.5rem !important;
            background: #fee2e2 !important;
            color: #991b1b !important;
            border: 1px solid #fecaca !important;
            padding: 0.4rem 0.6rem !important;
            border-radius: 0.375rem !important;
            font-size: 0.75rem !important;
            opacity: 0 !important;
            transform: translateY(6px) !important;
            transition: all 0.3s ease !important;
            pointer-events: none !important;
        }

        .caps-toast.show {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    </style>

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

</x-guest-layout>
