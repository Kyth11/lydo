@extends('layouts.app')
@section('page-title', 'Add Profile')
@section('page-desc', 'Complete KK Youth Information')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/youth-create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
@endpush

@section('content')
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        <div class="bg-white rounded-xl shadow p-6 relative">

            <!-- Sticky Save Button -->


            <div class="save-bar">
                <a href="{{ route('youth.index') }}" class="cancel-btn">
                    Cancel
                </a>
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
                        <img id="photoPreview" src="{{ asset('images/Avatar.png') }}" class="w-24 h-24 object-cover">
                    </div>

                    <label class="save-btn cursor-pointer text-sm">
                        📷 Take / Upload Photo
                        <input type="file" name="profile_photo" accept="image/jpeg,image/png" capture="environment"
                            hidden onchange="previewPhoto(event)">
                    </label>

                    <span class="text-xs text-gray-500">
                        JPG or PNG • Max 2MB
                    </span>

                </div>
                <div class="form-row">
                    <input name="first_name" class="form-input" placeholder="FIRST NAME" value="{{ old('first_name') }}" required>
                    <input name="middle_name" class="form-input" placeholder="MIDDLE NAME" value="{{ old('middle_name') }}">
                    <input name="last_name" class="form-input" placeholder="LAST NAME" value="{{ old('last_name') }}" required>
                </div>

                <div class="form-row">
                    <select name="sex" class="form-input" required>
                        <option value="" class="bold" {{ old('sex') ? '' : 'selected' }}>SEX</option>
                        <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>MALE</option>
                        <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>FEMALE</option>
                    </select>
                    <select name="gender" class="form-input" required>
                        <option value="" class="bold" {{ old('gender') ? '' : 'selected' }}>GENDER</option>
                        <option value="LGBTQAI+" {{ old('gender') == 'LGBTQAI+' ? 'selected' : '' }}>LGBTQAI+</option>
                        <option value="Prefer not to say" {{ old('gender') == 'Prefer not to say' ? 'selected' : '' }}>PREFER NOT TO SAY</option>
                    </select>

                    <input id="birthday" type="date" name="birthday" class="form-input" required
                        max="{{ now()->toDateString() }}" value="{{ old('birthday') }}">
                    <input id="age" type="number" name="age" class="form-input" placeholder="AGE" readonly value="{{ old('age') }}">


                    <select name="civil_status" class="form-input" required>
                        <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>
                            CIVIL STATUS
                        </option>

                        <option value="SINGLE" {{ old('civil_status') == 'SINGLE' ? 'selected' : '' }}>
                            SINGLE
                        </option>

                        <option value="MARRIED" {{ old('civil_status') == 'MARRIED' ? 'selected' : '' }}>
                            MARRIED
                        </option>

                        <option value="WIDOWED" {{ old('civil_status') == 'WIDOWED' ? 'selected' : '' }}>
                            WIDOWED
                        </option>

                        <option value="SEPARATED" {{ old('civil_status') == 'SEPARATED' ? 'selected' : '' }}>
                            SEPARATED
                        </option>

                        <option value="LIVE-IN" {{ old('civil_status') == 'LIVE-IN' ? 'selected' : '' }}>
                            LIVE-IN
                        </option>
                    </select>
                </div>

                <!-- Location (DEFAULT VALUES SET) -->
                <div class="form-row">
                    <input id="region" name="region" class="form-input" value="{{ old('region', 'NORTHERN MINDANAO') }}" required readonly>
                    <input id="province" name="province" class="form-input" value="{{ old('province', 'MISAMIS ORIENTAL') }}" required readonly>
                    <input id="municipality" name="municipality" class="form-input" value="{{ old('municipality', 'OPOL') }}" required readonly>
                </div>

                <!-- Barangay + Home Address -->
                @php
                    $user = auth()->user();
                @endphp

                <div class="form-row">
                    <select id="barangay" name="barangay" class="form-input" required
                        {{ $user && $user->role === 'sk' ? 'disabled' : '' }}>

                        <option value="" disabled class="bold" {{ old('barangay') ? '' : 'selected' }}>BARANGAY</option>

                        @foreach (['AWANG', 'BAGOCBOC', 'BARRA', 'BONBON', 'CAUYUNAN', 'IGPIT', 'LIMUNDA', 'LUYONG BONBON', 'MALANANG', 'NANGCAON', 'PATAG', 'POBLACION', 'TABOC', 'TINGALAN'] as $b)
                            <option value="{{ $b }}" 
                                @if ($user && $user->role === 'sk' && strtoupper($user->barangay) === $b) selected @endif
                                {{ strtoupper(old('barangay')) == $b ? 'selected' : '' }}>
                                {{ $b }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Hidden input so disabled select still submits --}}
                    @if ($user && $user->role === 'sk')
                        <input type="hidden" name="barangay" value="{{ $user->barangay }}">
                    @endif
                    <input id="purok_zone" name="purok_zone" class="form-input" placeholder="PUROK / ZONE (E.G. ZONE 1)"
                        required value="{{ old('purok_zone') }}">
                    <input id="home_address" name="home_address" class="form-input" placeholder="HOME ADDRESS" required
                        readonly value="{{ old('home_address') }}">
                </div>

                <div class="form-row">
                    <select name="religion" id="religionSelect" class="form-input" required>
                        <option value="" disabled class="bold" {{ old('religion') ? '' : 'selected' }}>RELIGION
                        </option>

                        <!-- Christian -->
                        <option value="ROMAN CATHOLIC" {{ old('religion') == 'ROMAN CATHOLIC' ? 'selected' : '' }}>ROMAN
                            CATHOLIC</option>
                        <option value="BAPTIST" {{ old('religion') == 'BAPTIST' ? 'selected' : '' }}>BAPTIST</option>
                        <option value="BORN AGAIN CHRISTIAN"
                            {{ old('religion') == 'BORN AGAIN CHRISTIAN' ? 'selected' : '' }}>BORN AGAIN CHRISTIAN</option>
                        <option value="IGLESIA NI CRISTO" {{ old('religion') == 'IGLESIA NI CRISTO' ? 'selected' : '' }}>
                            IGLESIA NI CRISTO</option>
                        <option value="SEVENTH-DAY ADVENTIST"
                            {{ old('religion') == 'SEVENTH-DAY ADVENTIST' ? 'selected' : '' }}>SEVENTH-DAY ADVENTIST
                        </option>
                        <option value="JEHOVAH'S WITNESSES"
                            {{ old('religion') == "JEHOVAH'S WITNESSES" ? 'selected' : '' }}>
                            JEHOVAH'S WITNESSES</option>
                        <option value="METHODIST" {{ old('religion') == 'METHODIST' ? 'selected' : '' }}>METHODIST
                        </option>
                        <option value="LUTHERAN" {{ old('religion') == 'LUTHERAN' ? 'selected' : '' }}>LUTHERAN</option>
                        <option value="ANGLICAN" {{ old('religion') == 'ANGLICAN' ? 'selected' : '' }}>ANGLICAN</option>
                        <option value="PENTECOSTAL" {{ old('religion') == 'PENTECOSTAL' ? 'selected' : '' }}>PENTECOSTAL
                        </option>
                        <option value="UNITED CHURCH OF CHRIST IN THE PHILIPPINES (UCCP)"
                            {{ old('religion') == 'UNITED CHURCH OF CHRIST IN THE PHILIPPINES (UCCP)' ? 'selected' : '' }}>
                            UNITED CHURCH OF CHRIST IN THE PHILIPPINES (UCCP)
                        </option>

                        <!-- Non-Christian -->
                        <option value="ISLAM" {{ old('religion') == 'ISLAM' ? 'selected' : '' }}>ISLAM</option>

                        <!-- Others -->
                        <option value="OTHERS" {{ old('religion') == 'OTHERS' ? 'selected' : '' }}>OTHERS (SPECIFY)
                        </option>
                    </select>

                    <input type="text" name="religion_other" id="otherReligionInput" class="form-input"
                        placeholder="PLEASE SPECIFY RELIGION" value="{{ old('religion_other') }}" style="display:none;">
                </div>

                <select name="education" class="form-input" required>
                    <option disabled value="" class="bold" {{ old('education') ? '' : 'selected' }}>EDUCATION LAST ATTENDED</option>
                    <option value="ELEMENTARY LEVEL" {{ old('education') == 'ELEMENTARY LEVEL' ? 'selected' : '' }}>ELEMENTARY LEVEL</option>
                    <option value="ELEMENTARY GRADUATE" {{ old('education') == 'ELEMENTARY GRADUATE' ? 'selected' : '' }}>ELEMENTARY GRADUATE</option>
                    <option value="HIGH SCHOOL LEVEL" {{ old('education') == 'HIGH SCHOOL LEVEL' ? 'selected' : '' }}>HIGH SCHOOL LEVEL</option>
                    <option value="HIGH SCHOOL GRADUATE" {{ old('education') == 'HIGH SCHOOL GRADUATE' ? 'selected' : '' }}>HIGH SCHOOL GRADUATE</option>
                    <option value="COLLEGE LEVEL" {{ old('education') == 'COLLEGE LEVEL' ? 'selected' : '' }}>COLLEGE LEVEL</option>
                    <option value="COLLEGE GRADUATE" {{ old('education') == 'COLLEGE GRADUATE' ? 'selected' : '' }}>COLLEGE GRADUATE</option>
                    <option value="VOCATIONAL" {{ old('education') == 'VOCATIONAL' ? 'selected' : '' }}>VOCATIONAL</option>
                </select>

                <div class="form-row">
                    <label>ARE YOU A REGISTERED SK VOTER? </label>
                    <label><input type="checkbox" name="is_sk_voter" value="Yes" {{ old('is_sk_voter') == 'Yes' ? 'checked' : '' }}> YES</label>
                    <label><input type="checkbox" name="is_sk_voter" value="No" {{ old('is_sk_voter') == 'No' ? 'checked' : '' }}> NO</label>
                </div>

                <div class="form-row">
                    <label>YOUTH CLASSIFICATION: </label>
                    <label><input type="checkbox" name="is_osy" {{ old('is_osy') ? 'checked' : '' }}> OUT-OF-SCHOOL YOUTH</label>
                    <label><input type="checkbox" name="is_isy" {{ old('is_isy') ? 'checked' : '' }}> IN-SCHOOL YOUTH</label>
                    <label><input type="checkbox" name="is_4ps" {{ old('is_4ps') ? 'checked' : '' }}> 4PS</label>
                    <label><input type="checkbox" name="is_ip" {{ old('is_ip') ? 'checked' : '' }}> INDIGENOUS PEOPLE IP</label>
                    <label><input type="checkbox" name="is_pwd" {{ old('is_pwd') ? 'checked' : '' }}> PERSON WITH DISABILITY PWD</label>

                </div>

                <div class="form-row">
                    <label> WORK Classification: </label>
                    <label><input type="checkbox" name="is_unemployed" {{ old('is_unemployed') ? 'checked' : '' }}> UNEMPLOYED YOUTH</label>
                    <label><input type="checkbox" name="is_employed" {{ old('is_employed') ? 'checked' : '' }}> EMPLOYED YOUTH</label>
                    <label><input type="checkbox" name="is_self_employed" {{ old('is_self_employed') ? 'checked' : '' }}> SELF-EMPLOYED YOUTH</label>
                </div>

                <div class="form-row">
                    <!-- Skills Input -->
                    <input type="text" name="skills" class="form-input" placeholder="SKILLS"
                        value="{{ old('skills') }}" required>

                    <!-- Preferred Skills Dropdown -->
                    <select name="preferred_skills" id="preferredSkillsSelect" class="form-input" required>
                        <option value="" disabled class="bold" {{ old('preferred_skills') ? '' : 'selected' }}>
                            PREFERRED SKILLS
                        </option>

                        <option value="HOUSEKEEPING" {{ old('preferred_skills') == 'HOUSEKEEPING' ? 'selected' : '' }}>
                            HOUSEKEEPING
                        </option>
                        <option value="BREAD & PASTRIES PRODUCTION"
                            {{ old('preferred_skills') == 'BREAD & PASTRIES PRODUCTION' ? 'selected' : '' }}>BREAD &
                            PASTRIES
                            PRODUCTION</option>
                        <option value="DRIVING" {{ old('preferred_skills') == 'DRIVING' ? 'selected' : '' }}>DRIVING
                        </option>
                        <option value="AUTOMOTIVE SERVICING"
                            {{ old('preferred_skills') == 'AUTOMOTIVE SERVICING' ? 'selected' : '' }}>AUTOMOTIVE SERVICING
                        </option>
                        <option value="BOOKKEEPING" {{ old('preferred_skills') == 'BOOKKEEPING' ? 'selected' : '' }}>
                            BOOKKEEPING
                        </option>
                        <option value="ELECTRICAL INSTALLATION & MAINTENANCE"
                            {{ old('preferred_skills') == 'ELECTRICAL INSTALLATION & MAINTENANCE' ? 'selected' : '' }}>
                            ELECTRICAL
                            INSTALLATION & MAINTENANCE</option>
                        <option value="PLUMBING" {{ old('preferred_skills') == 'PLUMBING' ? 'selected' : '' }}>PLUMBING
                        </option>
                        <option value="SHIELDED METAL ARC WELDING SMAW"
                            {{ old('preferred_skills') == 'SHIELDED METAL ARC WELDING SMAW' ? 'selected' : '' }}>SHIELDED
                            METAL ARC
                            WELDING SMAW</option>
                        <option value="TILE SETTING" {{ old('preferred_skills') == 'TILE SETTING' ? 'selected' : '' }}>
                            TILE
                            SETTING
                        </option>
                        <option value="FOOD & BEVERAGE SERVICES"
                            {{ old('preferred_skills') == 'FOOD & BEVERAGE SERVICES' ? 'selected' : '' }}>FOOD & BEVERAGE
                            SERVICES
                        </option>
                        <option value="COMPUTER SYSTEM SERVICING"
                            {{ old('preferred_skills') == 'COMPUTER SYSTEM SERVICING' ? 'selected' : '' }}>COMPUTER SYSTEM
                            SERVICING
                        </option>
                        <option value="CARPENTRY" {{ old('preferred_skills') == 'CARPENTRY' ? 'selected' : '' }}>CARPENTRY
                        </option>
                        <option value="MASONRY" {{ old('preferred_skills') == 'MASONRY' ? 'selected' : '' }}>MASONRY
                        </option>
                        <option value="BARISTA" {{ old('preferred_skills') == 'BARISTA' ? 'selected' : '' }}>BARISTA
                        </option>
                        <option value="MASSAGE THERAPIST"
                            {{ old('preferred_skills') == 'MASSAGE THERAPIST' ? 'selected' : '' }}>
                            MASSAGE THERAPIST</option>
                        <option value="CAREGIVING" {{ old('preferred_skills') == 'CAREGIVING' ? 'selected' : '' }}>
                            CAREGIVING
                        </option>
                        <option value="DRESSMAKING" {{ old('preferred_skills') == 'DRESSMAKING' ? 'selected' : '' }}>
                            DRESSMAKING
                        </option>
                        <option value="TAILORING" {{ old('preferred_skills') == 'TAILORING' ? 'selected' : '' }}>TAILORING
                        </option>

                        <!-- Others -->
                        <option value="OTHERS" {{ old('preferred_skills') == 'OTHERS' ? 'selected' : '' }}>
                            OTHERS (SPECIFY)
                        </option>
                    </select>

                    <!-- Others Input -->
                    <input type="text" name="preferred_skills_other" id="otherPreferredSkillInput" class="form-input"
                        placeholder="PLEASE SPECIFY PREFERRED SKILL" value="{{ old('preferred_skills_other') }}"
                        style="{{ old('preferred_skills') == 'OTHERS' ? '' : 'display:none;' }}">

                    <input type="text" name="source_of_income" class="form-input" placeholder="SOURCE OF INCOME" value="{{ old('source_of_income') }}">
                    <input type="number" name="contact_number" class="form-input" placeholder="CONTACT NUMBER" value="{{ old('contact_number') }}">
                </div>


                <!-- II. Family Composition -->
                <h4 class="bold mt-6 mb-2">II. FAMILY COMPOSITION</h4>

                <div class="family-wrapper">
                    <table class="family-table">
                        <thead>
                            <tr>
                                <th>FAMILY MEMBER</th>
                                <th>AGE</th>
                                <th>RELATIONSHIP</th>
                                <th>EDUCATIONAL ATTAINMENT</th>
                                <th>OCCUPATION</th>
                                <th>INCOME</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="familyBody">
                            <tr>
                                <td>
                                    <input class="form-input" name="family_members[0][name]" placeholder="FULL NAME" value="{{ old('family_members.0.name') }}">
                                </td>

                                <td>
                                    <input type="number" class="form-input" name="family_members[0][age]"
                                        min="1" max="99" inputmode="numeric"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')" value="{{ old('family_members.0.age') }}">
                                </td>

                                <td>
                                    <select name="family_members[0][relationship]" class="form-input">
                                        <option value="" disabled {{ old('family_members.0.relationship') ? '' : 'selected' }}>RELATIONSHIP</option>
                                        <option value="MOTHER" {{ old('family_members.0.relationship') == 'MOTHER' ? 'selected' : '' }}>MOTHER</option>
                                        <option value="FATHER" {{ old('family_members.0.relationship') == 'FATHER' ? 'selected' : '' }}>FATHER</option>
                                        <option value="BROTHER" {{ old('family_members.0.relationship') == 'BROTHER' ? 'selected' : '' }}>BROTHER</option>
                                        <option value="SISTER" {{ old('family_members.0.relationship') == 'SISTER' ? 'selected' : '' }}>SISTER</option>
                                        <option value="GRANDPARENT" {{ old('family_members.0.relationship') == 'GRANDPARENT' ? 'selected' : '' }}>GRANDPARENT</option>
                                        <option value="AUNT" {{ old('family_members.0.relationship') == 'AUNT' ? 'selected' : '' }}>AUNT</option>
                                        <option value="UNCLE" {{ old('family_members.0.relationship') == 'UNCLE' ? 'selected' : '' }}>UNCLE</option>
                                        <option value="COUSIN" {{ old('family_members.0.relationship') == 'COUSIN' ? 'selected' : '' }}>COUSIN</option>
                                        <option value="SPOUSE" {{ old('family_members.0.relationship') == 'SPOUSE' ? 'selected' : '' }}>SPOUSE</option>
                                    </select>
                                </td>

                                <td>
                                    <select name="family_members[0][education]" class="form-input">
                                        <option value="" disabled {{ old('family_members.0.education') ? '' : 'selected' }}>EDUCATION</option>
                                        <option value="NONE" {{ old('family_members.0.education') == 'NONE' ? 'selected' : '' }}>NONE</option>
                                        <option value="PRE-SCHOOL" {{ old('family_members.0.education') == 'PRE-SCHOOL' ? 'selected' : '' }}>PRE-SCHOOL</option>
                                        <option value="KINDERGARTEN" {{ old('family_members.0.education') == 'KINDERGARTEN' ? 'selected' : '' }}>KINDERGARTEN</option>
                                        <option value="ELEMENTARY LEVEL" {{ old('family_members.0.education') == 'ELEMENTARY LEVEL' ? 'selected' : '' }}>ELEMENTARY LEVEL</option>
                                        <option value="ELEMENTARY GRADUATE" {{ old('family_members.0.education') == 'ELEMENTARY GRADUATE' ? 'selected' : '' }}>ELEMENTARY GRADUATE</option>
                                        <option value="HIGH SCHOOL LEVEL" {{ old('family_members.0.education') == 'HIGH SCHOOL LEVEL' ? 'selected' : '' }}>HIGH SCHOOL LEVEL</option>
                                        <option value="HIGH SCHOOL GRADUATE" {{ old('family_members.0.education') == 'HIGH SCHOOL GRADUATE' ? 'selected' : '' }}>HIGH SCHOOL GRADUATE</option>
                                        <option value="COLLEGE LEVEL" {{ old('family_members.0.education') == 'COLLEGE LEVEL' ? 'selected' : '' }}>COLLEGE LEVEL</option>
                                        <option value="COLLEGE GRADUATE" {{ old('family_members.0.education') == 'COLLEGE GRADUATE' ? 'selected' : '' }}>COLLEGE GRADUATE</option>
                                        <option value="VOCATIONAL" {{ old('family_members.0.education') == 'VOCATIONAL' ? 'selected' : '' }}>VOCATIONAL</option>
                                    </select>
                                </td>

                                <td>
                                    <input class="form-input" name="family_members[0][occupation]"
                                        placeholder="OCCUPATION" value="{{ old('family_members.0.occupation') }}">
                                </td>

                                <td>
                                    <input type="number" class="form-input" name="family_members[0][income]"
                                        min="0" step="1" inputmode="numeric"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                        placeholder="MONTHLY INCOME" value="{{ old('family_members.0.income') }}">
                                </td>

                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="button" id="addFamilyRow" class="save-btn mt-3">
                        + ADD FAMILY MEMBER
                    </button>
                </div>

                <!-- ATTACHMENTS -->
                <h4 class="bold mt-6">III. ATTACHMENTS</h4>

                <div class="attachment-wrapper">

                    <!-- File Input -->
                    <input type="file" name="attachments[]" id="attachments" multiple
                        accept="image/jpeg,image/png,image/jpg" class="form-input">

                    <small class="text-gray-500">
                        UPLOAD MULTIPLE JPG/PNG IMAGES (MAX 4MB EACH)
                    </small>

                    <!-- Preview Grid -->
                    <div id="attachmentPreview" class="attachment-preview-grid"></div>

                </div>

                <!-- DATA PRIVACY CONSENT -->
                <div class="form-row items-center mt-6 !important">
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" id="privacyConsent" class="mt-1" required {{ old('privacyConsent') ? 'checked' : '' }}>

                        <span>
                            I AGREE TO THE
                            <a href="javascript:void(0)" id="openPrivacyModal"
                                class="text-indigo-600 font-semibold underline">
                                TERMS & CONDITIONS AND DATA PRIVACY CONSENT
                            </a>
                        </span>
                    </label>
                </div>
            </form>
            <!-- PRIVACY MODAL -->
            <div id="privacyModal" class="privacy-overlay">

                <div class="privacy-modal">

                    <!-- Header -->
                    <div class="privacy-header">
                        <h3>
                            Local Youth Development Office<br>
                            <span>Data Privacy Consent & Terms of Agreement</span>
                        </h3>
                        <button id="closePrivacyModal" class="close-btn">&times;</button>
                    </div>

                    <!-- Body -->
                    <div class="privacy-body">

                        <p class="bold">
                            REPUBLIC OF THE PHILIPPINES<br>
                            MUNICIPALITY OF OPOL<br>
                            LOCAL YOUTH DEVELOPMENT OFFICE (LYDO)
                        </p>

                        <p>
                            In compliance with the <strong>Data Privacy Act of 2012 (RA 10173)</strong>,
                            the Local Youth Development Office ensures that all personal information
                            collected through this Youth Profiling System shall be handled with
                            utmost confidentiality and security.
                        </p>

                        <p>
                            By proceeding, you voluntarily provide your personal information for
                            official government purposes, including but not limited to:
                        </p>

                        <ul>
                            <li>Youth profiling and demographic analysis</li>
                            <li>Program planning and policy development</li>
                            <li>Delivery of youth-related services and interventions</li>
                        </ul>

                        <p>The data collected may include:</p>

                        <ul>
                            <li>Personal identification details</li>
                            <li>Contact and address information</li>
                            <li>Educational and employment background</li>
                            <li>Household and family composition</li>
                            <li>Skills, interests, and affiliations</li>
                        </ul>

                        <p>
                            All collected data shall be stored securely and accessed only by
                            authorized personnel. No information shall be disclosed without
                            lawful basis or your consent, except as required by law.
                        </p>

                        <p>

                        <p>
                            ALL COLLECTED DATA SHALL BE STORED SECURELY AND ACCESSED ONLY BY
                            AUTHORIZED PERSONNEL. NO INFORMATION SHALL BE DISCLOSED WITHOUT
                            LAWFUL BASIS OR YOUR CONSENT, EXCEPT AS REQUIRED BY LAW.
                        </p>

                        <p>
                            YOU HAVE THE RIGHT TO ACCESS, CORRECT, OR REQUEST THE DELETION OF
                            YOUR PERSONAL DATA, SUBJECT TO APPLICABLE LAWS AND REGULATIONS.
                        </p>

                        <p class="bold">
                            BY CLICKING “I AGREE”, YOU CONFIRM THAT YOU HAVE READ, UNDERSTOOD,
                            AND VOLUNTARILY CONSENT TO THE COLLECTION AND PROCESSING OF YOUR
                            PERSONAL DATA.
                        </p>

                    </div>

                    <!-- Footer -->
                    <div class="privacy-footer">
                        <button id="agreePrivacy" class="agree-btn">I AGREE</button>
                        <button id="closePrivacyModalBtn" class="close-secondary">CLOSE</button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textInputs = document.querySelectorAll('input:not([type="number"]):not([type="date"]):not([type="file"]):not([type="checkbox"]):not([type="radio"]):not([type="hidden"])');
            textInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            });
        });
    </script>
    @endpush
@endsection
