<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>KK Profiling Form</title>

    <style>
        @page { size: A4; margin: 25mm; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .header {
            text-align: center;
            position: relative;
        }

        .logo-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 70px;
        }

        .logo-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 70px;
        }

        h2 {
            margin-top: 10px;
            font-size: 16px;
        }

        hr {
            margin: 12px 0;
        }

        .section-title {
            font-weight: bold;
            margin-top: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        td, th {
            padding: 6px;
            border: 1px solid #000;
        }

        .no-border td {
            border: none;
            padding: 4px 2px;
        }

        .label {
            width: 180px;
            font-weight: bold;
        }

        .checkbox {
            display: inline-block;
            margin-right: 15px;
        }

        .footer {
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <img src="{{ public_path('images/OpolLogo.png') }}" class="logo-left">
        <img src="{{ public_path('images/LydoLogo.png') }}" class="logo-right">

        <div>Republic of the Philippines</div>
        <div>Province of Misamis Oriental</div>
        <div>Municipality of Opol</div>
        <div><strong>LOCAL YOUTH DEVELOPMENT OFFICE</strong></div>

        <hr>
        <h2>PROFILING FORM</h2>
    </div>

    <!-- IDENTIFYING INFORMATION -->
    <div class="section-title">I. Identifying Information</div>

    <table class="no-border">
        <tr>
            <td class="label">Full Name:</td>
            <td>
                {{ $youth->first_name }}
                {{ $youth->middle_name ? $youth->middle_name : '' }}
                {{ $youth->last_name }}
            </td>
        </tr>
        <tr>
            <td class="label">Sex:</td>
            <td>{{ $youth->sex }}</td>
        </tr>
        <tr>
            <td class="label">Birthday:</td>
            <td>{{ \Carbon\Carbon::parse($youth->birthday)->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Age:</td>
            <td>{{ $youth->age }}</td>
        </tr>
        <tr>
            <td class="label">Home Address:</td>
            <td>{{ $youth->home_address }}</td>
        </tr>
        <tr>
            <td class="label">Religion:</td>
            <td>{{ $youth->religion }}</td>
        </tr>
        <tr>
            <td class="label">Educational Attainment:</td>
            <td>{{ $youth->education }}</td>
        </tr>
        <tr>
            <td class="label">Contact Number:</td>
            <td>{{ $youth->contact_number }}</td>
        </tr>
        <tr>
            <td class="label">Skills:</td>
            <td>{{ $youth->skills }}</td>
        </tr>
        <tr>
            <td class="label">Source of Income:</td>
            <td>{{ $youth->source_of_income }}</td>
        </tr>
        <tr>
            <td class="label">Youth Classification:</td>
            <td>
                <span class="checkbox">[ {{ $youth->is_osy ? '✔' : ' ' }} ] OSY</span>
                <span class="checkbox">[ {{ $youth->is_isy ? '✔' : ' ' }} ] ISY</span>
                <span class="checkbox">[ {{ $youth->is_working_youth ? '✔' : ' ' }} ] WY</span>
            </td>
        </tr>
        <tr>
            <td class="label">Barangay:</td>
            <td>{{ $youth->barangay }}</td>
        </tr>
    </table>

    <!-- FAMILY COMPOSITION -->
    <div class="section-title">II. Family Composition</div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Relationship</th>
                <th>Education</th>
                <th>Occupation</th>
                <th>Income</th>
            </tr>
        </thead>
        <tbody>
            @foreach($youth->family_members ?? [] as $member)
            <tr>
                <td>{{ $member['name'] ?? '' }}</td>
                <td>{{ $member['age'] ?? '' }}</td>
                <td>{{ $member['relationship'] ?? '' }}</td>
                <td>{{ $member['education'] ?? '' }}</td>
                <td>{{ $member['occupation'] ?? '' }}</td>
                <td>{{ $member['income'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER SIGNATURES -->
    <div class="footer">
        <table class="no-border">
            <tr>
                <td>Signature: ____________________________</td>
                <td style="text-align:right;">Interviewed by: ____________________________</td>
            </tr>
        </table>
    </div>

</body>
</html>
