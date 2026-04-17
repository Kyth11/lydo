function getFilePreview(file, isPath = false) {
    const name = isPath ? file.split("/").pop() : file.name;
    const ext = name.split(".").pop().toLowerCase();

    const url = isPath ? "/storage/" + file : URL.createObjectURL(file);

    // IMAGE
    if (["jpg", "jpeg", "png"].includes(ext)) {
        return `<img src="${url}" class="file-img">`;
    }

    // ICONS
    let icon = "/icons/file.png";

    if (ext === "pdf") icon = "/icons/pdf.png";
    if (["doc", "docx"].includes(ext)) icon = "/icons/word.png";
    if (["xls", "xlsx"].includes(ext)) icon = "/icons/excel.png";

    return `<img src="${icon}" class="file-icon-img">`;
}

document.addEventListener("DOMContentLoaded", function () {
    /* =========================
       ADD REPORT MODAL
    ========================= */
    const openBtn = document.getElementById("openReportModal");
    const reportModal = document.getElementById("reportModal");

    if (openBtn && reportModal) {
        openBtn.addEventListener("click", function (e) {
            e.preventDefault();
            reportModal.classList.add("active");
        });
    }

    /* =========================
       CLOSE MODAL (CLICK OUTSIDE)
    ========================= */
    document.querySelectorAll(".modal-overlay").forEach((modal) => {
        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                modal.classList.remove("active");
            }
        });
    });

    /* =========================
       FILE INPUT PREVIEW (ADD)
    ========================= */
    const fileInput = document.getElementById("fileInput");

    if (fileInput) {
        fileInput.addEventListener("change", function () {
            const preview = document.getElementById("uploadPreview");
            preview.innerHTML = "";

            Array.from(this.files).forEach((file) => {
                const ext = file.name.split(".").pop().toLowerCase();

                preview.innerHTML += `
    <div class="file-item">
        ${getFilePreview(file)}
        <div class="file-name">${file.name}</div>
    </div>
`;
            });
        });
    }
});

/* =========================
   GLOBAL MODAL HELPERS
========================= */
function openModal(id) {
    document.getElementById(id)?.classList.add("active");
}

function closeModal(id) {
    document.getElementById(id)?.classList.remove("active");
}

/* =========================
   ADMIN TOGGLE (FIXED)
========================= */
function toggleBrgy(el) {
    const content = el.nextElementSibling;
    if (!content) return;

    content.classList.toggle("active");

    document.querySelectorAll(".brgy-title").forEach((h) => {
        h.classList.remove("active-brgy");
    });

    el.classList.add("active-brgy");
}

/* =========================
   FILE MODAL (SORTED)
========================= */
function openFileModal(files) {
    const modal = document.getElementById("fileModal");
    const preview = document.getElementById("filePreview");

    if (!modal || !preview) return;

    preview.innerHTML = "";

    if (!files || files.length === 0) {
        preview.innerHTML = "<p>No files available</p>";
        modal.classList.add("active");
        return;
    }

    const images = [];
    const docs = [];

    files.forEach((file) => {
        const ext = file.split(".").pop().toLowerCase();

        if (["jpg", "jpeg", "png"].includes(ext)) {
            images.push(file);
        } else {
            docs.push(file);
        }
    });

    // 🔥 RENDER GROUP FUNCTION
    function renderGroup(title, list, type) {
        if (!list.length) return "";

        let html = `<div class="file-section">`;
        html += '<div class="event-divider"></div>';
        html += `<h5 class="file-section-title">${title}</h5>`;
        html += `<div class="file-grid">`;

        list.forEach((file) => {
            const ext = file.split(".").pop().toLowerCase();
            const url = "/storage/" + file;

            let content = getFilePreview(file, true);

            html += `
                <div class="file-item" onclick="window.open('${url}', '_blank')">
                    ${content}
<p class="file-name">${file.split("/").pop()}</p>
                </div>

            `;
        });

        html += `</div></div>`;
        return html;
    }
    let dailyCounter = 1;

    function generateFileName() {
        const now = new Date();

        const month = String(now.getMonth() + 1).padStart(2, "0");
        const day = String(now.getDate()).padStart(2, "0");
        const increment = String(dailyCounter++).padStart(3, "0");

        return `att${month}${day}${increment}`;
    }
    // 🔥 STACKED OUTPUT

    preview.innerHTML =
        renderGroup("Photos", images, "image") +
        renderGroup("Documents", docs, "doc");

    modal.classList.add("active");
}

/* =========================
   EDIT MODAL
========================= */
let selectedEditFiles = [];
let existingFiles = [];

function openEditModal(report) {
    openModal("editModal");

    document.getElementById("editForm").action = `/sk/report/${report.id}`;
    document.getElementById("editCategory").value = report.category_id;
    document.getElementById("editDescription").value = report.description || "";

    existingFiles = report.files || [];

    renderExistingFiles();
}

function renderExistingFiles() {
    const container = document.getElementById("existingFiles");
    const input = document.getElementById("existingFilesInput");

    if (!container || !input) return;

    container.innerHTML = "";

    existingFiles.forEach((file, index) => {
        container.innerHTML += `
            <div class="image-wrapper">
${getFilePreview(file, true)}
                <button type="button" onclick="removeExistingFile(${index})">X</button>
            </div>
        `;
    });

    input.value = JSON.stringify(existingFiles);
}

function removeExistingFile(index) {
    existingFiles.splice(index, 1);
    renderExistingFiles();
}

const editInput = document.getElementById("editFileInput");

if (editInput) {
    editInput.addEventListener("change", function (e) {
        selectedEditFiles = Array.from(e.target.files);
        renderEditPreview();
    });
}

function renderEditPreview() {
    const preview = document.getElementById("editPreview");
    if (!preview) return;

    preview.innerHTML = "";

    selectedEditFiles.forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.innerHTML += `
    <div class="image-wrapper">
        ${getFilePreview(file)}
        <button type="button" onclick="removeNewFile(${index})">X</button>
    </div>
`;
        };

        reader.readAsDataURL(file);
    });

    syncEditFiles();
}

function removeNewFile(index) {
    selectedEditFiles.splice(index, 1);
    renderEditPreview();
}

function syncEditFiles() {
    const dt = new DataTransfer();

    selectedEditFiles.forEach((file) => dt.items.add(file));

    document.getElementById("editFileInput").files = dt.files;
}

/* =========================
   DELETE CONFIRM
========================= */
function confirmDeleteReport() {
    return confirm("Are you sure you want to delete this report?");
}

/* =========================
   REVIEW MODAL
========================= */
function openActionModal(id, status, comment) {
    const modal = document.getElementById("reviewModal");
    const form = document.getElementById("reviewForm");

    if (!modal || !form) return;

    // open modal
    modal.classList.add("active");

    // set form action (route must match Laravel)
    form.action = "/admin/report/update";

    // assign values safely
    document.getElementById("reviewReportId").value = id;

    document.getElementById("reviewStatus").value = status ?? "pending";

    document.getElementById("reviewComment").value = comment ?? "";
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".row-approved, .row-rejected").forEach((row) => {
        row.style.animation = "flashFade 1.2s ease";
    });
});

function autoSubmit() {
    document.getElementById("filterForm").submit();
}

// 🔍 SEARCH DELAY (LIKE DATATABLES)
let typingTimer;
const searchInput = document.getElementById("searchInput");

if (searchInput) {
    searchInput.addEventListener("keyup", function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            autoSubmit();
        }, 500);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const deleteForms = document.querySelectorAll(".delete-form");

    deleteForms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Delete Report?",
                text: "This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "Cancel",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
