@extends('layouts.app')

@section('page-title', 'Manage SK Accounts')
@section('page-desc', 'Enable or disable SK user accounts')

@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<style>
/* 🔥 Override Breeze button/link styles */
.action-btn {
    display: inline-block;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #ffffff !important;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all 0.25s ease;
}

/* Disable button */
.btn-disable {
    background-color: #dc2626;
}

/* Enable button */
.btn-enable {
    background-color: #16a34a;
}

/* Hover animation (transpose effect) */
.action-btn:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
</style>

<div class="max-w-6xl mx-auto px-4 space-y-6">

    <div class="bg-white rounded-xl shadow p-6 relative">

        <div class="overflow-x-auto">
            <table id="skTable" class="display w-full">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Barangay</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($sks as $sk)
                        <tr>
                            <td>{{ $sk->name }}</td>
                            <td>{{ $sk->email }}</td>
                            <td>{{ $sk->barangay }}</td>
                            <td>
                                @if($sk->is_disabled)
                                    <span style="color:#dc2626; font-weight:600;">Disabled</span>
                                @else
                                    <span style="color:#16a34a; font-weight:600;">Active</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button
                                    type="button"
                                    onclick="handleToggle({{ $sk->id }})"
                                    class="action-btn {{ $sk->is_disabled ? 'btn-enable' : 'btn-disable' }}">

                                    {{ $sk->is_disabled ? 'Enable Account' : 'Disable Account' }}

                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#skTable').DataTable({
        pageLength: 10,
        lengthChange: false
    });
});

function handleToggle(id) {

    Swal.fire({
        title: 'Admin Verification Required',
        input: 'password',
        inputLabel: 'Enter your password',
        inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Confirm'
    }).then(result => {

        if (result.isConfirmed && result.value) {

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/sk/' + id + '/toggle';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";

            const pass = document.createElement('input');
            pass.type = 'hidden';
            pass.name = 'password';
            pass.value = result.value;

            form.appendChild(csrf);
            form.appendChild(pass);

            document.body.appendChild(form);
            form.submit();
        }

    });
}
</script>

@endsection
