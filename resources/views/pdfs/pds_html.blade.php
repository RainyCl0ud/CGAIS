<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PDS - Print View</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        color: #111;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .label {
        font-weight: 700;
        font-size: 12px;
        margin-bottom: 2px;
    }

    .field {
        border: 1px solid #777;
        min-height: 20px;
        padding: 3px 6px;
    }

    .section-title {
        background: #000;
        color: #fff;
        font-weight: 700;
        padding: 6px;
        margin-top: 16px;
        font-size: 13px;
    }

    .photo {
        width: 130px;
        height: 160px;
        border: 1px solid #000;
        overflow: hidden;
        text-align: center;
    }

    .photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .btn-bar {
        position: fixed;
        top: 10px;
        right: 10px;
        z-index: 999;
    }

    .btn {
        padding: 8px 12px;
        background: #0b5ed7;
        color: #fff;
        border-radius: 4px;
        text-decoration: none;
        margin-left: 5px;
    }
</style>
</head>

<body>

@php
    function field($value = null) {
        return trim($value ?? '') !== '' ? e($value) : '&nbsp;';
    }
@endphp

<!-- ACTION BUTTONS -->
<div class="btn-bar">
    <a href="#" class="btn" onclick="window.print();return false;">Print</a>
</div>

<!-- HEADER -->
<div style="position: relative; margin-bottom: 10px;">

   <!-- DOCUMENT CODE -->
<div style="position:absolute; top:0; right:0;">
    <div style="
        width:140px;
        border:2px solid #000;
        font-family: Arial, sans-serif;
        font-size:10px;
    ">
        <table style="width:100%; border-collapse:collapse; text-align:center;">
            
            <!-- HEADER -->
            <tr>
                <td style="
                    background:#1f2f6b;
                    color:#fff;
                    font-weight:700;
                    padding:4px 2px;
                    border-bottom:2px solid #000;
                ">
                    Document Code No.
                </td>
            </tr>

            <!-- CODE -->
            <tr>
                <td style="
                    font-weight:700;
                    padding:6px 2px;
                    border-bottom:2px solid #000;
                ">
                    {{ $documentCode->code ?? 'ZZZZZZZZZZZZ' }}
                </td>
            </tr>

            <!-- REV / EFFECTIVE LABEL -->
            <tr>
                <td style="
                    padding:4px 2px;
                    border-bottom:2px solid #000;
                    font-weight:600;
                ">
                    Rev. No. &nbsp;&nbsp;&nbsp; Effective Date
                </td>
            </tr>

            <!-- REV / EFFECTIVE VALUE -->
            <tr>
                <td style="
                    padding:4px 2px;
                    border-bottom:2px solid #000;
                ">
                    {{ $documentCode->revision ?? '00' }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ $documentCode->effective_date ?? '0000000' }}
                </td>
            </tr>

            <!-- PAGE -->
            <tr>
                <td style="
                    font-weight:700;
                    padding:4px 2px;
                ">
                    Page No. 1 of 2
                </td>
            </tr>

        </table>
    </div>
</div>

    <!-- UNIVERSITY HEADER -->
    <div style="text-align:center;">
        <div style="font-weight:700; font-size:15px; text-transform:uppercase;">
            UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES
        </div>
        <div style="font-size:11px; margin-top:3px;">
            ALUBIJID | BALUBAL | CAGAYAN DE ORO | CLAVERIA | JASAAN | OROQUIETA | PANAON | VILLANUEVA
        </div>
        <div style="font-weight:700; margin-top:6px;">
            GUIDANCE AND COUNSELING SERVICES
        </div>
    </div>
</div>

<h2 style="text-align:center; margin:14px 0;">
    STUDENT'S PERSONAL DATA SHEET
</h2>

