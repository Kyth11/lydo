function showAdminPasswordModal(title, confirmText, confirmColor, callback) {

    Swal.fire({
        title: title,
        input: 'password',
        inputPlaceholder: 'Enter Admin Password',
        showCancelButton: true,
        confirmButtonText: confirmText,
        confirmButtonColor: confirmColor,
        preConfirm: (password) => {
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            callback(result.value);
        }
    });
}

// 🔒 Toggle SK Archive
function toggleProtection() {

    showAdminPasswordModal(
        'Admin Verification Required',
        'Verify',
        '#4f46e5',
        function(password) {

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "/admin/toggle-protection";

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]')?.content;

            const pass = document.createElement('input');
            pass.type = 'hidden';
            pass.name = 'password';
            pass.value = password;

            form.appendChild(csrf);
            form.appendChild(pass);

            document.body.appendChild(form);
            form.submit();
        }
    );
}

// 🔓 Toggle KK Register
function toggleKKRegister() {

    showAdminPasswordModal(
        'Admin Verification Required',
        'Verify',
        '#f59e0b',
        function(password) {

            fetch("/admin/toggle-kk", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ password: password })
            })
            .then(res => res.json())
            .then(data => {

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated Successfully'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Incorrect Password'
                    });
                }

            });
        }
    );
}

// 📬 Nudge Notifications
function loadNudges() {
    const nudgesUrl = document.body.dataset.nudgesUrl;
    if (!nudgesUrl) return;

    fetch(nudgesUrl)
        .then(response => response.json())
        .then(nudges => {
            const notificationList = document.getElementById('notificationList');
            const notificationBadge = document.getElementById('notificationBadge');

            if (nudges.length === 0) {
                notificationList.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">No new notifications</div>';
                notificationBadge.style.display = 'none';
            } else {
                notificationBadge.textContent = nudges.length;
                notificationBadge.style.display = 'inline-flex';

                let clearAllBtn = '';
                if (nudges.length > 1) {
                    clearAllBtn = `
                        <div class="p-3 border-b border-gray-200">
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); clearAllNudges()" class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium py-2 px-4 rounded hover:bg-gray-100">
                                Clear All
                            </button>
                        </div>
                    `;
                }

                notificationList.innerHTML = clearAllBtn + nudges.map(nudge => `
                    <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 cursor-pointer" onclick="openNudgeReport('${nudge.category_id}')">
                                <p class="font-semibold text-gray-800 text-sm">📢 Nudge from Admin</p>
                                <p class="text-gray-600 text-xs mt-1">Category: <strong>${nudge.category_name}</strong> ${nudge.duration ? `(${nudge.duration})` : ''}</p>
                                ${nudge.message ? `<p class="text-gray-700 text-sm mt-2">${nudge.message}</p>` : ''}
                                <p class="text-gray-400 text-xs mt-2">${new Date(nudge.created_at).toLocaleString()}</p>
                            </div>
                            <button onclick="event.preventDefault(); event.stopPropagation(); clearNudge(${nudge.id})" class="ml-2 text-gray-400 hover:text-red-500 text-sm p-1 rounded hover:bg-gray-100">
                                ✕
                            </button>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading nudges:', error);
            const notificationList = document.getElementById('notificationList');
            if (notificationList) {
                notificationList.innerHTML = '<div class="p-4 text-center text-red-500 text-sm">Error loading notifications</div>';
            }
        });
}

function clearNudge(nudgeId) {
    console.log('Clearing nudge:', nudgeId);
    fetch(`/sk/nudges/clear/${nudgeId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Clear response:', data);
        if (data.success) {
            loadNudges();
        }
    })
    .catch(error => {
        console.error('Error clearing nudge:', error);
    });
}

function clearAllNudges() {
    console.log('Clearing all nudges');

    Swal.fire({
        title: 'Clear All Notifications?',
        text: 'This will remove all your nudges.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Clear All'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/sk/nudges/clear-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNudges();
                    Swal.fire({
                        icon: 'success',
                        title: 'Cleared',
                        text: 'All notifications have been cleared.',
                        confirmButtonColor: '#16a34a'
                    });
                }
            })
            .catch(error => {
                console.error('Error clearing all nudges:', error);
            });
        }
    });
}

function openNudgeReport(categoryId) {
    window.location.href = `/sk/monitoring?nudge_category=${categoryId}`;
}

function confirmReportSubmit(event) {
    event.preventDefault();

    const form = document.getElementById('reportForm');
    const categoryId = form.querySelector('select[name="category_id"]').value;

    if (!categoryId) {
        Swal.fire({
            icon: 'warning',
            title: 'Category Required',
            text: 'Please select a category before submitting.',
            confirmButtonColor: '#f59e0b'
        });
        return;
    }

    // Check if report already exists for this category
    fetch(`/sk/check-report/${categoryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                Swal.fire({
                    title: 'Report Already Submitted',
                    text: 'You have already submitted a report for this category. Do you want to submit another one?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Submit',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                Swal.fire({
                    title: 'Submit Report?',
                    text: 'Are you sure you want to submit this report?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error checking report:', error);
            // If check fails, just submit
            form.submit();
        });
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('notificationList')) {
        loadNudges();
        setInterval(loadNudges, 10000);
    }
});
