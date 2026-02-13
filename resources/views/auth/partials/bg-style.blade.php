<style>
    html, body {
        height: 100% !important;
        margin: 0 !important;
        overflow: hidden !important;
    }

    .bg-fixed-layer {
        position: fixed;
        inset: 0;
        z-index: -2;
        background-image: url('/images/LydoCover.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        transform: scale(1.05);
    }

    .bg-fixed-layer::after {
        content: '';
        position: absolute;
        inset: 0;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        background: rgba(0, 0, 0, 0.25);
    }

    .min-h-screen { background: transparent !important; }

    .login-form {
        background: transparent !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 2.5rem;
        max-width: 420px;
        width: 100%;
        margin: auto;
    }

    .password-wrapper { position: relative; }
    .password-input { padding-right: 3rem; }
    .password-eye {
        position: absolute;
        top: 50%;
        right: .75rem;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6b7280;
    }
    .min-h-screen {
    background: transparent !important;
}

</style>

<script>
    function togglePassword(id, el) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            el.textContent = "👁";
        } else {
            input.type = "password";
            el.textContent = "🙈";
        }
    }
</script>
