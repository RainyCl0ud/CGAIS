<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Personal Data Sheet</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color:#111; font-size:12px; }
        .header { text-align:center; margin-bottom:8px; }
        .logo { width:85px; height:auto; display:block; margin:0 auto 4px; }
        .title { font-weight:700; font-size:14px; text-transform:uppercase; }
        .subtitle { font-size:10px; text-transform:uppercase; }
        .container { width:100%; max-width:800px; margin:0 auto; }
        .photo-box { width:120px; height:120px; border:1px solid #000; float:right; text-align:center; overflow:hidden; }
        .photo-box img { width:100%; height:100%; object-fit:cover; }
        .section { margin-top:10px; clear:both; }
        .label { font-weight:600; }
        .field { border:1px solid #666; padding:4px 4px; min-height:16px; }
        table { width:100%; border-collapse:collapse; margin-top:6px; }
        td, th { padding:6px; vertical-align:top; }
        .muted { color:#666; font-size:11px; }
        .section-title { font-weight:700; background:#000; color:#fff; padding:6px; font-size:12px; margin-top:10px; }
        .note { font-size:11px; }
    </style>
</head>
<body>
<div class="container">

    <div class="header" style="display: flex; flex-direction: column; align-items: center; padding-top:6px; position:relative;">
        @if(!empty($logos['logo']))
            <img class="logo" src="{{ $logos['logo'] }}" alt="logo" style="margin-bottom: 4px;">
        @endif

        <div style="text-align:center;">
            <div class="title" style="font-family: serif;">University of Science and Technology of Southern Philippines</div>
            <div class="subtitle" style="margin-top:4px;">
                Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva
            </div>
            <div class="subtitle" style="font-weight:700; margin-top:6px;">GUIDANCE AND COUNSELING SERVICES</div>
        </div>

        <div style="position:absolute; right:0; top:0; width:100px;">
            <div style="border:1px solid #000; padding:1px; font-size:5px; text-align:center; background:#fff;">
                <div style="background:#1b2a6b; color:#fff; font-weight:700; padding:1px;">Document Code No.</div>
                <div style="font-weight:700; font-size:6px; padding:2px 0;">{{ $documentCode->document_code_no ?? 'FM-USTP-GCS-02' }}</div>
                <div style="display:flex; font-size:6px; border-top:1px solid #000;">
                    <div style="flex:1; border-right:1px solid #000; padding:1px;">Rev. No.</div>
                    <div style="flex:1; padding:1px;">Effective Date</div>
                </div>
                <div style="display:flex; font-size:6px; border-top:1px solid #000;">
                    <div style="flex:1; border-right:1px solid #000; padding:1px;">{{ $documentCode->revision_no ?? '00' }}</div>
                    <div style="flex:1; padding:1px;">{{ $documentCode->effective_date ?? '03.17.25' }}</div>
                </div>
                <div style="border-top:1px solid #000; padding:2px; font-size:6px; font-weight:700;">Page No. 1 of {{ explode(' of ', $documentCode->page_no ?? '1 of 2')[1] ?? '2' }}</div>
            </div>
        </div>
    </div>

    <h2 style="text-align:center; margin-top:10px; font-weight:700;">STUDENT'S PERSONAL DATA SHEET</h2>

    <div style="position:relative;">
        <div style="position:absolute; left:50%; transform:translateX(-50%); text-align:center;">
            <div style="font-weight:700; font-size:13px;">{{ $student->getFullNameAttribute() }}</div>
            <div class="muted">Student ID: {{ $student->student_id ?? 'N/A' }}</div>
            <div class="muted">
                {{ $pds->course ?? $student->course_category ?? '' }}
                -
                {{ $pds->year_level ?? $student->year_level ?? '' }}
            </div>
        </div>

        <div style="position:absolute; right:0; top:0; width:120px;">
            <div class="photo-box">
                @if(!empty($photoData))
                    <img src="{{ $photoData }}" alt="photo">
                @else
                    <div style="font-size:11px; padding-top:28px; color:#666;">No Photo<br>Available</div>
                @endif
            </div>
        </div>
    </div>

    <!-- PERSONAL BACKGROUND -->
    <div class="section">
        <div class="section-title">PERSONAL BACKGROUND</div>
        <table>
            <tr>
                <td style="width:33%;"><div class="label">Course/Track:</div><div class="field">{{ $pds->course ?? $student->course_category ?? '' }}</div></td>
                <td style="width:33%;"><div class="label">Major/Strand:</div><div class="field">{{ $pds->major ?? '' }}</div></td>
                <td style="width:34%;"><div class="label">Grade/Year Level:</div><div class="field">{{ $pds->year_level ?? $student->year_level ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">First Name:</div><div class="field">{{ $pds->first_name ?: $student->first_name ?: '' }}</div></td>
                <td><div class="label">Gender:</div><div class="field">{{ $pds->sex ?: '' }}</div></td>
                <td><div class="label">Date of Birth:</div><div class="field">{{ $pds->birth_date ? $pds->birth_date->format('Y-m-d') : '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Contact Number:</div><div class="field">{{ $pds->contact_number ?? $student->phone_number ?? '' }}</div></td>
                <td colspan="2"><div class="label">Email Address:</div><div class="field">{{ $pds->email ?? $student->email ?? '' }}</div></td>
            </tr>
            <tr>
                <td colspan="3"><div class="label">Permanent Address:</div><div class="field">{{ $pds->permanent_address ?? '' }}</div></td>
            </tr>
            <tr>
                <td colspan="3"><div class="label">Present Address:</div><div class="field">{{ $pds->present_address ?? '' }}</div></td>
            </tr>
        </table>
    </div>

    <!-- FAMILY BACKGROUND -->
    <div class="section">
        <div class="section-title">FAMILY BACKGROUND</div>
        <table>
            <tr>
                <td style="width:50%;"><div class="label">Father's Name:</div><div class="field">{{ $pds->father_name ?? '' }}</div></td>
                <td style="width:50%;"><div class="label">Mother's Name:</div><div class="field">{{ $pds->mother_name ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Parents' Address:</div><div class="field">{{ $pds->parents_address ?? '' }}</div></td>
                <td><div class="label">Guardian:</div><div class="field">{{ $pds->guardian_name ?? '' }}</div></td>
            </tr>
        </table>
    </div>

    <!-- AWARDS AND RECOGNITION -->
    <div class="section">
        <div class="section-title">AWARDS AND RECOGNITION</div>

        <table>
            <tr>
                <td style="font-weight:700;">AWARDS/RECOGNITION RECEIVED</td>
                <td style="font-weight:700;">NAME OF SCHOOL/ORGANIZATION</td>
                <td style="font-weight:700;">YEAR</td>
            </tr>

            @for($i = 0; $i < 4; $i++)
            @php
                $award = $pds->awards[$i] ?? [];
            @endphp
            <tr>
                <td><div class="field">{{ $award['title'] ?? '' }}</div></td>
                <td><div class="field">{{ $award['organization'] ?? '' }}</div></td>
                <td><div class="field">{{ $award['year'] ?? '' }}</div></td>
            </tr>
            @endfor
        </table>

        <div class="note" style="margin-top:8px;">
            I hereby certify that all entries on the form are true and correct.
            I also agree to allow GCS to use the information/data for research purposes.
        </div>

        <div style="text-align:center; margin-top:14px;">
            <div style="display:inline-block; text-align:center; margin-right:40px;">
                <div class="field" style="text-align:center; font-weight:600; width:200px;">
                    {{ $student->getFullNameAttribute() }}
                </div>
                <div style="font-size:10px; margin-top:2px;">SIGNATURE OVER PRINTED NAME</div>
            </div>

            <div style="display:inline-block; text-align:center;">
                <div class="field" style="text-align:center; width:100px;">
                    {{ $pds->signed_at ?? \Carbon\Carbon::now()->format('m/d/Y') }}
                </div>
                <div style="font-size:10px; margin-top:2px;">DATE</div>
            </div>
        </div>
    </div>

    <div style="text-align:center; margin-top:18px; font-size:11px; color:#666;">
        Generated on {{ \Carbon\Carbon::now()->toDateTimeString() }}
    </div>

</div>
</body>
</html>
