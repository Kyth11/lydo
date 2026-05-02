document.addEventListener("DOMContentLoaded", () => {
    /* =====================================================
       ELEMENT REFERENCES
    ===================================================== */

    const birthdayInput = document.getElementById("birthday");
    const ageInput = document.getElementById("age");
    const youthForm = document.getElementById("youthForm");

    const region = document.getElementById("region");
    const province = document.getElementById("province");
    const municipality = document.getElementById("municipality");
    const barangay = document.getElementById("barangay");
    const purokZone = document.getElementById("purok_zone");
    const homeAddress = document.getElementById("home_address");

    const religionSelect = document.getElementById("religionSelect");
    const otherReligionInput = document.getElementById("otherReligionInput");

    const preferredSkillsSelect = document.getElementById(
        "preferredSkillsSelect",
    );
    const otherPreferredSkillInput = document.getElementById(
        "otherPreferredSkillInput",
    );

    const familyBody = document.getElementById("familyBody");
    const addFamilyRowBtn = document.getElementById("addFamilyRow");

    const attachmentInput = document.getElementById("attachments");
    const previewContainer = document.getElementById("attachmentPreview");

    /* =====================================================
       AGE CALCULATION
    ===================================================== */

    function calculateAge(dateString) {
        if (!dateString) return "";
        const birth = new Date(dateString + "T00:00:00");
        const today = new Date();

        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
            age--;
        }

        return age;
    }

    function updateAge() {
        if (!birthdayInput || !ageInput) return;

        const age = calculateAge(birthdayInput.value);
        ageInput.value = age;

        if (age < 15 || age > 30) {
            ageInput.classList.add("border-red-500");
        } else {
            ageInput.classList.remove("border-red-500");
        }
    }

    birthdayInput?.addEventListener("change", updateAge);
    birthdayInput?.addEventListener("input", updateAge);

    youthForm?.addEventListener("submit", (e) => {
        const age = parseInt(ageInput?.value);
        if (isNaN(age) || age < 15 || age > 30) {
            e.preventDefault();
            Swal.fire({
                icon: "warning",
                title: "Submission Blocked",
                text: "Only ages 15 to 30 are allowed.",
                confirmButtonColor: "#ef4444",
            });
        }
    });

    /* =====================================================
       HOME ADDRESS BUILDER
    ===================================================== */

    function buildHomeAddress() {
        const parts = [
            purokZone?.value?.trim(),
            barangay?.value?.trim(),
            municipality?.value?.trim(),
            province?.value?.trim(),
            region?.value?.trim(),
        ].filter(Boolean);

        if (homeAddress) {
            homeAddress.value = parts.join(", ");
        }
    }

    [region, province, municipality, barangay, purokZone].forEach((el) => {
        el?.addEventListener("input", buildHomeAddress);
        el?.addEventListener("change", buildHomeAddress);
    });

    buildHomeAddress();

    /* =====================================================
       RELIGION (OTHERS)
    ===================================================== */

    function toggleOtherReligion() {
        if (!religionSelect || !otherReligionInput) return;

        if (religionSelect.value === "Others") {
            otherReligionInput.style.display = "block";
            otherReligionInput.required = true;
        } else {
            otherReligionInput.style.display = "none";
            otherReligionInput.required = false;
            otherReligionInput.value = "";
        }
    }

    religionSelect?.addEventListener("change", toggleOtherReligion);
    toggleOtherReligion();

    /* =====================================================
       PREFERRED SKILLS (OTHERS)
    ===================================================== */

    function toggleOtherSkill() {
        if (!preferredSkillsSelect || !otherPreferredSkillInput) return;

        if (preferredSkillsSelect.value === "OTHERS") {
            otherPreferredSkillInput.style.display = "block";
            otherPreferredSkillInput.required = true;
        } else {
            otherPreferredSkillInput.style.display = "none";
            otherPreferredSkillInput.required = false;
            otherPreferredSkillInput.value = "";
        }
    }

    preferredSkillsSelect?.addEventListener("change", toggleOtherSkill);
    toggleOtherSkill();

    /* =====================================================
       FAMILY MEMBERS (DYNAMIC ROWS)
    ===================================================== */

    let familyIndex = familyBody ? familyBody.children.length : 0;

    addFamilyRowBtn?.addEventListener("click", () => {
        const row = document.createElement("tr");

        row.innerHTML = `
<td><input class="form-input" name="family_members[${familyIndex}][name]" placeholder="Full Name"></td>
<td><input type="number" class="form-input" name="family_members[${familyIndex}][age]" min="1" max="99"></td>
<td>
<select name="family_members[${familyIndex}][relationship]" class="form-input">
<option disabled selected>Relationship</option>
<option>Mother</option><option>Father</option><option>Brother</option>
<option>Sister</option><option>Grandparent</option><option>Aunt</option>
<option>Uncle</option><option>Cousin</option><option>Spouse</option>
</select>
</td>
<td>
<select name="family_members[${familyIndex}][education]" class="form-input">
<option disabled selected>Educational Attainment</option>
<option>None</option><option>Pre-School</option><option>Kindergarten</option>
<option>Elementary Level</option><option>Elementary Graduate</option>
<option>High School Level</option><option>High School Graduate</option>
<option>College Level</option><option>College Graduate</option><option>Vocational</option>
</select>
</td>
<td><input class="form-input" name="family_members[${familyIndex}][occupation]" placeholder="Occupation"></td>
<td><input type="number" class="form-input" name="family_members[${familyIndex}][income]" placeholder="Monthly Income"></td>
<td><button type="button" class="remove-btn removeRow">Remove</button></td>
`;

        familyBody.appendChild(row);
        familyIndex++;
    });

    familyBody?.addEventListener("click", (e) => {
        if (e.target.classList.contains("removeRow")) {
            e.target.closest("tr").remove();
        }
    });

    /* =====================================================
       ATTACHMENTS (GRID + REMOVE)
    ===================================================== */

    let selectedFiles = [];

    attachmentInput?.addEventListener("change", function (e) {
        const files = Array.from(e.target.files);

        files.forEach((file) => {
            if (!file.type.startsWith("image/")) {
                Swal.fire({
                    icon: "error",
                    title: "Invalid File",
                    text: "Only image files allowed.",
                });
                return;
            }

            if (file.size > 4 * 1024 * 1024) {
                Swal.fire({
                    icon: "warning",
                    title: "File Too Large",
                    text: "Maximum file size is 4MB.",
                });
                return;
            }

            selectedFiles.push(file);
        });

        renderPreviews();
    });

    function renderPreviews() {
        if (!previewContainer) return;

        previewContainer.innerHTML = "";

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function (e) {
                const wrapper = document.createElement("div");
                wrapper.classList.add("attachment-item");

                wrapper.innerHTML = `
<img src="${e.target.result}">
<button type="button"
class="attachment-remove-btn"
onclick="removeAttachment(${index})">
&times;
</button>
`;

                previewContainer.appendChild(wrapper);
            };

            reader.readAsDataURL(file);
        });

        const dataTransfer = new DataTransfer();
        selectedFiles.forEach((file) => dataTransfer.items.add(file));
        attachmentInput.files = dataTransfer.files;
    }

    window.removeAttachment = function (index) {
        selectedFiles.splice(index, 1);
        renderPreviews();
    };
});

