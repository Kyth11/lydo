@extends('layouts.app')

@section('page-title', 'Register SK')
@section('page-desc', 'Create a new SK account for a Barangay')

@section('content')
<div class="max-w-4xl mx-auto px-4">

    <!-- UNIFIED ADMIN CARD -->
    <div class="bg-white rounded-xl shadow p-6">

        <form method="POST" action="{{ route('sk.store') }}" class="space-y-6 relative">
            @csrf

            <!-- HEADINGS -->
            <div class="text-center mb-4">
                <h1 class="text-xl font-bold text-gray-800">
                    Local Youth Development Office
                </h1>
                <h4 class="text-lg font-semibold text-gray-800 mt-1">
                    Opol Profiling System
                </h4>
                <p class="text-sm text-gray-500 mt-1">
                    Register Sangguniang Kabataan Account
                </p>
            </div>

            <!-- STICKY SAVE BAR -->
            <div class="save-bar">
                <a href="{{ route('dashboard') }}" class="cancel-btn">
                    Cancel
                </a>
                <button type="submit" class="save-btn">
                    Create SK Account
                </button>
            </div>

            <!-- FORM GRID -->
            <div class="form-grid">

                <!-- FULL NAME -->
                <div>
                    <label class="admin-label">Full Name</label>
                    <input name="name"
                           type="text"
                           value="{{ old('name') }}"
                           class="admin-input"
                           required>
                    @error('name') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <!-- EMAIL -->
                <div>
                    <label class="admin-label">Email Address</label>
                    <input name="email"
                           type="email"
                           value="{{ old('email') }}"
                           class="admin-input"
                           required>
                    @error('email') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <!-- BARANGAY -->
                <div>
                    <label class="admin-label">Barangay</label>
                    <select name="barangay" class="admin-input" required>
                        <option value="">Select Barangay</option>
                        @foreach(['Awang','Bagocboc','Barra','Bonbon','Cauyunan','Igpit','Limunda','Luyong Bonbon','Malanang','Nangcaon','Patag','Poblacion','Taboc','Tingalan'] as $b)
                            <option value="{{ $b }}" {{ old('barangay') === $b ? 'selected' : '' }}>
                                {{ $b }}
                            </option>
                        @endforeach
                    </select>
                    @error('barangay') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <!-- PASSWORD -->
                <div class="relative">
                    <label class="admin-label">Password (optional)</label>

                    <div class="password-wrapper">
                        <input id="password"
                               name="password"
                               type="password"
                               class="admin-input password-input"
                               onkeyup="checkCapsLock(event)"
                               onkeydown="checkCapsLock(event)">
                        <span onclick="togglePassword('password', this)"
                              class="password-eye">🙈</span>
                    </div>

                    <p class="helper-text">
                        Leave blank to auto-generate a secure password.
                    </p>

                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                </div>

            </div>

            <!-- CAPS TOAST -->
            <div id="caps-toast" class="caps-toast hidden">
                ⚠️ Caps Lock is ON
            </div>

        </form>
    </div>
</div>

<!-- CSS -->
<style>

/* GRID LAYOUT */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.admin-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.admin-input {
    width: 100% !important;
    padding: 0.6rem 0.75rem !important;
    border-radius: 0.5rem !important;
    border: 1px solid #d1d5db !important;
    background: #fff !important;
    font-size: 0.9rem;
}

.admin-input:focus {
    outline: none !important;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 1px #6366f1 !important;
}

.helper-text {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.error-text {
    font-size: 0.75rem;
    color: #dc2626;
    margin-top: 0.25rem;
}

/* PASSWORD */
.password-wrapper {
    position: relative;
}

.password-input {
    padding-right: 3rem !important;
}

.password-eye {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6b7280;
}

.password-eye:hover {
    color: #4f46e5;
}

/* CAPS TOAST */
.caps-toast {
    position: fixed;
    right: 1.5rem;
    bottom: 1.5rem;
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
    padding: .5rem .75rem;
    border-radius: .375rem;
    font-size: .75rem;
    opacity: 0;
    transform: translateY(10px);
    transition: all .25s ease;
    pointer-events: none;
    z-index: 50;
}

.caps-toast.show {
    opacity: 1;
    transform: translateY(0);
}

.hidden { display: none; }

/* SAVE BAR */
.save-bar {
    position: sticky !important;
    top: 5.5rem !important;
    z-index: 30 !important;
    padding: .75rem 0 !important;
    margin-bottom: 1rem !important;
    display: flex !important;
    justify-content: flex-end !important;
}

.save-btn {
    background: #4f46e5 !important;
    color: white !important;
    padding: .55rem 1.5rem !important;
    border-radius: .5rem !important;
    font-weight: 600 !important;
}

.save-btn:hover {
    background: #4338ca !important;
    translate: 0 -2px !important;
    transition: all 0.30s ease !important;
}

.cancel-btn {
    background: rgb(202, 0, 0) !important;
    color: #ffffff !important;
    padding: .45rem .9rem !important;
    border-radius: .5rem !important;
    margin-right: 0.50rem !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.cancel-btn:hover {
    background: rgb(154, 1, 1) !important;
    translate: 0 -2px !important;
    transition: all 0.30s ease !important;
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
        toast.classList.remove('hidden');
        toast.classList.add('show');
    } else {
        toast.classList.remove('show');
        setTimeout(() => toast.classList.add('hidden'), 200);
    }
}
</script>
@endsection
