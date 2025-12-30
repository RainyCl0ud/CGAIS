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
        .field { border-bottom:1px solid #666; padding:2px 0; }
        table { width:100%; border-collapse:collapse; margin-top:6px; }
        td, th { padding:6px; vertical-align:top; }
        .muted { color:#666; font-size:11px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header" style="position:relative; padding-top:6px;">
            <div style="float:left; width:90px;">
                @if(!empty($logos['logo']))
                    <img class="logo" src="{{ $logos['logo'] }}" alt="logo">
                @endif
            </div>

            <div style="text-align:center; margin-left:100px; margin-right:100px;">
                <div class="title" style="font-family: serif; font-weight:700; font-size:14px; text-transform:uppercase;">University of Science and Technology of Southern Philippines</div>
                <div class="subtitle" style="font-size:10px; text-transform:uppercase; margin-top:4px;">Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva</div>
                <div class="subtitle" style="font-weight:700; margin-top:6px;">GUIDANCE AND COUNSELING SERVICES</div>
            </div>

            <div style="position:absolute; right:0; top:0; width:100px;">
                <div style="border:1px solid #000; padding:1px; font-size:5px; text-align:center; background:#fff;">
                    <div style="background:#1b2a6b; color:#fff; font-weight:700; padding:1px;">Document Code No.</div>
                    <div style="font-weight:700; font-size:6px; padding:2px 0;">FM-USTP-GCS-02</div>
                    <div style="display:flex; font-size:6px; border-top:1px solid #000;">
                        <div style="flex:1; border-right:1px solid #000; padding:1px;">Rev. No.</div>
                        <div style="flex:1; padding:1px;">Effective Date</div>
                    </div>
                    <div style="display:flex; font-size:6px; border-top:1px solid #000;">
                        <div style="flex:1; border-right:1px solid #000; padding:1px;">00</div>
                        <div style="flex:1; padding:1px;">03.17.25</div>
                    </div>
                    <div style="border-top:1px solid #000; padding:2px; font-size:6px; font-weight:700;">Page No. 1 of 2</div>
                </div>
            </div>
            <div style="clear:both;"></div>

        </div>

        <h2 style="text-align:center; margin-top:10px; font-weight:700;">STUDENT'S PERSONAL DATA SHEET</h2>

        <div style="position:relative;">
            <div style="width:68%; float:left;">
                <div style="text-align:center;">
                    <div style="font-weight:700; font-size:13px;">{{ $student->getFullNameAttribute() }}</div>
                    <div class="muted">Student ID: {{ $student->student_id ?? 'N/A' }}</div>
                    <div class="muted">{{ $pds->course ?? $student->course_category ?? '' }} - {{ $pds->year_level ?? $student->year_level ?? '' }}</div>
                </div>
            </div>

            <div style="float:right; width:120px;">
                <div class="photo-box">
                    @if(!empty($photoData))
                        <img src="{{ $photoData }}" alt="photo">
                    @else
                        <div style="font-size:11px; padding-top:28px; color:#666;">No Photo<br>Available</div>
                    @endif
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>

        <div class="section">
            <div style="font-weight:700; background:#000; color:#fff; padding:6px; font-size:12px;">PERSONAL BACKGROUND</div>
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

        <div class="section">
            <div style="font-weight:700; background:#000; color:#fff; padding:6px; font-size:12px;">FAMILY BACKGROUND</div>
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

        <div style="margin-top:18px; font-size:11px; color:#666;">Generated on {{ \Carbon\Carbon::now()->toDateTimeString() }}</div>
    </div>
</body>
</html>
