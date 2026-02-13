@extends('layouts.app')

@section('page-title', 'Edit Account')
@section('page-desc', 'Update your account details')

@section('content')
<div class="max-w-6xl mx-auto px-4 space-y-6">

    <!-- FORM CARD -->
    <div class="bg-white rounded-xl shadow p-6 relative">

        <!-- STICKY SAVE BAR -->
        <div class="save-bar">
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
                    <input
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        class="form-input w-full"
                        required
                    >
                    @error('name')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- EMAIL -->
            <div class="form-row">
                <div class="w-full">
                    <label class="form-label">Email Address</label>
                    <input
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        class="form-input w-full"
                        required
                    >
                    @error('email')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- BARANGAY -->
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
                    @error('barangay')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- PASSWORD -->
            <div class="form-row">
                <div class="flex-1">
                    <label class="form-label">
                        New Password <span class="text-xs text-gray-400">(leave blank to keep current)</span>
                    </label>
                    <input
                        name="password"
                        type="password"
                        class="form-input w-full"
                    >
                    @error('password')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex-1">
                    <label class="form-label">Confirm Password</label>
                    <input
                        name="password_confirmation"
                        type="password"
                        class="form-input w-full"
                    >
                </div>
            </div>

        </form>
    </div>
</div>

<!-- CSS OVERRIDE (MATCH ADD PROFILE DESIGN) -->
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
    flex: 1 1 0 !important;
    min-width: 200px !important;
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

.error-text {
    font-size: .75rem !important;
    color: #dc2626 !important;
    margin-top: .25rem !important;
}

.bold {
    font-weight: 700 !important;
}

/* 🔥 STICKY SAVE BAR */
.save-bar {
    position: sticky !important;
    top: 5.5rem !important; /* below navbar */
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
</style>
@endsection
