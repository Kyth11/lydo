function openEditModal(cat) {
    document.getElementById("edit_id").value = cat.id;
    document.getElementById("edit_name").value = cat.name ?? "";

    document.getElementById("edit_start").value = cat.start_date ?? "";
    document.getElementById("edit_end").value = cat.end_date ?? "";
    document.getElementById("edit_deadline").value = cat.deadline ?? "";

    document.getElementById("editModal").classList.add("active");
}

function confirmArchive(id) {
    Swal.fire({
        title: "Archive Category?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, archive",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `/admin/categories/archive/${id}`;
        }
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: "Delete Category?",
        text: "This cannot be undone.",
        icon: "error",
        showCancelButton: true,
        confirmButtonText: "Delete",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("delete-form-" + id).submit();
        }
    });
}

function openEditModal(cat) {
    document.getElementById("edit_id").value = cat.id;
    document.getElementById("edit_name").value = cat.name ?? "";

    document.getElementById("edit_start").value = cat.start_date ?? "";
    document.getElementById("edit_end").value = cat.end_date ?? "";
    document.getElementById("edit_deadline").value = cat.deadline ?? "";

    // lock duration if already used
    document.getElementById("edit_start").readOnly = false;
    document.getElementById("edit_end").readOnly = false;

    document.getElementById("editModal").classList.add("active");
}

function closeModal(id) {
    document.getElementById(id).classList.remove("active");
}
