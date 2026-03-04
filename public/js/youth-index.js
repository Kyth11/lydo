/* =====================================================
   YOUTH INDEX — TABLE
===================================================== */

$(document).ready(function () {
    $("#youthTable").DataTable({
        order: [[0, "asc"]],
        pageLength: 10,
        lengthChange: false,
        columnDefs: [
            { orderable: false, targets: 5 }
        ],
    });
});


/* =====================================================
   ARCHIVE / RESTORE / DELETE
===================================================== */

function confirmArchive(id) {
    Swal.fire({
        title: "Archive profile?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Yes, archive",
    }).then(result => {
        if (result.isConfirmed) {
            submitProtectedAction(`/youth/${id}/archive`, null, "PATCH");
        }
    });
}

function confirmRestore(id) {
    Swal.fire({
        title: "Restore profile?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#22c55e",
        confirmButtonText: "Yes, restore",
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = `/youth/${id}/restore`;
        }
    });
}

function handleArchive(id, protectedMode) {
    if (!protectedMode) {
        submitProtectedAction(`/youth/${id}/archive`, null, "PATCH");
        return;
    }

    Swal.fire({
        title: "Admin Verification Required",
        input: "password",
        inputLabel: "Enter your password to archive",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Confirm",
    }).then(result => {
        if (result.isConfirmed && result.value) {
            submitProtectedAction(
                `/youth/${id}/archive`,
                result.value,
                "PATCH"
            );
        }
    });
}

function handleRestore(id, protectedMode) {
    if (!protectedMode) {
        window.location.href = `/youth/${id}/restore`;
        return;
    }

    Swal.fire({
        title: "Admin Verification Required",
        input: "password",
        inputLabel: "Enter your password to restore",
        showCancelButton: true,
        confirmButtonColor: "#22c55e",
        confirmButtonText: "Confirm",
    }).then(result => {
        if (result.isConfirmed && result.value) {
            submitProtectedAction(`/youth/${id}/restore`, result.value);
        }
    });
}

function handleDelete(id, protectedMode) {
    Swal.fire({
        title: "Permanently delete this profile?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Yes, delete permanently",
    }).then(result => {
        if (!result.isConfirmed) return;

        if (!protectedMode) {
            submitProtectedAction(`/youth/${id}/delete`);
            return;
        }

        Swal.fire({
            title: "Admin Verification Required",
            input: "password",
            inputLabel: "Enter your password to permanently delete",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            confirmButtonText: "Confirm",
        }).then(result => {
            if (result.isConfirmed && result.value) {
                submitProtectedAction(`/youth/${id}/delete`, result.value);
            }
        });
    });
}


/* =====================================================
   PROTECTED FORM SUBMITTER
===================================================== */

function submitProtectedAction(action, password = null, method = "POST") {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = action;

    const csrf = document.createElement("input");
    csrf.type = "hidden";
    csrf.name = "_token";
    csrf.value = window.csrfToken; // <-- see note below
    form.appendChild(csrf);

    if (method !== "POST") {
        const m = document.createElement("input");
        m.type = "hidden";
        m.name = "_method";
        m.value = method;
        form.appendChild(m);
    }

    if (password !== null) {
        const pass = document.createElement("input");
        pass.type = "hidden";
        pass.name = "password";
        pass.value = password;
        form.appendChild(pass);
    }

    document.body.appendChild(form);
    form.submit();
}


/* =====================================================
   PRINT OPTIONS
===================================================== */

function openPrintOptions(id) {
    Swal.fire({
        title: "Choose Output Option",
        icon: "question",
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: "Download PDF",
        denyButtonText: "Direct Print",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#4f46e5",
        denyButtonColor: "#22c55e",
    }).then(result => {
        if (result.isConfirmed) {
            window.open(`/youth/${id}/pdf`, "_blank");
        }
        if (result.isDenied) {
            window.location.href = `/youth/${id}/print`;
        }
    });
}
