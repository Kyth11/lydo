<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    <style>
        /* =========================
           MOBILE RESPONSIVE
        ========================= */
        @media (max-width: 768px) {
            .mt-4 {
                flex-direction: column !important;
                gap: 1rem !important;
                align-items: stretch !important;
            }

            form {
                width: 100% !important;
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
                line-height: 1.5 !important;
            }

            .text-green-600 {
                font-size: 12px !important;
            }
        }
    </style>
</x-guest-layout>
