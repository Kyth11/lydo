<div id="editModal" class="modal-overlay">
    <div class="modal-content animated-modal">

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

        <form id="editForm" method="POST" enctype="multipart/form-data" class="youth-form">
            @csrf
            @method('PUT')

            <!-- I. Identifying Information -->
            <h4 class="bold">I. Identifying Information</h4>

            <!-- PROFILE PHOTO -->
            <div class="flex flex-col items-center gap-3 mb-6">
                <div class="relative w-32 h-32 rounded-full overflow-hidden border-2 border-indigo-500 mb-2">
                    <img id="editPhotoPreview" src="{{ asset('images/avatar.png') }}" class="w-24 h-24 object-cover">
                </div>

                <label class="save-btn cursor-pointer text-sm">
                    📷 Change Photo
                    <input type="file" name="profile_photo" accept="image/jpeg,image/png" hidden
                        onchange="previewEditPhoto(event)">
                </label>
            </div>

            <!-- NAME -->
            <div class="form-row">
                <input id="edit_first_name" name="first_name" class="form-input" placeholder="First Name" required>
                <input id="edit_middle_name" name="middle_name" class="form-input" placeholder="Middle Name">
                <input id="edit_last_name" name="last_name" class="form-input" placeholder="Last Name" required>
            </div>

            <!-- BASIC INFO -->
            <div class="form-row">
                <select id="edit_sex" name="sex" class="form-input" required>
                    <option value="">Sex</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>

                <select id="edit_gender" name="gender" class="form-input" required>
                    <option value="">Gender</option>
                    <option>LGBTQAI+</option>
                    <option>Prefer not to say</option>
                </select>

                <input id="edit_birthday" type="date" name="birthday" class="form-input" required>
                <input id="edit_age" type="number" name="age" class="form-input" readonly>

                <select id="edit_civil_status" name="civil_status" class="form-input" required>
                    <option value="" disabled>Civil Status</option>

                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Separated">Separated</option>
                    <option value="Live-in">Live-in</option>
                </select>
            </div>

            <!-- LOCATION -->
            <div class="form-row">
                <input id="edit_region" name="region" class="form-input">
                <input id="edit_province" name="province" class="form-input">
                <input id="edit_municipality" name="municipality" class="form-input">
            </div>

            <div class="form-row">
                <input id="edit_barangay" name="barangay" class="form-input">
                <input id="edit_purok_zone" name="purok_zone" class="form-input" placeholder="Purok / Zone">
                <input id="edit_home_address" name="home_address" class="form-input" readonly>
            </div>

            <!-- RELIGION -->
            <div class="form-row">
                <select name="religion" id="edit_religion" class="form-input" required>
                    <option value="" disabled selected>Religion</option>

                    <!-- Christian -->
                    <option value="Roman Catholic">Roman Catholic</option>
                    <option value="Baptist">Baptist</option>
                    <option value="Born Again Christian">Born Again Christian</option>
                    <option value="Iglesia ni Cristo">Iglesia ni Cristo</option>
                    <option value="Seventh-day Adventist">Seventh-day Adventist</option>
                    <option value="Jehovah's Witnesses">Jehovah's Witnesses</option>
                    <option value="Methodist">Methodist</option>
                    <option value="Lutheran">Lutheran</option>
                    <option value="Anglican">Anglican</option>
                    <option value="Pentecostal">Pentecostal</option>
                    <option value="United Church of Christ in the Philippines (UCCP)">
                        United Church of Christ in the Philippines (UCCP)
                    </option>

                    <!-- Non-Christian -->
                    <option value="Islam">Islam</option>

                    <!-- Others -->
                    <option value="Others">Others (Specify)</option>
                </select>

                <input type="text" name="religion_other" id="edit_religion_other" class="form-input"
                    placeholder="Please specify religion" style="display:none;">
            </div>

            <!-- EDUCATION -->
            <select id="edit_education" name="education" class="form-input" required>
                <option value="">Education Last Attended</option>
                <option>Elementary Level</option>
                <option>Elementary Graduate</option>
                <option>High School Level</option>
                <option>High School Graduate</option>
                <option>College Level</option>
                <option>College Graduate</option>
                <option>Vocational</option>
            </select>

            <!-- SK VOTER -->
            <div class="form-row">
                <label>Are you a Registered SK Voter?</label>
                <label>
                    <input type="radio" name="is_sk_voter" value="Yes"> Yes
                </label>
                <label>
                    <input type="radio" name="is_sk_voter" value="No"> No
                </label>
            </div>

            <!-- CLASSIFICATIONS -->
            <div class="form-row">
                <label>Youth Classification: </label>
                <label><input type="checkbox" id="edit_is_osy" name="is_osy"> OSY</label>
                <label><input type="checkbox" id="edit_is_isy" name="is_isy"> ISY</label>
                <label><input type="checkbox" id="edit_is_4ps" name="is_4ps"> 4Ps</label>
                <label><input type="checkbox" id="edit_is_ip" name="is_ip"> IP</label>
                <label><input type="checkbox" id="edit_is_pwd" name="is_pwd"> PWD</label>
            </div>

            <!-- WORK -->
            <div class="form-row">
                <label> Work Classification: </label>
                <label><input type="checkbox" id="edit_is_unemployed" name="is_unemployed"> Unemployed</label>
                <label><input type="checkbox" id="edit_is_employed" name="is_employed"> Employed</label>
                <label><input type="checkbox" id="edit_is_self_employed" name="is_self_employed">
                    Self-Employed</label>
            </div>

            <!-- SKILLS -->
            <div class="form-row">
                <input id="edit_skills" name="skills" class="form-input" placeholder="Skills" required>

                <!-- Preferred Skills -->
                <select id="edit_preferred_skills" name="preferred_skills" class="form-input" required>
                    <option value="" disabled selected>Preferred Skills</option>

                    <option value="Housekeeping">Housekeeping</option>
                    <option value="Bread & Pastries Production">Bread & Pastries Production</option>
                    <option value="Driving">Driving</option>
                    <option value="Automotive Servicing">Automotive Servicing</option>
                    <option value="Bookkeeping">Bookkeeping</option>
                    <option value="Electrical Installation & Maintenance">
                        Electrical Installation & Maintenance
                    </option>
                    <option value="Plumbing">Plumbing</option>
                    <option value="Shielded Metal Arc Welding SMAW">
                        Shielded Metal Arc Welding SMAW
                    </option>
                    <option value="Tile Setting">Tile Setting</option>
                    <option value="Food & Beverage Services">Food & Beverage Services</option>
                    <option value="Computer System Servicing">Computer System Servicing</option>
                    <option value="Carpentry">Carpentry</option>
                    <option value="Masonry">Masonry</option>
                    <option value="Barista">Barista</option>
                    <option value="Massage Therapist">Massage Therapist</option>
                    <option value="Caregiving">Caregiving</option>
                    <option value="Dressmaking">Dressmaking</option>
                    <option value="Tailoring">Tailoring</option>

                    <!-- Others -->
                    <option value="Others">Others (Specify)</option>
                </select>

                <input type="text" id="edit_preferred_skills_other" name="preferred_skills_other" class="form-input"
                    placeholder="Please specify preferred skill" style="display:none;">
                <input id="edit_source_of_income" name="source_of_income" class="form-input"
                    placeholder="Source of Income">

                <input id="edit_contact_number" name="contact_number" class="form-input" placeholder="Contact Number">
            </div>

            <!-- FAMILY -->
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
                    <tbody id="editFamilyBody"></tbody>
                </table>

                <button type="button" id="addEditFamilyRow" class="save-btn mt-3">
                    + Add Family Member
                </button>
            </div>
    </div>
    {{-- Edit Youth Assets --}}
    <link rel="stylesheet" href="{{ asset('css/youth-edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/youth-index.css') }}">
    <script src="{{ asset('js/youth-edit.js') }}" defer></script>
