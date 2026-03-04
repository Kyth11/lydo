@extends('layouts.app')
@section('page-title', 'Add Profile')
@section('page-desc', 'Complete KK Youth Information')

@section('content')
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        <div class="bg-white rounded-xl shadow p-6 relative">

            <!-- Sticky Save Button -->
            <div class="save-bar">
                <button form="youthForm" type="submit" class="save-btn">
                    Save Profile
                </button>
            </div>

            <form id="youthForm" method="POST" action="/youth" enctype="multipart/form-data" class="youth-form">
                @csrf

                <!-- I. Identifying Information -->
                <h4 class="bold">I. Identifying Information</h4>

                <!-- PROFILE PHOTO -->
                <div class="flex flex-col items-center gap-3 mb-6">

                    <div class="relative w-24 h-24 rounded-full overflow-hidden border-2 border-indigo-500">
                        <img id="photoPreview" src="{{ asset('images/avatar.png') }}" class="w-24 h-24 object-cover">
                    </div>

                    <label class="save-btn cursor-pointer text-sm">
                        📷 Take / Upload Photo
                        <input type="file" name="profile_photo" accept="image/jpeg,image/png" capture="environment" hidden
                            onchange="previewPhoto(event)">
                    </label>

                    <span class="text-xs text-gray-500">
                        JPG or PNG • Max 2MB
                    </span>

                </div>
                <div class="form-row">
                    <input name="first_name" class="form-input" placeholder="First Name" required>
                    <input name="middle_name" class="form-input" placeholder="Middle Name">
                    <input name="last_name" class="form-input" placeholder="Last Name" required>
                </div>

                <div class="form-row">
                    <select name="sex" class="form-input" required>
                        <option value="" class="bold">Sex</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                    <select name="gender" class="form-input" required>
                        <option value="" class="bold">Gender</option>
                        <option>LGBTQAI+</option>
                        <option>Prefer not to say</option>
                    </select>

                    <input id="birthday" type="date" name="birthday" class="form-input" required
                        max="{{ now()->toDateString() }}">
                    <input id="age" type="number" name="age" class="form-input" placeholder="Age" readonly>


                    <select name="civil_status" class="form-input" required>
                        <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>
                            Civil Status
                        </option>

                        <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>
                            Single
                        </option>

                        <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>
                            Married
                        </option>

                        <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>
                            Widowed
                        </option>

                        <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>
                            Separated
                        </option>

                        <option value="Live-in" {{ old('civil_status') == 'Live-in' ? 'selected' : '' }}>
                            Live-in
                        </option>
                    </select>
                </div>

                <!-- Location (DEFAULT VALUES SET) -->
                <div class="form-row">
                    <input id="region" name="region" class="form-input" value="Northern Mindanao" required>
                    <input id="province" name="province" class="form-input" value="Misamis Oriental" required>
                    <input id="municipality" name="municipality" class="form-input" value="Opol" required>
                </div>

                <!-- Barangay + Home Address -->
                @php
                    $user = auth()->user();
                @endphp

                <div class="form-row">
                    <select id="barangay" name="barangay" class="form-input" required {{ $user && $user->role === 'sk' ? 'disabled' : '' }}>

                        <option value="" disabled class="bold">Barangay</option>

                        @foreach (['Awang', 'Bagocboc', 'Barra', 'Bonbon', 'Cauyunan', 'Igpit', 'Limunda', 'Luyong Bonbon', 'Malanang', 'Nangcaon', 'Patag', 'Poblacion', 'Taboc', 'Tingalan'] as $b)
                            <option value="{{ $b }}" @if ($user && $user->role === 'sk' && $user->barangay === $b) selected @endif>
                                {{ $b }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Hidden input so disabled select still submits --}}
                    @if ($user && $user->role === 'sk')
                        <input type="hidden" name="barangay" value="{{ $user->barangay }}">
                    @endif
                    <input id="purok_zone" name="purok_zone" class="form-input" placeholder="Purok / Zone (e.g. Zone 1)"
                        required>
                    <input id="home_address" name="home_address" class="form-input" placeholder="Home Address" required
                        readonly>
                </div>

                <div class="form-row">
                    <select name="religion" id="religionSelect" class="form-input" required>
                        <option value="" disabled class="bold" {{ old('religion') ? '' : 'selected' }}>Religion
                        </option>

                        <!-- Christian -->
                        <option value="Roman Catholic" {{ old('religion') == 'Roman Catholic' ? 'selected' : '' }}>Roman
                            Catholic</option>
                        <option value="Baptist" {{ old('religion') == 'Baptist' ? 'selected' : '' }}>Baptist</option>
                        <option value="Born Again Christian" {{ old('religion') == 'Born Again Christian' ? 'selected' : '' }}>Born Again Christian</option>
                        <option value="Iglesia ni Cristo" {{ old('religion') == 'Iglesia ni Cristo' ? 'selected' : '' }}>
                            Iglesia ni Cristo</option>
                        <option value="Seventh-day Adventist" {{ old('religion') == 'Seventh-day Adventist' ? 'selected' : '' }}>Seventh-day Adventist
                        </option>
                        <option value="Jehovah's Witnesses" {{ old('religion') == "Jehovah's Witnesses" ? 'selected' : '' }}>
                            Jehovah's Witnesses</option>
                        <option value="Methodist" {{ old('religion') == 'Methodist' ? 'selected' : '' }}>Methodist
                        </option>
                        <option value="Lutheran" {{ old('religion') == 'Lutheran' ? 'selected' : '' }}>Lutheran</option>
                        <option value="Anglican" {{ old('religion') == 'Anglican' ? 'selected' : '' }}>Anglican</option>
                        <option value="Pentecostal" {{ old('religion') == 'Pentecostal' ? 'selected' : '' }}>Pentecostal
                        </option>
                        <option value="United Church of Christ in the Philippines (UCCP)" {{ old('religion') == 'United Church of Christ in the Philippines (UCCP)' ? 'selected' : '' }}>
                            United Church of Christ in the Philippines (UCCP)
                        </option>

                        <!-- Non-Christian -->
                        <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>

                        <!-- Others -->
                        <option value="Others" {{ old('religion') == 'Others' ? 'selected' : '' }}>Others (Specify)
                        </option>
                    </select>

                    <input type="text" name="religion_other" id="otherReligionInput" class="form-input"
                        placeholder="Please specify religion" value="{{ old('religion_other') }}" style="display:none;">
                </div>

                <select name="education" class="form-input" required>
                    <option disabled value="" class="bold">Education Last Attended</option>
                    <option>Elementary Level</option>
                    <option>Elementary Graduate</option>
                    <option>High School Level</option>
                    <option>High School Graduate</option>
                    <option>College Level</option>
                    <option>College Graduate</option>
                    <option>Vocational</option>
                </select>

                <div class="form-row">
                    <label>Are you a Registered Sk Voter? </label>
                    <label><input type="checkbox" name="is_sk_voter" value="Yes"> Yes</label>
                    <label><input type="checkbox" name="is_sk_voter" value="No"> No</label>
                </div>

                <div class="form-row">
                    <label>Youth Classification: </label>
                    <label><input type="checkbox" name="is_osy"> Out-of-School Youth</label>
                    <label><input type="checkbox" name="is_isy"> In-School Youth</label>
                    <label><input type="checkbox" name="is_4ps"> 4Ps</label>
                    <label><input type="checkbox" name="is_ip"> Indigenous People IP</label>
                    <label><input type="checkbox" name="is_pwd"> Person with Disability PWD</label>

                </div>

                <div class="form-row">
                    <label> Work Classification: </label>
                    <label><input type="checkbox" name="is_unemployed"> Unemployed Youth</label>
                    <label><input type="checkbox" name="is_employed"> Employed Youth</label>
                    <label><input type="checkbox" name="is_self_employed"> Self-Employed Youth</label>
                </div>

                <div class="form-row">
                    <!-- Skills Input -->
                    <input type="text" name="skills" class="form-input" placeholder="Skills" value="{{ old('skills') }}"
                        required>

                    <!-- Preferred Skills Dropdown -->
                    <select name="preferred_skills" id="preferredSkillsSelect" class="form-input" required>
                        <option value="" disabled class="bold" {{ old('preferred_skills') ? '' : 'selected' }}>
                            Preferred Skills
                        </option>

                        <option value="Housekeeping" {{ old('preferred_skills') == 'Housekeeping' ? 'selected' : '' }}>
                            Housekeeping
                        </option>
                        <option value="Bread & Pastries Production" {{ old('preferred_skills') == 'Bread & Pastries Production' ? 'selected' : '' }}>Bread &
                            Pastries
                            Production</option>
                        <option value="Driving" {{ old('preferred_skills') == 'Driving' ? 'selected' : '' }}>Driving
                        </option>
                        <option value="Automotive Servicing" {{ old('preferred_skills') == 'Automotive Servicing' ? 'selected' : '' }}>Automotive Servicing
                        </option>
                        <option value="Bookkeeping" {{ old('preferred_skills') == 'Bookkeeping' ? 'selected' : '' }}>
                            Bookkeeping
                        </option>
                        <option value="Electrical Installation & Maintenance" {{ old('preferred_skills') == 'Electrical Installation & Maintenance' ? 'selected' : '' }}>
                            Electrical
                            Installation & Maintenance</option>
                        <option value="Plumbing" {{ old('preferred_skills') == 'Plumbing' ? 'selected' : '' }}>Plumbing
                        </option>
                        <option value="Shielded Metal Arc Welding SMAW" {{ old('preferred_skills') == 'Shielded Metal Arc Welding SMAW' ? 'selected' : '' }}>Shielded
                            Metal Arc
                            Welding SMAW</option>
                        <option value="Tile Setting" {{ old('preferred_skills') == 'Tile Setting' ? 'selected' : '' }}>
                            Tile
                            Setting
                        </option>
                        <option value="Food & Beverage Services" {{ old('preferred_skills') == 'Food & Beverage Services' ? 'selected' : '' }}>Food & Beverage
                            Services
                        </option>
                        <option value="Computer System Servicing" {{ old('preferred_skills') == 'Computer System Servicing' ? 'selected' : '' }}>Computer System
                            Servicing
                        </option>
                        <option value="Carpentry" {{ old('preferred_skills') == 'Carpentry' ? 'selected' : '' }}>Carpentry
                        </option>
                        <option value="Masonry" {{ old('preferred_skills') == 'Masonry' ? 'selected' : '' }}>Masonry
                        </option>
                        <option value="Barista" {{ old('preferred_skills') == 'Barista' ? 'selected' : '' }}>Barista
                        </option>
                        <option value="Massage Therapist" {{ old('preferred_skills') == 'Massage Therapist' ? 'selected' : '' }}>
                            Massage Therapist</option>
                        <option value="Caregiving" {{ old('preferred_skills') == 'Caregiving' ? 'selected' : '' }}>
                            Caregiving
                        </option>
                        <option value="Dressmaking" {{ old('preferred_skills') == 'Dressmaking' ? 'selected' : '' }}>
                            Dressmaking
                        </option>
                        <option value="Tailoring" {{ old('preferred_skills') == 'Tailoring' ? 'selected' : '' }}>Tailoring
                        </option>

                        <!-- Others -->
                        <option value="Others" {{ old('preferred_skills') == 'Others' ? 'selected' : '' }}>
                            Others (Specify)
                        </option>
                    </select>

                    <!-- Others Input -->
                    <input type="text" name="preferred_skills_other" id="otherPreferredSkillInput" class="form-input"
                        placeholder="Please specify preferred skill" value="{{ old('preferred_skills_other') }}"
                        style="{{ old('preferred_skills') == 'Others' ? '' : 'display:none;' }}">

                    <input type="text" name="source_of_income" class="form-input" placeholder="Source of Income">
                    <input type="number" name="contact_number" class="form-input" placeholder="Contact Number">
                </div>


                <!-- II. Family Composition -->
                <h4 class="bold mt-6 mb-2">II. Family Composition</h4>

                <div class="family-wrapper">
                    <table class="family-table">
                        <thead>
                            <tr>
                                <th>Family Member</th>
                                <th>Age</th>
                                <th>Relationship</th>
                                <th>Educational Attainment</th>
                                <th>Occupation</th>
                                <th>Income</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="familyBody">
                            <tr>
                                <td>
                                    <input class="form-input" name="family_members[0][name]" placeholder="Full Name">
                                </td>

                                <td>
                                    <input type="number" class="form-input" name="family_members[0][age]" min="1" max="99"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                </td>

                                <td>
                                    <select name="family_members[0][relationship]" class="form-input">
                                        <option value="" disabled selected>Relationship</option>
                                        <option>Mother</option>
                                        <option>Father</option>
                                        <option>Brother</option>
                                        <option>Sister</option>
                                        <option>Grandparent</option>
                                        <option>Aunt</option>
                                        <option>Uncle</option>
                                        <option>Cousin</option>
                                        <option>Spouse</option>
                                    </select>
                                </td>

                                <td>
                                    <select name="family_members[0][education]" class="form-input">
                                        <option value="" disabled selected>Education</option>
                                        <option>None</option>
                                        <option>Pre-School</option>
                                        <option>Kindergarten</option>
                                        <option>Elementary Level</option>
                                        <option>Elementary Graduate</option>
                                        <option>High School Level</option>
                                        <option>High School Graduate</option>
                                        <option>College Level</option>
                                        <option>College Graduate</option>
                                        <option>Vocational</option>
                                    </select>
                                </td>

                                <td>
                                    <input class="form-input" name="family_members[0][occupation]" placeholder="Occupation">
                                </td>

                                <td>
                                    <input type="number" class="form-input" name="family_members[0][income]" min="0"
                                        step="1" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                        placeholder="Monthly Income">
                                </td>

                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="button" id="addFamilyRow" class="save-btn mt-3">
                        + Add Family Member
                    </button>
                </div>
                <!-- DATA PRIVACY CONSENT -->
                <div class="form-row items-center mt-6 !important">
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" id="privacyConsent" class="mt-1" required>

                        <span>
                            I agree to the
                            <a href="javascript:void(0)" id="openPrivacyModal"
                                class="text-indigo-600 font-semibold underline">
                                Terms & Conditions and Data Privacy Consent
                            </a>
                        </span>
                    </label>
                </div>
            </form>
            <!-- PRIVACY MODAL -->
            <div id="privacyModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center px-4">

                <div class="bg-white rounded-xl shadow-lg max-w-3xl w-full max-h-[85vh] overflow-hidden">

                    <!-- Modal Header -->
                    <div class="flex justify-between items-center px-6 py-4 border-b">
                        <h3 class="text-lg font-bold">Terms & Conditions & Data Privacy Consent</h3>
                        <button id="closePrivacyModal" class="text-gray-500 hover:text-black text-xl">
                            &times;
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4 overflow-y-auto text-sm leading-relaxed space-y-4">

                        <p>
                            This form collects personal information in accordance with the
                            <strong>Data Privacy Act of 2012 (Republic Act No. 10173)</strong>.
                        </p>

                        <p>
                            By submitting this form, you voluntarily provide accurate and truthful
                            information for youth profiling, planning, and program development purposes
                            of the Local Youth Development Office.
                        </p>

                        <p>
                            The collected data may include but is not limited to:
                        </p>

                        <ul class="list-disc ml-6 space-y-1">
                            <li>Personal identification details</li>
                            <li>Contact and address information</li>
                            <li>Educational, employment, and skills data</li>
                            <li>Household and family composition</li>
                        </ul>

                        <p>
                            All information shall be treated with strict confidentiality and will only
                            be accessed by authorized personnel. Data will not be shared without lawful
                            basis and shall be stored securely.
                        </p>

                        <p>
                            You have the right to access, correct, and request deletion of your data,
                            subject to legal and administrative requirements.
                        </p>

                        <p class="font-semibold">
                            By clicking “I Agree” and submitting this form, you confirm that you have
                            read, understood, and consent to the collection and processing of your
                            personal data.
                        </p>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button id="closePrivacyModalBtn" class="px-4 py-2 rounded-lg border border-gray-300 text-sm">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/youth-create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
    <script src="{{ asset('js/youth-create.js') }}" defer></script>
@endsection
