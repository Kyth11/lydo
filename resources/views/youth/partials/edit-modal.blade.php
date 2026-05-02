<div id="editModal" class="modal-overlay" style="display:none;">
    <div class="modal-content animated-modal">

        <!-- SAVE BAR -->
        <div class="save-bar">
            <div style="display:flex; gap:.75rem;">
                <button type="button" class="cancel-btn" onclick="closeEditModal()">
                    Cancel
                </button>
                <button form="editForm" type="submit" class="save-btn">
                    Update Profile
                </button>
            </div>
        </div>

        <form id="editForm" method="POST" action="" enctype="multipart/form-data" class="youth-form">
            @csrf
            @method('PUT')

            <!-- I. IDENTIFYING INFORMATION -->
            <h4 class="bold">I. Identifying Information</h4>

            <!-- PROFILE PHOTO -->
            <div class="flex flex-col items-center gap-3 mb-6">
                <div class="profile-photo-wrapper">
                    <img id="editPhotoPreview" src="{{ asset('images/Avatar.png') }}">
                </div>

                <label class="save-btn cursor-pointer text-sm">
                    📷 Change Photo
                    <input type="file" name="profile_photo" accept="image/jpeg,image/png" hidden
                        onchange="previewEditPhoto(event)">
                </label>
            </div>

            <!-- NAME -->
            <div class="form-row">
                <input id="edit_first_name" name="first_name" class="form-input" placeholder="FIRST NAME" required>
                <input id="edit_middle_name" name="middle_name" class="form-input" placeholder="MIDDLE NAME">
                <input id="edit_last_name" name="last_name" class="form-input" placeholder="LAST NAME" required>
            </div>

            <!-- BASIC INFO -->
            <div class="form-row">
                <select id="edit_sex" name="sex" class="form-input" required>
                    <option value="">SEX</option>
                    <option value="Male">MALE</option>
                    <option value="Female">FEMALE</option>
                </select>

                <select id="edit_gender" name="gender" class="form-input" required>
                    <option value="">GENDER</option>
                    <option value="LGBTQAI+">LGBTQAI+</option>
                    <option value="Prefer not to say">PREFER NOT TO SAY</option>
                </select>

                <input id="edit_birthday" type="date" name="birthday" class="form-input">
                <input id="edit_age" type="number" name="age" class="form-input" readonly>

                <select id="edit_civil_status" name="civil_status" class="form-input" required>
                    <option value="">CIVIL STATUS</option>
                    <option value="SINGLE">SINGLE</option>
                    <option value="MARRIED">MARRIED</option>
                    <option value="WIDOWED">WIDOWED</option>
                    <option value="SEPARATED">SEPARATED</option>
                    <option value="LIVE-IN">LIVE-IN</option>
                </select>
            </div>

            <!-- LOCATION -->
            <div class="form-row">
                <input id="edit_region" name="region" class="form-input" placeholder="REGION" readonly>
                <input id="edit_province" name="province" class="form-input" placeholder="PROVINCE" readonly>
                <input id="edit_municipality" name="municipality" class="form-input" placeholder="MUNICIPALITY" readonly>
            </div>

            <div class="form-row">
                <select id="edit_barangay" name="barangay" class="form-input" placeholder="BARANGAY">

                        <option value="" disabled class="bold">BARANGAY</option>

                        @foreach (['AWANG', 'BAGOCBOC', 'BARRA', 'BONBON', 'CAUYUNAN', 'IGPIT', 'LIMUNDA', 'LUYONG BONBON', 'MALANANG', 'NANGCAON', 'PATAG', 'POBLACION', 'TABOC', 'TINGALAN'] as $b)
                            <option value="{{ $b }}">
                                {{ $b }}
                            </option>
                        @endforeach
                    </select>

                <input id="edit_purok_zone" name="purok_zone" class="form-input" placeholder="PUROK / ZONE">
                <input id="edit_home_address" name="home_address" class="form-input" placeholder="HOME ADDRESS"
                    readonly>
            </div>

            <!-- RELIGION -->
            <div class="form-row">
                <select name="religion" id="edit_religion" class="form-input" required>
                    <option value="">RELIGION</option>
                    <option value="ROMAN CATHOLIC">ROMAN CATHOLIC</option>
                    <option value="BAPTIST">BAPTIST</option>
                    <option value="BORN AGAIN CHRISTIAN">BORN AGAIN CHRISTIAN</option>
                    <option value="IGLESIA NI CRISTO">IGLESIA NI CRISTO</option>
                    <option value="SEVENTH-DAY ADVENTIST">SEVENTH-DAY ADVENTIST</option>
                    <option value="JEHOVAH'S WITNESSES">JEHOVAH'S WITNESSES</option>
                    <option value="METHODIST">METHODIST</option>
                    <option value="LUTHERAN">LUTHERAN</option>
                    <option value="ANGLICAN">ANGLICAN</option>
                    <option value="PENTECOSTAL">PENTECOSTAL</option>
                    <option value="UNITED CHURCH OF CHRIST IN THE PHILIPPINES (UCCP)">
                        UNITED CHURCH OF CHRIST IN THE PHILIPPINES (UCCP)
                    </option>
                    <option value="ISLAM">ISLAM</option>
                    <option value="OTHERS">OTHERS</option>
                </select>

                <input type="text" name="religion_other" id="edit_religion_other" class="form-input"
                    placeholder="PLEASE SPECIFY RELIGION" style="display:none;">
            </div>

            <!-- EDUCATION -->
            <select id="edit_education" name="education" class="form-input" required>
                <option value="">EDUCATION LAST ATTENDED</option>
                <option value="ELEMENTARY LEVEL">ELEMENTARY LEVEL</option>
                <option value="ELEMENTARY GRADUATE">ELEMENTARY GRADUATE</option>
                <option value="HIGH SCHOOL LEVEL">HIGH SCHOOL LEVEL</option>
                <option value="HIGH SCHOOL GRADUATE">HIGH SCHOOL GRADUATE</option>
                <option value="COLLEGE LEVEL">COLLEGE LEVEL</option>
                <option value="COLLEGE GRADUATE">COLLEGE GRADUATE</option>
                <option value="VOCATIONAL">VOCATIONAL</option>
            </select>



            <!-- SKILLS -->
            <div class="form-row">

                <input id="edit_skills" name="skills" class="form-input" placeholder="SKILLS">

                <select id="edit_preferred_skills" name="preferred_skills" class="form-input">

                    <option value="" disabled>PREFERRED SKILLS</option>

                    <option value="HOUSEKEEPING">HOUSEKEEPING</option>
                    <option value="BREAD & PASTRIES PRODUCTION">BREAD & PASTRIES PRODUCTION</option>
                    <option value="DRIVING">DRIVING</option>
                    <option value="AUTOMOTIVE SERVICING">AUTOMOTIVE SERVICING</option>
                    <option value="BOOKKEEPING">BOOKKEEPING</option>
                    <option value="ELECTRICAL INSTALLATION & MAINTENANCE">ELECTRICAL INSTALLATION & MAINTENANCE</option>
                    <option value="PLUMBING">PLUMBING</option>
                    <option value="SHIELDED METAL ARC WELDING SMAW">SHIELDED METAL ARC WELDING SMAW</option>
                    <option value="TILE SETTING">TILE SETTING</option>
                    <option value="FOOD & BEVERAGE SERVICES">FOOD & BEVERAGE SERVICES</option>
                    <option value="COMPUTER SYSTEM SERVICING">COMPUTER SYSTEM SERVICING</option>
                    <option value="CARPENTRY">CARPENTRY</option>
                    <option value="MASONRY">MASONRY</option>
                    <option value="BARISTA">BARISTA</option>
                    <option value="MASSAGE THERAPIST">MASSAGE THERAPIST</option>
                    <option value="CAREGIVING">CAREGIVING</option>
                    <option value="DRESSMAKING">DRESSMAKING</option>
                    <option value="TAILORING">TAILORING</option>
                    <option value="OTHERS">OTHERS (SPECIFY)</option>

                </select>

                <input id="edit_preferred_skills_other" name="preferred_skills_other" class="form-input"
                    placeholder="SPECIFY PREFERRED SKILL" style="display:none;">

            </div>


            <!-- OTHER INFO -->
            <div class="form-row">

                <input id="edit_source_of_income" name="source_of_income" class="form-input"
                    placeholder="SOURCE OF INCOME">

                <input id="edit_contact_number" name="contact_number" class="form-input" type="number"
                    placeholder="CONTACT NUMBER">

            </div>

            <!-- SK VOTER -->
            <div class="form-row">
                <label>ARE YOU A REGISTERED SK VOTER?</label>

                <label>
                    <input type="radio" name="is_sk_voter" value="Yes" id="edit_is_sk_voter_yes">
                    YES
                </label>

                <label>
                    <input type="radio" name="is_sk_voter" value="No" id="edit_is_sk_voter_no">
                    NO
                </label>
            </div>

            <!-- YOUTH CLASSIFICATION -->
            <div class="form-row">
                <label>YOUTH CLASSIFICATION:</label>

                <label>
                    <input type="checkbox" id="edit_is_osy" name="is_osy">
                    OUT-OF-SCHOOL YOUTH
                </label>

                <label>
                    <input type="checkbox" id="edit_is_isy" name="is_isy">
                    IN-SCHOOL YOUTH
                </label>

                <label>
                    <input type="checkbox" id="edit_is_4ps" name="is_4ps">
                    4PS
                </label>

                <label>
                    <input type="checkbox" id="edit_is_ip" name="is_ip">
                    INDIGENOUS PEOPLE (IP)
                </label>

                <label>
                    <input type="checkbox" id="edit_is_pwd" name="is_pwd">
                    PERSON WITH DISABILITY
                </label>

            </div>


            <!-- WORK CLASSIFICATION -->
            <div class="form-row">

                <label>WORK CLASSIFICATION:</label>

                <label>
                    <input type="checkbox" id="edit_is_unemployed" name="is_unemployed">
                    UNEMPLOYED YOUTH
                </label>

                <label>
                    <input type="checkbox" id="edit_is_employed" name="is_employed">
                    EMPLOYED YOUTH
                </label>

                <label>
                    <input type="checkbox" id="edit_is_self_employed" name="is_self_employed">
                    SELF-EMPLOYED YOUTH
                </label>

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

                    <tbody id="editFamilyBody">
                        <tr>
                            <td>
                                <input class="form-input" name="family_members[0][name]" placeholder="FULL NAME">
                            </td>

                            <td>
                                <input type="number" class="form-input" name="family_members[0][age]" min="1" max="99"
                                    inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            </td>

                            <td>
                                <select name="family_members[0][relationship]" class="form-input">
                                    <option value="" disabled selected>RELATIONSHIP</option>
                                    <option>MOTHER</option>
                                    <option>FATHER</option>
                                    <option>BROTHER</option>
                                    <option>SISTER</option>
                                    <option>GRANDPARENT</option>
                                    <option>AUNT</option>
                                    <option>UNCLE</option>
                                    <option>COUSIN</option>
                                    <option>SPOUSE</option>
                                </select>
                            </td>

                            <td>
                                <select name="family_members[0][education]" class="form-input">
                                    <option value="" disabled selected>EDUCATION</option>
                                    <option>NONE</option>
                                    <option>PRE-SCHOOL</option>
                                    <option>KINDERGARTEN</option>
                                    <option>ELEMENTARY LEVEL</option>
                                    <option>ELEMENTARY GRADUATE</option>
                                    <option>HIGH SCHOOL LEVEL</option>
                                    <option>HIGH SCHOOL GRADUATE</option>
                                    <option>COLLEGE LEVEL</option>
                                    <option>COLLEGE GRADUATE</option>
                                    <option>VOCATIONAL</option>
                                </select>
                            </td>

                            <td>
                                <input class="form-input" name="family_members[0][occupation]" placeholder="OCCUPATION">
                            </td>

                            <td>
                                <input type="number" class="form-input" name="family_members[0][income]" min="0"
                                    step="1" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    placeholder="MONTHLY INCOME">
                            </td>

                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" id="addEditFamilyRow" class="save-btn mt-3">
                    + ADD FAMILY MEMBER
                </button>
            </div>

            <h4 class="bold mt-6 mb-2">III. ATTACHMENTS</h4>

            <div class="attachment-wrapper">

                <!-- ================= EXISTING ================= -->
                <h5 class="text-sm font-semibold mb-2">SAVED ATTACHMENTS</h5>

                <div id="existingAttachments" class="attachment-preview-grid mb-4">
                </div>

                <!-- Divider -->
                <hr class="my-4 border-gray-300">

                <!-- ================= NEW UPLOAD ================= -->
                <h5 class="text-sm font-semibold mb-2">ADD NEW ATTACHMENTS</h5>

                <label class="save-btn cursor-pointer text-sm">
                    📎 ADD MORE ATTACHMENTS
                    <input type="file" id="editAttachments" name="attachments[]" accept="image/jpeg,image/png,image/jpg"
                        multiple hidden>
                </label>

                <small class="text-gray-500 block mt-2">
                    UPLOAD MULTIPLE JPG/PNG IMAGES (MAX 4MB EACH)
                </small>

                <div id="editAttachmentPreview" class="attachment-preview-grid mt-3">
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Styles & Script --}}
<link rel="stylesheet" href="{{ asset('css/buttons.css') }}">

<link rel="stylesheet" href="{{ asset('css/youth-edit.css') }}">
<link rel="stylesheet" href="{{ asset('css/youth-index.css') }}">
<script src="{{ asset('js/youth-edit.js') }}" defer></script>
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
