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
