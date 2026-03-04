document.addEventListener("DOMContentLoaded", () => {
    /* =====================================================
       ELEMENT REFERENCES (SAFE)
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
        const age = calculateAge(birthdayInput.value);
        ageInput.value = age;

        if (age < 15 || age > 30) {
            ageInput.classList.add("border-red-500");
        } else {
            ageInput.classList.remove("border-red-500");
        }
    }

    if (birthdayInput) {
        birthdayInput.addEventListener("change", updateAge);
        birthdayInput.addEventListener("input", updateAge);
    }

    if (youthForm) {
        youthForm.addEventListener("submit", (e) => {
            const age = parseInt(ageInput.value);
            if (isNaN(age) || age < 15 || age > 30) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Submission Blocked",
                    text: "Only ages 15 to 30 are allowed to submit this form.",
                    confirmButtonColor: "#ef4444",
                });
            }
        });
    }

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
        if (!el) return;
        el.addEventListener("input", buildHomeAddress);
        el.addEventListener("change", buildHomeAddress);
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

        if (preferredSkillsSelect.value === "Others") {
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
       PRIVACY MODAL
    ===================================================== */

    const privacyModal = document.getElementById("privacyModal");
    const openPrivacyBtn = document.getElementById("openPrivacyModal");
    const closePrivacyBtn = document.getElementById("closePrivacyModal");
    const closePrivacyFooterBtn = document.getElementById(
        "closePrivacyModalBtn",
    );

    function openPrivacyModalFn() {
        privacyModal.classList.remove("hidden");
        privacyModal.classList.add("flex");
        document.body.style.overflow = "hidden";
    }

    function closePrivacyModalFn() {
        privacyModal.classList.add("hidden");
        privacyModal.classList.remove("flex");
        document.body.style.overflow = "";
    }

    openPrivacyBtn?.addEventListener("click", openPrivacyModalFn);
    closePrivacyBtn?.addEventListener("click", closePrivacyModalFn);
    closePrivacyFooterBtn?.addEventListener("click", closePrivacyModalFn);

    privacyModal?.addEventListener("click", (e) => {
        if (e.target === privacyModal) closePrivacyModalFn();
    });
});

/* =====================================================
   PHOTO PREVIEW (GLOBAL)
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
