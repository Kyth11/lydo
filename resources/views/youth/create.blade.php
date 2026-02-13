@extends('layouts.app')
@section('page-title', 'Add Profile')
@section('page-desc', 'Complete KK Youth Information')

@section('content')
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        <div class="bg-white rounded-xl shadow p-6 relative">

            <!-- Sticky Save Button -->
            <div class="save-bar">
                <button form="youthForm" type="submit" class="save-btn">
                    Save Profile
                </button>
            </div>

            <form id="youthForm" method="POST" action="/youth" class="youth-form">
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

                <!-- Location (DEFAULT VALUES SET) -->
                <div class="form-row">
                    <input id="region" name="region" class="form-input" value="Northern Mindanao" required>
                    <input id="province" name="province" class="form-input" value="Misamis Oriental" required>
                    <input id="municipality" name="municipality" class="form-input" value="Opol" required>
                </div>

                <!-- Barangay + Home Address -->
@php
    $user = auth()->user();
@endphp

<div class="form-row">
    <select id="barangay"
            name="barangay"
            class="form-input"
            required
            {{ $user && $user->role === 'sk' ? 'disabled' : '' }}>

        <option value="" class="bold">Barangay</option>

        @foreach([
            'Awang','Bagocboc','Barra','Bonbon','Cauyunan','Igpit','Limunda',
            'Luyong Bonbon','Malanang','Nangcaon','Patag','Poblacion','Taboc','Tingalan'
        ] as $b)

            <option value="{{ $b }}"
                @if($user && $user->role === 'sk' && $user->barangay === $b)
                    selected
                @endif
            >
                {{ $b }}
            </option>

        @endforeach
    </select>

    {{-- Hidden input so disabled select still submits --}}
    @if($user && $user->role === 'sk')
        <input type="hidden" name="barangay" value="{{ $user->barangay }}">
    @endif

    <input id="home_address"
           name="home_address"
           class="form-input"
           placeholder="Home Address"
           required
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
                                <th>Educational Attainment</th>
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
        document.addEventListener('DOMContentLoaded', function () {
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

            function update() {
                age.value = calcAge(birthday.value);
            }

            birthday.addEventListener('change', update);
            birthday.addEventListener('input', update);

            // 🔁 ORIGINAL HOME ADDRESS LOGIC (REVERTED)
            const region = document.getElementById('region');
            const province = document.getElementById('province');
            const municipality = document.getElementById('municipality');
            const barangay = document.getElementById('barangay');
            const homeAddress = document.getElementById('home_address');

            function buildHomeAddress() {
                const parts = [
                    barangay.value,
                    municipality.value,
                    province.value,
                    region.value
                ].filter(Boolean);

                homeAddress.value = parts.join(', ');
            }

            [region, province, municipality, barangay].forEach(el => {
                el.addEventListener('input', buildHomeAddress);
                el.addEventListener('change', buildHomeAddress);
            });

            buildHomeAddress();

            // Dynamic family rows
            let familyIndex = 1;
            document.getElementById('addFamilyRow').addEventListener('click', function () {
                const tbody = document.getElementById('familyBody');
                const row = document.createElement('tr');

                row.innerHTML = `
                <td><input class="form-input" name="family_members[${familyIndex}][name]"></td>
                <td><input class="form-input" name="family_members[${familyIndex}][age]"></td>
                <td>
                    <select name="family_members[${familyIndex}][relationship]" class="form-input">
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
                    <select name="family_members[${familyIndex}][education]" class="form-input">
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
                <td><input class="form-input" name="family_members[${familyIndex}][occupation]"></td>
                <td><input class="form-input" name="family_members[${familyIndex}][income]"></td>
                <td>
                    <button type="button" class="save-btn removeRow">Remove</button>
                </td>
            `;

                tbody.appendChild(row);
                familyIndex++;
            });

            document.getElementById('familyBody').addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>

    <style>
        .youth-form {
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }

        .bold {
            font-weight: 700 !important;
        }

        .form-row {
            display: flex !important;
            gap: 1rem !important;
            flex-wrap: wrap !important;
        }

        .form-input {
            flex: 1 1 0 !important;
            min-width: 160px !important;
            padding: .6rem .75rem !important;
            border-radius: .5rem !important;
            border: 1px solid #d1d5db !important;
        }

        .save-bar {
            position: sticky !important;
            top: 5.5rem !important;
            z-index: 30 !important;
            /* background: white !important; */
            padding: .75rem 0 !important;
            margin-bottom: 1rem !important;
            /* border-bottom: 1px solid #e5e7eb !important; */
            display: flex !important;
            justify-content: flex-end !important;
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
        }
    </style>
@endsection
