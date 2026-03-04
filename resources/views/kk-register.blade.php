@extends('layouts.public')
@section('page-title', 'KK Youth Registration')
@section('page-desc', 'Complete KK Youth Information')

@section('content')

    <style>
        .bold {
            font-weight: bold;
        }
        .youth-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .form-row>* {
            flex: 1 1 calc(33.333% - 1rem);
        }

        /* Tablet */
        @media (max-width: 1024px) {
            .form-row>* {
                flex: 1 1 calc(50% - 1rem);
            }
        }

        /* Mobile */
        @media (max-width: 640px) {
            .form-row {
                flex-direction: column;
            }

            .form-row>* {
                flex: 1 1 100%;
                width: 100%;
            }

            .form-input {
                width: 100%;
                font-size: 16px;
                /* Prevent zoom on iOS */
            }

            .btn {
                width: 50%;
                align-self: center;
                text-align: center;
            }
        }

        .form-input {
            flex: 1 1 0;
            min-width: 100px;
            padding: .65rem .8rem;
            border-radius: .5rem;
            border: 1px solid #d1d5db;
            width: 100%;
        }

        .save-btn {
            background: #4f46e5 !important;
            color: white !important;
            padding: .55rem 1.5rem !important;
            border-radius: .5rem !important;
            font-weight: 600 !important;
        }

        .save-btn:hover {
            background: #4338ca !important;
            translate: 0 -2px;
            transition: .2s ease-in-out;
        }



        .rmv-btn {
            background: #cd0000 !important;
            color: white !important;
            padding: .55rem 1.5rem !important;
            border-radius: .5rem !important;
            font-weight: 600 !important;
        }

        .rmv-btn:hover {
            background: #b30000 !important;
            translate: 0 -2px;
            transition: .2s ease-in-out;
        }

        .action-bar {
            position: sticky;
            top: 0;
            z-index: 20;
            padding: 1rem 0;
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn {
            padding: .65rem 1.5rem;
            border-radius: .5rem;
            font-weight: 600;
            transition: .2s ease;
            width: auto;
            max-width: 50%;

        }

        .btn-submit {
            background: #0700d7;
            color: white;
            text-align: center;
        }

        .btn-submit:hover {
            background: #140087;
            translate: 0 -2px;
            transition: .2s ease-in-out;
        }

        .btn-back {
            background: #cd0000;
            text-align: center;
            color: #ffffff;
        }

        .btn-back:hover {
            background: #b30000;
            translate: 0 -2px;
            transition: .2s ease-in-out;
        }

        /* Mobile stacking */
        @media (max-width: 640px) {
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }
        }
    </style>

    <div class="max-w-6xl mx-auto px-4 space-y-6">

        <div class="bg-white rounded-xl shadow p-6 relative">



            <!-- ACTION BUTTONS -->
            <div class="action-bar">
                <a href="{{ url('/') }}" class="btn btn-back">
                    Back
                </a>

                <button form="youthForm" type="submit" class="btn btn-submit">
                    Submit Registration
                </button>
            </div>

            <form id="youthForm" method="POST" action="{{ route('kk.register.store') }}" class="youth-form">
                @csrf

                <!-- I. Identifying Information -->
                <h4 class="bold">I. Identifying Information</h4>

                <div class="form-row">
                    <input name="first_name" class="form-input" placeholder="First Name" required>
                    <input name="middle_name" class="form-input" placeholder="Middle Name">
                    <input name="last_name" class="form-input" placeholder="Last Name" required>
                </div>

                <div class="form-row">
                    <select name="sex" class="form-input" required>
                        <option value="" class="bold">Sex</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>

                    <input id="birthday" type="date" name="birthday" class="form-input" required
                        max="{{ now()->toDateString() }}">

                    <input id="age" type="number" name="age" class="form-input" placeholder="Age" readonly>
                </div>

                <!-- Location -->
                <div class="form-row">
                    <input id="region" name="region" class="form-input" value="Northern Mindanao" required>
                    <input id="province" name="province" class="form-input" value="Misamis Oriental" required>
                    <input id="municipality" name="municipality" class="form-input" value="Opol" required>
                </div>

                <div class="form-row">
                    <select id="barangay" name="barangay" class="form-input" required>
                        <option value="" class="bold">Barangay</option>
                        @foreach (['Awang', 'Bagocboc', 'Barra', 'Bonbon', 'Cauyunan', 'Igpit', 'Limunda', 'Luyong Bonbon', 'Malanang', 'Nangcaon', 'Patag', 'Poblacion', 'Taboc', 'Tingalan'] as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>

                    <input id="purok_zone" name="purok_zone" class="form-input" placeholder="Purok / Zone" required>

                    <input id="home_address" name="home_address" class="form-input" placeholder="Home Address" required
                        readonly>
                </div>

                <div class="form-row">
                    <input name="religion" class="form-input" placeholder="Religion">
                    <select name="education" class="form-input" required>
                        <option value="" class="bold">Education Level</option>
                        <option>Elementary Level</option>
                        <option>Elementary Graduate</option>
                        <option>High School Level</option>
                        <option>High School Graduate</option>
                        <option>College Level</option>
                        <option>College Graduate</option>
                        <option>Vocational</option>
                    </select>
                </div>

                <div class="form-row">
                    <label><input type="checkbox" name="is_osy"> Out-of-School Youth</label>
                    <label><input type="checkbox" name="is_isy"> In-School Youth</label>
                    <label><input type="checkbox" name="is_working_youth"> Working Youth</label>
                </div>

                <div class="form-row">
                    <input name="skills" class="form-input" placeholder="Skills">
                    <input name="source_of_income" class="form-input" placeholder="Source of Income">
                    <input name="contact_number" class="form-input" placeholder="Contact Number">
                </div>

                <!-- II. Family Composition -->
                <h4 class="bold mt-6">II. Family Composition</h4>

                <div class="overflow-x-auto">
                    <table class="w-full border mt-2">
                        <thead>
                            <tr class="border">
                                <th>Family Member</th>
                                <th>Age</th>
                                <th>Relationship</th>
                                <th>Education</th>
                                <th>Occupation</th>
                                <th>Income</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="familyBody">
                            <tr>
                                <td><input class="form-input" name="family_members[0][name]"></td>
                                <td><input class="form-input" name="family_members[0][age]"></td>
                                <td>
                                    <select name="family_members[0][relationship]" class="form-input">
                                        <option value="" class="bold"></option>
                                        <option>Mother</option>
                                        <option>Father</option>
                                        <option>Brother</option>
                                        <option>Sister</option>
                                        <option>Grandparent</option>
                                        <option>Aunt</option>
                                        <option>Uncle</option>
                                        <option>Cousin</option>
                                        <option>Spouse</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="family_members[0][education]" class="form-input">
                                        <option value="" class="bold"></option>
                                        <option>None</option>
                                        <option>Pre-School</option>
                                        <option>Kindergarten</option>
                                        <option>Elementary Level</option>
                                        <option>Elementary Graduate</option>
                                        <option>High School Level</option>
                                        <option>High School Graduate</option>
                                        <option>College Level</option>
                                        <option>College Graduate</option>
                                        <option>Vocational</option>
                                    </select>
                                </td>
                                <td><input class="form-input" name="family_members[0][occupation]"></td>
                                <td><input class="form-input" name="family_members[0][income]"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="button" id="addFamilyRow" class="save-btn mt-3">
                        + Add Family Member
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ===== AGE AUTO COMPUTE ===== */
            const birthday = document.getElementById('birthday');
            const age = document.getElementById('age');

            function calcAge(v) {
                if (!v) return '';
                const d = new Date(v + 'T00:00:00');
                const t = new Date();
                let a = t.getFullYear() - d.getFullYear();
                const m = t.getMonth() - d.getMonth();
                if (m < 0 || (m === 0 && t.getDate() < d.getDate())) a--;
                return a;
            }

            function updateAge() {
                age.value = calcAge(birthday.value);
            }

            birthday.addEventListener('change', updateAge);
            birthday.addEventListener('input', updateAge);

            /* ===== AUTO HOME ADDRESS BUILD ===== */
            const region = document.getElementById('region');
            const province = document.getElementById('province');
            const municipality = document.getElementById('municipality');
            const barangay = document.getElementById('barangay');
            const purok = document.getElementById('purok_zone');
            const home = document.getElementById('home_address');

            function buildHomeAddress() {
                const parts = [
                    purok.value?.trim(),
                    barangay.value?.trim(),
                    municipality.value?.trim(),
                    province.value?.trim(),
                    region.value?.trim()
                ].filter(p => p && p.length > 0);

                home.value = parts.join(', ');
            }

            [region, province, municipality, barangay, purok].forEach(el => {
                el.addEventListener('input', buildHomeAddress);
                el.addEventListener('change', buildHomeAddress);
            });

            buildHomeAddress();

            /* ===== DYNAMIC FAMILY ROWS ===== */
            let familyIndex = 1;

            document.getElementById('addFamilyRow').addEventListener('click', function() {
                const tbody = document.getElementById('familyBody');
                const row = document.createElement('tr');

                row.innerHTML = `
            <td><input class="form-input" name="family_members[${familyIndex}][name]"></td>
            <td><input class="form-input" name="family_members[${familyIndex}][age]"></td>
            <td><input class="form-input" name="family_members[${familyIndex}][relationship]"></td>
            <td><input class="form-input" name="family_members[${familyIndex}][education]"></td>
            <td><input class="form-input" name="family_members[${familyIndex}][occupation]"></td>
            <td><input class="form-input" name="family_members[${familyIndex}][income]"></td>
            <td><button type="button" class="rmv-btn removeRow">Remove</button></td>
        `;

                tbody.appendChild(row);
                familyIndex++;
            });

            document.getElementById('familyBody').addEventListener('click', function(e) {
                if (e.target.classList.contains('removeRow')) {
                    e.target.closest('tr').remove();
                }
            });

        });
    </script>

    {{-- SWEET ALERT --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#16a34a'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#dc2626'
            });
        </script>
    @endif

@endsection
