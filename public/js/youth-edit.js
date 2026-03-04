let editFamilyIndex = 0;

function openEditModal(youth) {
    const modal = document.getElementById("editModal");
    const form = document.getElementById("editForm");
    const familyBody = document.getElementById("editFamilyBody");

    form.action = `/youth/${youth.id}`;

    // BASIC INFO
    setVal("edit_first_name", youth.first_name);
    setVal("edit_middle_name", youth.middle_name);
    setVal("edit_last_name", youth.last_name);
    setVal("edit_sex", youth.sex);
    setVal("edit_gender", youth.gender);
    setVal("edit_civil_status", youth.civil_status);
    setVal("edit_education", youth.education);
    setVal("edit_skills", youth.skills);
    setVal("edit_preferred_skills", youth.preferred_skills);
    setVal("edit_source_of_income", youth.source_of_income);
    setVal("edit_contact_number", youth.contact_number);

    if (youth.birthday) {
        document.getElementById("edit_birthday").value =
            youth.birthday.split("T")[0];
    }

    setVal("edit_age", youth.age);
    setVal("edit_region", youth.region);
    setVal("edit_province", youth.province);
    setVal("edit_municipality", youth.municipality);
    setVal("edit_barangay", youth.barangay);
    setVal("edit_purok_zone", youth.purok_zone);
    setVal("edit_home_address", youth.home_address);

    // RELIGION (OTHERS)
    setVal("edit_religion", youth.religion);
    toggleEditReligion(youth.religion);

    if (youth.religion_other) {
        setVal("edit_religion_other", youth.religion_other);
    }

    // SK VOTER
    document.querySelectorAll('input[name="is_sk_voter"]').forEach((radio) => {
        radio.checked = radio.value === youth.is_sk_voter;
    });

    // CHECKBOXES
    [
        "is_osy",
        "is_isy",
        "is_4ps",
        "is_ip",
        "is_pwd",
        "is_unemployed",
        "is_employed",
        "is_self_employed",
    ].forEach((f) => {
        const el = document.getElementById(`edit_${f}`);
        if (el) el.checked = youth[f] == 1;
    });

    // PHOTO
    document.getElementById("editPhotoPreview").src = youth.profile_photo
        ? `/storage/${youth.profile_photo}`
        : "/images/avatar.png";

    // FAMILY MEMBERS
    familyBody.innerHTML = "";
    editFamilyIndex = 0;

    if (Array.isArray(youth.family_members) && youth.family_members.length) {
        youth.family_members.forEach((member) => {
            familyBody.insertAdjacentHTML(
                "beforeend",
                buildEditFamilyRow(member, editFamilyIndex++),
            );
        });
    } else {
        addEditFamilyRow();
    }

    modal.style.display = "flex";
}

/* ================= HELPERS ================= */

function setVal(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value ?? "";
}

/* RELIGION OTHERS */
function toggleEditReligion(value) {
    const input = document.getElementById("edit_religion_other");
    if (value === "Others") {
        input.style.display = "block";
        input.required = true;
    } else {
        input.style.display = "none";
        input.required = false;
        input.value = "";
    }
}

document.getElementById("edit_religion").addEventListener("change", (e) => {
    toggleEditReligion(e.target.value);
});

/* FAMILY ROW */
function buildEditFamilyRow(m, i) {
    return `
<tr>
<td><input class="form-input" name="family_members[${i}][name]" value="${m.name ?? ""}"></td>
<td><input type="number" class="form-input" name="family_members[${i}][age]" value="${m.age ?? ""}"></td>
<td>
<select name="family_members[${i}][relationship]" class="form-input">
${relOpt(m.relationship)}
</select>
</td>
<td>
<select name="family_members[${i}][education]" class="form-input">
${eduOpt(m.education)}
</select>
</td>
<td><input class="form-input" name="family_members[${i}][occupation]" value="${m.occupation ?? ""}"></td>
<td><input type="number" class="form-input" name="family_members[${i}][income]" value="${m.income ?? ""}"></td>
<td><button type="button" style="background: white !important;
            color: red !important;
            padding: .55rem 1.5rem !important;
            border: 2px solid red !important;
            border-radius: .5rem !important;
            font-weight: 600 !important;
            font-size: 14px !important;" onmouseover="this.style.transform='translateY(-2px)'; this.style.transition='.2s ease-in-out'; this.style.background='red'; this.style.color='white'" onmouseout="this.style.transform='translateY(0)'; this.style.transition='.2s ease-in-out'; this.style.background='white'; this.style.color='red'">Remove</button></td>
</tr>`;
}

function relOpt(v) {
    return [
        "Mother",
        "Father",
        "Brother",
        "Sister",
        "Grandparent",
        "Aunt",
        "Uncle",
        "Cousin",
        "Spouse",
    ]
        .map((r) => `<option ${v === r ? "selected" : ""}>${r}</option>`)
        .join("");
}

function eduOpt(v) {
    return [
        "None",
        "Pre-School",
        "Kindergarten",
        "Elementary Level",
        "Elementary Graduate",
        "High School Level",
        "High School Graduate",
        "College Level",
        "College Graduate",
        "Vocational",
    ]
        .map((e) => `<option ${v === e ? "selected" : ""}>${e}</option>`)
        .join("");
}

function addEditFamilyRow() {
    document
        .getElementById("editFamilyBody")
        .insertAdjacentHTML(
            "beforeend",
            buildEditFamilyRow({}, editFamilyIndex++),
        );
}

document.addEventListener("click", (e) => {
    if (e.target.classList.contains("removeEditRow")) {
        e.target.closest("tr").remove();
    }
});

function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}

function previewEditPhoto(e) {
    const r = new FileReader();
    r.onload = () =>
        (document.getElementById("editPhotoPreview").src = r.result);
    r.readAsDataURL(e.target.files[0]);
}