<!-- NAME + PHOTO ROW -->
<table style="margin-bottom:10px;">
<tr>
    <td style="width:25%;"></td>

    <!-- PERFECTLY CENTERED NAME -->
    <td style="width:50%; text-align:center;">
        <div style="font-weight:700; font-size:16px;">
            {{ $student->getFullNameAttribute() }}
        </div>
        <div style="font-size:12px; color:#555;">
            Student ID: {{ $student->student_id ?? '' }}<br>
            {{ $student->course_category ?? '' }} - {{ $student->year_level ?? '' }}
        </div>
    </td>

    <!-- PHOTO -->
    <td style="width:25%; text-align:right;">
        <div class="photo">
            @if(!empty($photoData))
                <img src="{{ $photoData }}">
            @else
                <div style="padding-top:60px; color:#777;">No Photo</div>
            @endif
        </div>
    </td>
</tr>
</table>

<!-- PERSONAL BACKGROUND -->
<div class="section-title">PERSONAL BACKGROUND</div>
<table cellpadding="6">
<tr>
    <td>
        <div class="label">Course/Track</div>
        <div class="field">{!! field($pds->course ?? $student->course_category) !!}</div>
    </td>
    <td>
        <div class="label">Major/Strand</div>
        <div class="field">{!! field($pds->major) !!}</div>
    </td>
    <td>
        <div class="label">Grade/Year Level</div>
        <div class="field">{!! field($pds->year_level ?? $student->year_level) !!}</div>
    </td>
</tr>

<tr>
    <td>
        <div class="label">First Name</div>
        <div class="field">{!! field($student->first_name) !!}</div>
    </td>
    <td>
        <div class="label">Gender</div>
        <div class="field">{!! field($pds->sex) !!}</div>
    </td>
    <td>
        <div class="label">Date of Birth</div>
        <div class="field">
            {!! field(optional($pds->birth_date)->format('m/d/Y')) !!}
        </div>
    </td>
</tr>

<tr>
    <td>
        <div class="label">Contact Number</div>
        <div class="field">{!! field($pds->contact_number ?? $student->phone_number) !!}</div>
    </td>
    <td colspan="2">
        <div class="label">Email Address</div>
        <div class="field">{!! field($pds->email ?? $student->email) !!}</div>
    </td>
</tr>

<tr>
    <td colspan="3">
        <div class="label">Permanent Address</div>
        <div class="field">{!! field($pds->permanent_address) !!}</div>
    </td>
</tr>

<tr>
    <td colspan="3">
        <div class="label">Present Address</div>
        <div class="field">{!! field($pds->present_address) !!}</div>
    </td>
</tr>
</table>

<!-- FAMILY BACKGROUND -->
<div class="section-title">FAMILY BACKGROUND</div>
<table cellpadding="6">
<tr>
    <td>
        <div class="label">Father's Name</div>
        <div class="field">{!! field($pds->father_name) !!}</div>
    </td>
    <td>
        <div class="label">Mother's Name</div>
        <div class="field">{!! field($pds->mother_name) !!}</div>
    </td>
</tr>

<tr>
    <td>
        <div class="label">Father Occupation</div>
        <div class="field">{!! field($pds->father_occupation) !!}</div>
    </td>
    <td>
        <div class="label">Mother Occupation</div>
        <div class="field">{!! field($pds->mother_occupation) !!}</div>
    </td>
</tr>

<tr>
    <td colspan="2">
        <div class="label">Parents' Address</div>
        <div class="field">{!! field($pds->parents_address) !!}</div>
    </td>
</tr>
</table>

<!-- CERTIFICATION -->
<div style="margin-top:20px; font-size:12px;">
    I hereby certify that all entries in this form are true and correct.
</div>

<br>

<table>
<tr>
    <td style="width:50%; text-align:center;">
        <div class="field">{{ $student->getFullNameAttribute() }}</div>
        Signature over Printed Name
    </td>
    <td style="width:50%; text-align:center;">
        <div class="field">{{ now()->format('m/d/Y') }}</div>
        Date
    </td>
</tr>
</table>

</body>
</html>
