<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDS - Print View</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; color:#111; font-size:13px; }
        .header { display:flex; align-items:flex-start; border-bottom:1px solid #d3d3d3; padding-bottom:10px; margin-bottom:8px; }
        .logo { width:85px; margin-right:12px; }
        .title { text-align:center; flex:1; }
        .photo { width:120px; height:120px; border:1px solid #000; text-align:center; overflow:hidden; }
        .photo img { width:100%; height:100%; object-fit:cover; }
        .section { margin-top:10px; }
        .label { font-weight:700; }
        .field { border-bottom:1px solid #666; padding:4px 0; }
        .btn-bar { position:fixed; top:8px; right:8px; z-index:999; }
        .btn { padding:8px 10px; background:#0b5ed7; color:#fff; border-radius:4px; text-decoration:none; margin-left:6px; }
    </style>
</head>
<body>
    <div class="btn-bar">
        <a href="#" class="btn" onclick="window.print(); return false;">Print</a>
        <a href="#" id="generatePdfBtn" class="btn">Generate PDF</a>
    </div>

    <div class="section">
        <div style="font-weight:700; background:#000; color:#fff; padding:6px;">FAMILY BACKGROUND</div>
        <table style="width:100%; border-collapse:collapse; margin-top:6px;">
            <tr>
                <td style="width:50%;"><div class="label">Father's Name</div><div class="field">{{ $pds->father_name ?? '' }}</div></td>
                <td style="width:50%;"><div class="label">Mother's Name</div><div class="field">{{ $pds->mother_name ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Parents' Address</div><div class="field">{{ $pds->parents_address ?? '' }}</div></td>
                <td><div class="label">Guardian</div><div class="field">{{ $pds->guardian_name ?? '' }}</div></td>
            </tr>
        </table>
    </div>


    <div class="header" style="position:relative; padding-top:6px;">
        <div style="float:left; width:90px;">
            @if(!empty($logos['logo']))
                <img class="logo" src="{{ $logos['logo'] }}" alt="logo">
            @endif
        </div>

        <div style="text-align:center; margin-left:100px; margin-right:100px;">
            <div style="font-family: serif; font-weight:700; font-size:15px; text-transform:uppercase;">UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES</div>
            <div style="font-size:11px; text-transform:uppercase; margin-top:4px;">Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva</div>
            <div style="font-weight:700; margin-top:6px;">GUIDANCE AND COUNSELING SERVICES</div>
        </div>

        <div style="float:right; width:120px;">
            <div class="photo">
                @if(!empty($photoData))
                    <img src="{{ $photoData }}" alt="photo">
                @else
                    <div style="padding-top:36px; color:#666;">No Photo<br>Available</div>
                @endif
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>

    <h2 style="text-align:center;">STUDENT'S PERSONAL DATA SHEET</h2>

    <div class="section">
        <div style="text-align:center; font-weight:700; font-size:14px;">{{ $student->getFullNameAttribute() }}</div>
        <div style="text-align:center; color:#666;">Student ID: {{ $student->student_id ?? '' }}</div>
    </div>

    <div class="section">
        <div style="font-weight:700; background:#000; color:#fff; padding:6px;">PERSONAL BACKGROUND</div>
        <table style="width:100%; border-collapse:collapse; margin-top:6px;">
            <tr>
                <td style="width:33%;"><div class="label">Course/Track</div><div class="field">{{ $pds->course ?? $student->course_category ?? '' }}</div></td>
                <td style="width:33%;"><div class="label">Major/Strand</div><div class="field">{{ $pds->major ?? '' }}</div></td>
                <td style="width:34%;"><div class="label">Grade/Year Level</div><div class="field">{{ $pds->year_level ?? $student->year_level ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">First Name</div><div class="field">{{ $pds->first_name ?? $student->first_name ?? '' }}</div></td>
                <td><div class="label">Middle Name</div><div class="field">{{ $pds->middle_name ?? $student->middle_name ?? '' }}</div></td>
                <td><div class="label">Last Name</div><div class="field">{{ $pds->last_name ?? $student->last_name ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Gender</div><div class="field">{{ $pds->sex ?? '' }}</div></td>
                <td><div class="label">Date of Birth</div><div class="field">{{ $pds->birth_date ? $pds->birth_date->format('Y-m-d') : '' }}</div></td>
                <td><div class="label">Age</div><div class="field">{{ $pds->age ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Place of Birth</div><div class="field">{{ $pds->birth_place ?? '' }}</div></td>
                <td><div class="label">Civil Status</div><div class="field">{{ $pds->civil_status ?? '' }}</div></td>
                <td><div class="label">Religion</div><div class="field">{{ $pds->religion ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Contact Number</div><div class="field">{{ $pds->contact_number ?? $student->phone_number ?? '' }}</div></td>
                <td colspan="2"><div class="label">Email Address</div><div class="field">{{ $pds->email ?? $student->email ?? '' }}</div></td>
            </tr>
            <tr>
                <td colspan="3"><div class="label">Permanent Address</div><div class="field">{{ $pds->permanent_address ?? '' }}</div></td>
            </tr>
            <tr>
                <td colspan="3"><div class="label">Present Address</div><div class="field">{{ $pds->present_address ?? '' }}</div></td>
            </tr>
        </table>
    </div>

    <div style="margin-top:12px; font-size:11px; color:#666;">Generated on {{ \Carbon\Carbon::now()->toDateTimeString() }}</div>

    <script>
        document.getElementById('generatePdfBtn').addEventListener('click', function (e) {
            e.preventDefault();
            const btn = this;
            btn.textContent = 'Generating...';
            fetch("{{ route('students.pds.generate', $student) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
            }).then(r => r.json()).then(j => {
                if (j.url) {
                    window.open(j.url, '_blank');
                } else {
                    alert('Failed to generate PDF');
                }
            }).catch(() => alert('Failed to generate PDF')).finally(() => btn.textContent = 'Generate PDF');
        });
    </script>
</body>
</html>
