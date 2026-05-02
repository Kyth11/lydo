<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>

    <style>
        /* =========================
           MOBILE RESPONSIVE
        ========================= */
        @media (max-width: 768px) {
            .mb-4 {
                font-size: 14px !important;
                line-height: 1.5 !important;
            }

            input {
                font-size: 14px !important;
                padding: 0.5rem !important;
            }

            .flex.justify-end {
                justify-content: stretch !important;
            }

            button {
                width: 100% !important;
                padding: 0.75rem 1rem !important;
                font-size: 14px !important;
            }
        }

        @media (max-width: 480px) {
            .mb-4 {
                font-size: 13px !important;
            }

            form {
                max-width: 320px !important;
                margin: 0 auto !important;
            }
        }
    </style>
</x-guest-layout>
