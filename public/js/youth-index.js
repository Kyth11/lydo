/* =====================================================
    YOUTH INDEX — TABLE
    ===================================================== */

$(document).ready(function () {
    $("#youthTable").DataTable({
        /* SORT BY BARANGAY POPULATION DESCENDING */
        order: [[1, "desc"]],

        pageLength: 10,
        lengthChange: false,

        columnDefs: [
            /* FORCE POPULATION COLUMN TO NUMERIC */
            {
                targets: 1,
                render: function (data, type, row) {
                    if (type === "sort" || type === "type") {
                        return parseInt(data.toString().replace(/,/g, "")) || 0;
                    }

                    return data;
                },
            },

            /* ACTION BUTTONS COLUMN NOT SORTABLE */
            {
                orderable: false,
                targets: -1,
            },
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
    }).then((result) => {
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
    }).then((result) => {
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
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            submitProtectedAction(
                `/youth/${id}/archive`,
                result.value,
                "PATCH",
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
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            submitProtectedAction(`/youth/${id}/restore`, result.value);
        }
    });
}

function handleDelete(id, protectedMode) {
    if (!protectedMode) {
        Swal.fire({
            title: "Permanently delete this profile?",
            text: "This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            confirmButtonText: "Yes, delete permanently",
        }).then((result) => {
            if (result.isConfirmed) {
                submitProtectedAction(`/youth/${id}/delete`);
            }
        });
        return;
    }

    // Use styled admin password modal if available (admin users)
    if (typeof showAdminPasswordModal === 'function') {
        showAdminPasswordModal(
            'Admin Verification Required',
            'Confirm Permanent Deletion',
            '#ef4444',
            function(password) {
                submitProtectedAction(`/youth/${id}/delete`, password);
            }
        );
    } else {
        // Fallback for non-admin users
        Swal.fire({
            title: "Admin Verification Required",
            input: "password",
            inputLabel: "Enter your password to permanently delete",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            confirmButtonText: "Confirm",
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                submitProtectedAction(`/youth/${id}/delete`, result.value);
            }
        });
    }
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
    csrf.value = window.csrfToken;

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
    }).then((result) => {
        if (result.isConfirmed) {
            window.open(`/youth/${id}/pdf`, "_blank");
        }

        if (result.isDenied) {
            window.open(`/youth/${id}/print`, "_blank");
        }
    });
}

/* =================================
    BARANGAY POPULATION EDIT
    ================================= */

document.querySelectorAll(".edit-population").forEach((btn) => {
    btn.addEventListener("click", function () {
        const barangay = this.dataset.barangay;

        const display = document.querySelector(
            `.population-display[data-barangay="${barangay}"]`,
        );

        display.querySelector(".population-value").classList.add("hidden");

        display.querySelector(".population-input").classList.remove("hidden");

        this.classList.add("hidden");

        document
            .querySelector(`.save-population[data-barangay="${barangay}"]`)
            .classList.remove("hidden");
    });
});

/* SAVE */

document.querySelectorAll(".save-population").forEach((btn) => {
    btn.addEventListener("click", function () {
        const barangay = this.dataset.barangay;

        const input = document.querySelector(
            `.population-input[data-barangay="${barangay}"]`,
        );

        const value = parseInt(input.value) || 0;

        fetch("/barangay-population/update", {
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": window.csrfToken,
            },

            body: JSON.stringify({
                barangay: barangay,
                population: value,
            }),
        })
            .then((res) => res.json())

            .then((data) => {
                if (data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Population Updated",
                        confirmButtonColor: "#16a34a",
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Update Failed",
                    });
                }
            });
    });
});

/* =================================
   EDIT ALL POPULATION
================================= */

document
    .getElementById("editAllPopulation")
    ?.addEventListener("click", function () {
        document.querySelectorAll(".population-display").forEach((display) => {
            display.querySelector(".population-value").classList.add("hidden");
            display
                .querySelector(".population-input")
                .classList.remove("hidden");
        });

        document.querySelectorAll(".edit-population").forEach((btn) => {
            btn.classList.add("hidden");
        });

        document.querySelectorAll(".save-population").forEach((btn) => {
            btn.classList.remove("hidden");
        });

        this.classList.add("hidden");

        document.getElementById("saveAllPopulation").classList.remove("hidden");
    });
/* =================================
   SAVE ALL POPULATION
================================= */

document
    .getElementById("saveAllPopulation")
    ?.addEventListener("click", function () {
        const updates = [];

        document.querySelectorAll(".population-input").forEach((input) => {
            updates.push({
                barangay: input.dataset.barangay,
                population: parseInt(input.value) || 0,
            });
        });

        fetch("/barangay-population/update-all", {
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": window.csrfToken,
            },

            body: JSON.stringify({
                updates: updates,
            }),
        })
            .then((res) => res.json())

            .then((data) => {
                if (data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "All Populations Updated",
                        confirmButtonColor: "#16a34a",
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Update Failed",
                    });
                }
            });
    });