/* =====================================================
   PHOTO PREVIEW
===================================================== */

function previewPhoto(event) {
    const input = event.target;
    const preview = document.getElementById("photoPreview");

    if (!input.files || !input.files[0]) return;

    const file = input.files[0];

    if (!file.type.startsWith("image/")) {
        Swal.fire({
            icon: "error",
            title: "Invalid File",
            text: "Please select an image file.",
        });
        input.value = "";
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => (preview.src = e.target.result);
    reader.readAsDataURL(file);
}

/* =========================
   PRIVACY MODAL CONTROL (FIXED)
========================= */

document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("privacyModal");
    const openBtn = document.getElementById("openPrivacyModal");
    const closeBtn = document.getElementById("closePrivacyModal");
    const closeBtn2 = document.getElementById("closePrivacyModalBtn");
    const agreeBtn = document.getElementById("agreePrivacy");
    const checkbox = document.getElementById("privacyConsent");

    if (!modal) return;

    // OPEN
    openBtn?.addEventListener("click", () => {
        modal.classList.add("active");
    });

    // CLOSE FUNCTION
    function closeModal() {
        modal.classList.remove("active");
    }

    closeBtn?.addEventListener("click", closeModal);
    closeBtn2?.addEventListener("click", closeModal);

    // AGREE BUTTON
    agreeBtn?.addEventListener("click", () => {
        checkbox.checked = true;
        closeModal();
    });

    // CLICK OUTSIDE
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
});

/* =========================
   FORM VALIDATION (CONSENT REQUIRED)
========================= */

document.getElementById("youthForm")?.addEventListener("submit", function (e) {
    if (!checkbox.checked) {
        e.preventDefault();
        Swal.fire({
            icon: "warning",
            title: "Consent Required",
            text: "You must agree to the Data Privacy Consent before submitting.",
        });
    }
});
