<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6 relative">
        @csrf
    <div class="bg-fixed-layer"></div>
        <h2 class="text-2xl font-bold text-center text-gray-800">
            LYDO Opol Profiling System
        </h2>
        <p class="text-center text-gray-500 text-sm">
            Create Administrator Account
        </p>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="relative">
            <x-input-label for="password" :value="__('Password')" />

            <div class="password-wrapper mt-1">
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    onkeyup="checkCapsLock(event, 'caps-toast-register-password')"
                    onkeydown="checkCapsLock(event, 'caps-toast-register-password')"
                    class="password-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                <span onclick="togglePassword('password', this)" class="password-eye">🙈</span>
            </div>

            <div id="caps-toast-register-password" class="caps-toast hidden">
                ⚠️ Caps Lock is ON
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="relative">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="password-wrapper mt-1">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    onkeyup="checkCapsLock(event, 'caps-toast-register-confirm')"
                    onkeydown="checkCapsLock(event, 'caps-toast-register-confirm')"
                    class="password-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                <span onclick="togglePassword('password_confirmation', this)" class="password-eye">🙈</span>
            </div>

            <div id="caps-toast-register-confirm" class="caps-toast hidden">
                ⚠️ Caps Lock is ON
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Buttons -->
        <div class="flex justify-between items-center">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:underline">
                Already registered?
            </a>

            <x-primary-button>
                Register
            </x-primary-button>
        </div>
    </form>

    <style>

        .password-wrapper { position: relative !important; width: 100%; }
        .password-input { padding-right: 3rem !important; }
        .password-eye {
            position: absolute !important;
            top: 50% !important;
            right: 0.75rem !important;
            transform: translateY(-50%) !important;
            cursor: pointer; user-select: none;
            color: #6b7280; font-size: 1.1rem; line-height: 1;
        }
        .password-eye:hover { color: #4f46e5; }

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
            transition: opacity .25s ease, transform .25s ease;
            pointer-events: none;
            white-space: nowrap;
        }
        .caps-toast.show { opacity: 1; transform: translateY(0); }
        .bg-fixed-layer {
            position: fixed;
            inset: 0;
            z-index: -2;
            background-image: url('/images/LydoCover.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            transform: scale(1.07);
        }

        .bg-fixed-layer::after {
            content: '';
            position: absolute;
            inset: 0;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(0,0,0,0.25);
        }
        .min-h-screen { background: transparent !important; }
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

        function checkCapsLock(event, toastId) {
            const toast = document.getElementById(toastId);
            if (!toast) return;

            if (event.getModifierState && event.getModifierState('CapsLock')) {
                toast.classList.remove('hidden');
                toast.classList.add('show');
            } else {
                toast.classList.remove('show');
                setTimeout(() => toast.classList.add('hidden'), 200);
            }
        }
    </script>
</x-guest-layout>
