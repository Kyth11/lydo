@extends('layouts.app')

@section('page-title', 'Edit Account')
@section('page-desc', 'Update your account details')

@section('content')
<div class="max-w-6xl mx-auto px-4 space-y-6">

    <div class="bg-white rounded-xl shadow p-6 relative">

        <!-- STICKY SAVE BAR -->
        <div class="save-bar">
            <a href="{{ route('dashboard') }}" class="cancel-btn">Cancel</a>
            <button form="accountForm" type="submit" class="save-btn">
                Save Changes
            </button>
        </div>

        <form id="accountForm" method="POST" action="{{ route('account.update') }}" class="youth-form">
            @csrf
            @method('PATCH')

            <!-- NAME -->
            <div class="form-row">
                <div class="w-full">
                    <label class="form-label">Full Name</label>
                    <input name="name"
                           value="{{ old('name', $user->name) }}"
                           class="form-input w-full"
                           required>
                    @error('name') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-row">
                <div class="w-full">
                    <label class="form-label">Email Address</label>
                    <input name="email"
                           type="email"
                           value="{{ old('email', $user->email) }}"
                           class="form-input w-full"
                           required>
                    @error('email') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- BARANGAY (visible only to SK users) -->
            @if($user->isSK())
            <div class="form-row">
                <div class="w-full">
                    <label class="form-label">Barangay</label>
                    <select name="barangay" class="form-input w-full">
                        <option value="" class="bold">Select Barangay</option>
                        @foreach(['Awang','Bagocboc','Barra','Bonbon','Cauyunan','Igpit','Limunda','Luyong Bonbon','Malanang','Nangcaon','Patag','Poblacion','Taboc','Tingalan'] as $b)
                            <option value="{{ $b }}" @selected(old('barangay', $user->barangay) === $b)>
                                {{ $b }}
                            </option>
                        @endforeach
                    </select>
                    @error('barangay') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>
            @endif

            <!-- PASSWORDS -->
            <div class="form-row">

                <!-- NEW PASSWORD -->
                <div class="flex-1 relative">
                    <label class="form-label">
                        New Password <span class="text-xs text-gray-400">(leave blank to keep current)</span>
                    </label>

                    <div class="password-wrapper">
                        <input id="password"
                               name="password"
                               type="password"
                               class="form-input w-full password-input"
                               onkeyup="checkCapsLock(event)"
                               onkeydown="checkCapsLock(event)">

                        <span class="password-eye"
                              onclick="togglePassword('password', this)"
                              title="Show/Hide">
                            🙈
                        </span>
                    </div>

                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="flex-1 relative">
                    <label class="form-label">Confirm Password</label>

                    <div class="password-wrapper">
                        <input id="password_confirmation"
                               name="password_confirmation"
                               type="password"
                               class="form-input w-full password-input">

                        <span class="password-eye"
                              onclick="togglePassword('password_confirmation', this)"
                              title="Show/Hide">
                            🙈
                        </span>
                    </div>
                </div>

            </div>

            <!-- CAPS LOCK TOAST -->
            <div id="caps-toast" class="caps-toast hidden">
                ⚠️ Caps Lock is ON
            </div>

        </form>
    </div>
</div>

<!-- CSS OVERRIDE -->
<style>
.youth-form {
    display: flex !important;
    flex-direction: column !important;
    gap: 1.25rem !important;
}

.form-row {
    display: flex !important;
    gap: 1rem !important;
    flex-wrap: wrap !important;
}

.form-label {
    font-size: .75rem !important;
    font-weight: 700 !important;
    letter-spacing: .08em !important;
    text-transform: uppercase !important;
    color: #6b7280 !important;
    margin-bottom: .35rem !important;
    display: block !important;
}

.form-input {
    padding: .6rem .75rem !important;
    border-radius: .5rem !important;
    border: 1px solid #d1d5db !important;
    background: white !important;
}

.form-input:focus {
    outline: none !important;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 2px rgba(99,102,241,.15) !important;
}

.password-wrapper {
    position: relative !important;
}

.password-input {
    padding-right: 3rem !important;
}

.password-eye {
    position: absolute !important;
    top: 50% !important;
    right: .75rem !important;
    transform: translateY(-50%) !important;
    cursor: pointer;
    user-select: none;
    font-size: 1.1rem;
    color: #6b7280;
}

.password-eye:hover {
    color: #4f46e5;
}

.error-text {
    font-size: .75rem !important;
    color: #dc2626 !important;
    margin-top: .25rem !important;
}

/* CAPS LOCK TOAST */
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
    /* background: white !important; */
    padding: .75rem 0 !important;
    margin-bottom: 1.5rem !important;
    /* border-bottom: 1px solid #e5e7eb !important; */
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
}

.cancel-btn {
    background: rgb(202, 0, 0) !important;
    color: #ffffff !important;
    border: 1px solid #e5e7eb !important;
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
    background: #f3f4f6 !important;
    color: #111 !important;
    text-decoration: none !important;
}
</style>

<!-- JS -->
<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
        el.textContent = "👁";
    } else {
        input.type = "password";
        el.textContent = "🙈";
    }
}

function checkCapsLock(e) {
    const toast = document.getElementById('caps-toast');
    if (e.getModifierState && e.getModifierState('CapsLock')) {
        toast.classList.remove('hidden');
        toast.classList.add('show');
    } else {
        toast.classList.remove('show');
        setTimeout(() => toast.classList.add('hidden'), 200);
    }
}
</script>
@endsection
