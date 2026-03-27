let editFamilyIndex = 0;

window.previewEditPhoto = function (event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById("editPhotoPreview").src = e.target.result;
    };
    reader.readAsDataURL(file);
};

document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("editModal");
    const form = document.getElementById("editForm");
    const familyBody = document.getElementById("editFamilyBody");

    const existingContainer = document.getElementById("existingAttachments");
    const newPreviewContainer = document.getElementById(
        "editAttachmentPreview",
    );
    const fileInput = document.getElementById("editAttachments");

    let newFiles = [];

    document
        .getElementById("addEditFamilyRow")
        ?.addEventListener("click", addEditFamilyRow);

    /* =====================================================
       OPEN MODAL
    ===================================================== */

    window.openEditModal = function (youth) {
        form.action = `/youth/${youth.id}`;

        /* BASIC INFO */
        setVal("edit_first_name", youth.first_name);
        setVal("edit_middle_name", youth.middle_name);
        setVal("edit_last_name", youth.last_name);
        setVal("edit_sex", youth.sex);
        setVal("edit_gender", youth.gender);
        setVal("edit_civil_status", youth.civil_status);
        setVal("edit_education", youth.education);
        setVal("edit_age", youth.age);

        setVal("edit_region", youth.region);
        setVal("edit_province", youth.province);
        setVal("edit_municipality", youth.municipality);
        setVal("edit_purok_zone", youth.purok_zone);
        setVal("edit_home_address", youth.home_address);

        /* BARANGAY (force select correct value) */
        const barangaySelect = document.getElementById("edit_barangay");
        if (barangaySelect) {
            barangaySelect.value = youth.barangay ?? "";
        }

        /* RELIGION */
        setVal("edit_religion", youth.religion);
        setVal("edit_religion_other", youth.religion_other);

        /* YOUTH CLASSIFICATION */
        setCheckbox("edit_is_osy", youth.is_osy);
        setCheckbox("edit_is_isy", youth.is_isy);
        setCheckbox("edit_is_4ps", youth.is_4ps);
        setCheckbox("edit_is_ip", youth.is_ip);
        setCheckbox("edit_is_pwd", youth.is_pwd);

        /* WORK CLASSIFICATION */
        setCheckbox("edit_is_unemployed", youth.is_unemployed);
        setCheckbox("edit_is_employed", youth.is_employed);
        setCheckbox("edit_is_self_employed", youth.is_self_employed);

        /* SKILLS */
        setVal("edit_skills", youth.skills);
        setVal("edit_preferred_skills", youth.preferred_skills);
        setVal("edit_preferred_skills_other", youth.preferred_skills_other);

        /* OTHER INFO */
        setVal("edit_source_of_income", youth.source_of_income);
        setVal("edit_contact_number", youth.contact_number);

        function setCheckbox(id, value) {
            const el = document.getElementById(id);
            if (!el) return;

            if (value === 1 || value === true || value === "Yes") {
                el.checked = true;
            } else {
                el.checked = false;
            }
        }

        /* BIRTHDAY */
        const birthdayField = document.getElementById("edit_birthday");

        if (birthdayField && youth.birthday) {
            birthdayField.value = youth.birthday.split("T")[0];
        }

        /* Auto calculate age when modal loads */
        if (typeof updateAge === "function") {
            updateAge();
        }
        /* SK VOTER */
        document
            .querySelectorAll('input[name="is_sk_voter"]')
            .forEach((radio) => {
                radio.checked = radio.value === String(youth.is_sk_voter);
            });

        /* PHOTO */
        document.getElementById("editPhotoPreview").src = youth.profile_photo
            ? `/storage/${youth.profile_photo}`
            : "/images/avatar.png";

        /* FAMILY */
        familyBody.innerHTML = "";
        editFamilyIndex = 0;

        if (
            Array.isArray(youth.family_members) &&
            youth.family_members.length
        ) {
            youth.family_members.forEach((member) => {
                familyBody.insertAdjacentHTML(
                    "beforeend",
                    buildEditFamilyRow(member, editFamilyIndex++),
                );
            });
        } else {
            addEditFamilyRow();
        }

        /* ATTACHMENTS */
        loadExistingAttachments(youth.attachments || []);

        newFiles = [];
        newPreviewContainer.innerHTML = "";
        fileInput.value = "";

        modal.style.display = "flex";
    };

    /* =====================================================
       EXISTING ATTACHMENTS
    ===================================================== */

    function loadExistingAttachments(attachments) {
        existingContainer.innerHTML = "";

        attachments.forEach((att) => {
            const wrapper = document.createElement("div");
            wrapper.classList.add("attachment-item");

            wrapper.innerHTML = `
                <img src="/storage/${att.file_path}">
                <button type="button"
                        class="attachment-remove-btn"
                        data-id="${att.id}">
                    &times;
                </button>
            `;

            wrapper
                .querySelector("button")
                .addEventListener("click", function () {
                    deleteExistingAttachment(att.id, wrapper);
                });

            existingContainer.appendChild(wrapper);
        });
    }

    function deleteExistingAttachment(id, element) {
        Swal.fire({
            icon: "warning",
            title: "Delete Attachment?",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            confirmButtonText: "Yes, Delete",
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`/attachments/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: "application/json",
                },
            })
                .then((res) => res.json())
                .then(() => {
                    element.remove();
                });
        });
    }

    /* =====================================================
       NEW ATTACHMENTS
    ===================================================== */

    fileInput?.addEventListener("change", function (e) {
        const files = Array.from(e.target.files);

        files.forEach((file) => {
            if (!file.type.startsWith("image/")) {
                Swal.fire("Invalid File", "Only images allowed.", "error");
                return;
            }

            if (file.size > 4 * 1024 * 1024) {
                Swal.fire("File Too Large", "Max 4MB allowed.", "warning");
                return;
            }

            newFiles.push(file);
        });

        renderNewAttachments();
    });

    function renderNewAttachments() {
        newPreviewContainer.innerHTML = "";

        newFiles.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function (e) {
                const wrapper = document.createElement("div");
                wrapper.classList.add("attachment-item");

                wrapper.innerHTML = `
                    <img src="${e.target.result}">
                    <button type="button"
                            class="attachment-remove-btn">
                        &times;
                    </button>
                `;

                wrapper
                    .querySelector("button")
                    .addEventListener("click", function () {
                        newFiles.splice(index, 1);
                        renderNewAttachments();
                    });

                newPreviewContainer.appendChild(wrapper);
            };

            reader.readAsDataURL(file);
        });

        const dt = new DataTransfer();
        newFiles.forEach((file) => dt.items.add(file));
        fileInput.files = dt.files;
    }

    /* =====================================================
       FAMILY
    ===================================================== */

    function buildEditFamilyRow(m = {}, i) {
        return `
        <tr>

            <td>
                <input class="form-input"
                    name="family_members[${i}][name]"
                    value="${m.name ?? ""}">
            </td>

            <td>
                <input type="number"
                    class="form-input"
                    name="family_members[${i}][age]"
                    value="${m.age ?? ""}">
            </td>

            <td>
                <select class="form-input"
                    name="family_members[${i}][relationship]">

                    <option value="">Relationship</option>
                    <option ${m.relationship == "Mother" ? "selected" : ""}>Mother</option>
                    <option ${m.relationship == "Father" ? "selected" : ""}>Father</option>
                    <option ${m.relationship == "Brother" ? "selected" : ""}>Brother</option>
                    <option ${m.relationship == "Sister" ? "selected" : ""}>Sister</option>
                    <option ${m.relationship == "Grandparent" ? "selected" : ""}>Grandparent</option>
                    <option ${m.relationship == "Aunt" ? "selected" : ""}>Aunt</option>
                    <option ${m.relationship == "Uncle" ? "selected" : ""}>Uncle</option>
                    <option ${m.relationship == "Cousin" ? "selected" : ""}>Cousin</option>
                    <option ${m.relationship == "Spouse" ? "selected" : ""}>Spouse</option>

                </select>
            </td>

            <td>
                <select class="form-input"
                    name="family_members[${i}][education]">

                    <option value="">Education</option>
                    <option ${m.education == "None" ? "selected" : ""}>None</option>
                    <option ${m.education == "Elementary Level" ? "selected" : ""}>Elementary Level</option>
                    <option ${m.education == "Elementary Graduate" ? "selected" : ""}>Elementary Graduate</option>
                    <option ${m.education == "High School Level" ? "selected" : ""}>High School Level</option>
                    <option ${m.education == "High School Graduate" ? "selected" : ""}>High School Graduate</option>
                    <option ${m.education == "College Level" ? "selected" : ""}>College Level</option>
                    <option ${m.education == "College Graduate" ? "selected" : ""}>College Graduate</option>
                    <option ${m.education == "Vocational" ? "selected" : ""}>Vocational</option>

                </select>
            </td>

            <td>
                <input class="form-input"
                    name="family_members[${i}][occupation]"
                    value="${m.occupation ?? ""}">
            </td>

            <td>
                <input type="number"
                    class="form-input"
                    name="family_members[${i}][income]"
                    value="${m.income ?? ""}">
            </td>

            <td>
                <button type="button"
                    class="removeEditRow remove-btn">
                    Remove
                </button>
            </td>

        </tr>`;
    }

    window.addEditFamilyRow = function () {
        familyBody.insertAdjacentHTML(
            "beforeend",
            buildEditFamilyRow({}, editFamilyIndex++),
        );
    };

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("removeEditRow")) {
            e.target.closest("tr").remove();
        }
    });

    window.closeEditModal = function () {
        modal.style.display = "none";
    };

    function setVal(id, value) {
        const el = document.getElementById(id);
        if (el) el.value = value ?? "";
    }
    /* =====================================================
   AGE CALCULATION (EDIT MODAL)
===================================================== */

    let birthdayInput = document.getElementById("edit_birthday");
    let ageInput = document.getElementById("edit_age");

    /* Calculate age */
    function calculateAge(dateString) {
        if (!dateString) return "";

        const birth = new Date(dateString + "T00:00:00");
        const today = new Date();

        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();

        if (
            monthDiff < 0 ||
            (monthDiff === 0 && today.getDate() < birth.getDate())
        ) {
            age--;
        }

        return age;
    }

    /* Update age field */
    function updateAge() {
        if (!birthdayInput || !ageInput) return;

        const age = calculateAge(birthdayInput.value);
        ageInput.value = age;

        /* Optional validation highlight */
        if (age < 15 || age > 30) {
            ageInput.style.borderColor = "#ef4444";
        } else {
            ageInput.style.borderColor = "";
        }
    }

    /* Trigger when birthday changes */
    birthdayInput?.addEventListener("change", updateAge);
});
