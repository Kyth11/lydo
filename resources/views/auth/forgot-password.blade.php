<x-guest-layout>
    <!-- FIXED IMAGE BACKGROUND -->
    <div class="bg-fixed-layer"></div>

    <div class="login-form space-y-6 relative">
        <div class="mb-4 text-sm text-black text-center">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="email" value="Email" class="text-gray-200" />
                <x-text-input id="email"
                    class="block mt-1 w-full bg-white/90"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <x-primary-button>
                    Email Password Reset Link
                </x-primary-button>
            </div>
        </form>
    </div>

    <style>
        html, body {
            height: 100% !important;
            margin: 0 !important;
            overflow: hidden !important;
        }

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

        .bg-fixed-layer::after {
            content: '';
            position: absolute;
            inset: 0;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(0,0,0,0.25);
        }

        .min-h-screen { background: transparent !important; }

        .login-form {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            margin: auto;
        }
    </style>
</x-guest-layout>
