<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Personal Data Sheet</title>
    <style>
body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color:#111; font-size:12px; margin-top: 120px; margin-bottom: 50px; }
.header { position: fixed; top: 0; left: 0; right: 0; background: white; z-index: 1000; text-align:center; padding:1px; }
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
.footer { position: fixed; bottom: 0; left: 0; right: 0; background: white; z-index: 1000; padding: 6px; text-align: center; border-top: 1px solid #000; }
.page-number::after { content: "Page " counter(page) " of 4"; }
    </style>
</head>
<body>
<div class="container">

    <div class="header" style="display: flex; flex-direction: column; align-items: center; padding-top:1px;">
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
                    <!-- <div style="flex:1; border-right:1px solid #000; padding:1px;">{{ $documentCode->revision_no ?? '00' }}</div> -->
                    <div style="flex:1; padding:1px;">{{ $documentCode->effective_date ?? '03.17.25' }}</div>
                </div>
                <!-- <div style="border-top:1px solid #000; padding:2px; font-size:6px; font-weight:700;"></div> -->
            </div>
        </div>
    </div>

    <h2 style="text-align:center; margin-top:10px; font-weight:700;">STUDENT'S PERSONAL DATA SHEET</h2>

    <!-- CONFIDENTIALITY STATEMENT -->
    <div class="section" style="margin-top:15px; padding:10px; background:#f9f9f9;">
        <div style="font-size:10px; line-height:1.4;">
            <strong>The Guidance and Counseling Services (GCS)</strong> observes <strong>STRICT CONFIDENTIALITY</strong> on the personal information shared in this form according to the ethical principles of confidentiality and in compliance with the Data Privacy Act. However, please take note that the information will be disclosed under the following circumstances:
            <br><br>
            <strong>Threat on the life of the client (e.g. attempt to commit suicide, victim of abuse)</strong>
            <br>
            <strong>The client can cause danger to the lives and health of other people.</strong>
            <br><br>
            Moreover, information may also be given to agencies (e.g. DSWD, Police, Women and Children Protection Unit, Rehabilitation Unit Hospitals and other health providers) that can facilitate or address client's need and situation.
            <br><br>
            <strong>Instruction:</strong> Please provide honest response to the information needed. Rest assured that data gathered will be treated with utmost confidentiality in accordance with the Data Privacy Act.
        </div>
    </div>

    <div style="position:relative;">
        <div style="position:absolute; left:50%; transform:translateX(-50%); text-align:center; padding-top: 30;">
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
                <td><div class="label">Last Name:</div><div class="field">{{ $pds->last_name ?: $student->last_name ?: '' }}</div></td>
                <td><div class="label">Middle Name:</div><div class="field">{{ $pds->middle_name ?: $student->middle_name ?: '' }}</div></td>
            </tr>
            <tr>
               <td><div class="label">Gender:</div><div class="field">{{ $pds->sex ?: '' }}</div></td>
               <td><div class="label">Date of Birth:</div><div class="field">{{ $pds->birth_date ? $pds->birth_date->format('m/d/Y') : '' }}</div></td>
               <td><div class="label">Age:</div><div class="field">{{ $pds->age ?? '' }}</div></td>
              </tr>
            <tr>
                <td><div class="label">Place of Birth:</div><div class="field">{{ $pds->birth_place ?? '' }}</div></td>
                <td><div class="label">Civil Status:</div><div class="field">{{ $pds->civil_status ?: '' }}</div></td>
                <td><div class="label">Religion:</div><div class="field">{{ $pds->religion ?? '' }}</div></td>
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
            <tr>
                <td><div class="label">School Last Attended:</div><div class="field">{{ $pds->last_school ?? '' }}</div></td>
                <td><div class="label">Location of School:</div><div class="field">{{ $pds->school_location ?? '' }}</div></td>
                <td><div class="label">Previous Course/Grade:</div><div class="field">{{ $pds->previous_course ?? '' }}</div></td>
            </tr>
        </table>
    </div>

    <!-- FAMILY BACKGROUND -->
    <div class="section">
        <div class="section-title">FAMILY BACKGROUND</div>
        <table>
            <tr>
                <td style="width:50%;"><div class="label">Name of Father:</div><div class="field">{{ $pds->father_name ?? '' }}</div></td>
                <td style="width:50%;"><div class="label">Age:</div><div class="field">{{ $pds->father_age ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Contact No:</div><div class="field">{{ $pds->father_contact ?? '' }}</div></td>
                <td><div class="label">Occupation:</div><div class="field">{{ $pds->father_occupation ?? '' }}</div></td>
            </tr>
            <tr>
                <td colspan="2"><div class="label">Educational Attainment:</div><div class="field">{{ $pds->father_education ?? '' }}</div></td>
            </tr>
            <tr>
                <td style="width:50%;"><div class="label">Name of Mother:</div><div class="field">{{ $pds->mother_name ?? '' }}</div></td>
                <td style="width:50%;"><div class="label">Age:</div><div class="field">{{ $pds->mother_age ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Contact No:</div><div class="field">{{ $pds->mother_contact ?? '' }}</div></td>
                <td><div class="label">Occupation:</div><div class="field">{{ $pds->mother_occupation ?? '' }}</div></td>
            </tr>
            <tr>
                <td colspan="2"><div class="label">Educational Attainment:</div><div class="field">{{ $pds->mother_education ?? '' }}</div></td>
            </tr>
            <tr>
                <td colspan="3"><div class="label">Parents' Permanent Address:</div><div class="field">{{ $pds->parents_address ?? '' }}</div></td>
            </tr>
            <tr>
                <td style="width:50%;"><div class="label">Husband/Wife (If Married):</div><div class="field">{{ $pds->spouse_name ?? '' }}</div></td>
                <td style="width:50%;"><div class="label">Contact No:</div><div class="field">{{ $pds->spouse_contact ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Occupation:</div><div class="field">{{ $pds->spouse_occupation ?? '' }}</div></td>
                <td><div class="label">Educational Attainment:</div><div class="field">{{ $pds->spouse_education ?? '' }}</div></td>
            </tr>
            <tr>
                <td style="width:50%;"><div class="label">Name of Guardian (if applicable):</div><div class="field">{{ $pds->guardian_name ?? '' }}</div></td>
                <td style="width:50%;"><div class="label">Age:</div><div class="field">{{ $pds->guardian_age ?? '' }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Contact No:</div><div class="field">{{ $pds->guardian_contact ?? '' }}</div></td>
                <td><div class="label">Occupation:</div><div class="field">{{ $pds->guardian_occupation ?? '' }}</div></td>
            </tr>
        </table>
        <div style="margin-top:8px; font-size:11px;">Pls. continue on the back page</div>
    </div>

    <!-- OTHER INFORMATION -->
    <div class="section">
            <br>
        <div class="section-title">OTHER INFORMATION</div>

        <div style="margin-bottom:10px;">
            <div class="label">1. Why did you choose this course/program?</div>
            <div class="field" style="min-height:40px;">{{ $pds->reason_for_course ?? '' }}</div>
        </div>

        <div style="margin-bottom:10px;">
            <div class="label">2. How would you describe your family? Please put a check (/) mark on the space provided.</div>
            <table style="margin-top:5px;">
                <tr>
                    <td style="width:5%;"><div class="field" style="text-align:center;">{{ $pds->family_description == 'harmonious' ? '/' : '' }}</div></td>
                    <td>a family with harmonious relationship among family members</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ $pds->family_description == 'conflict' ? '/' : '' }}</div></td>
                    <td>a family having conflict with some family members</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ $pds->family_description == 'separated' ? '/' : '' }}</div></td>
                    <td>a family with separated parents</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ $pds->family_description == 'abroad' ? '/' : '' }}</div></td>
                    <td>a family with parents working abroad</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ $pds->family_description == 'other' ? '/' : '' }}</div></td>
                    <td>others, pls. specify:<br><div class="field" style="width:200px;">{{ $pds->family_description_other ?? '' }}</div></td>
                </tr>
            </table>
        </div>

        <div style="margin-bottom:10px;">
            <div class="label">3. Where do you live right now? Please put a check (/) mark on the space provided.</div>
            <table style="margin-top:5px;">
                <tr>
                    <td style="width:5%;"><div class="field" style="text-align:center;">{{ $pds->living_situation == 'home' ? '/' : '' }}</div></td>
                    <td>at home</td>
                    <td style="width:5%;"><div class="field" style="text-align:center;">{{ $pds->living_situation == 'boarding' ? '/' : '' }}</div></td>
                    <td>boarding house</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ $pds->living_situation == 'relatives' ? '/' : '' }}</div></td>
                    <td>relatives</td>
                    <td><div class="field" style="text-align:center;">{{ $pds->living_situation == 'friends' ? '/' : '' }}</div></td>
                    <td>friends</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ $pds->living_situation == 'other' ? '/' : '' }}</div></td>
                    <td colspan="3">others, pls. specify:<br><div class="field" style="width:200px;">{{ $pds->living_situation_other ?? '' }}</div></td>
                </tr>
            </table>
        </div>

        <div style="margin-bottom:10px;">
            <div class="label">4. Describe your living condition. Please put a check (/) mark on the space provided.</div>
            <table style="margin-top:5px;">
                <tr>
                    <td style="width:5%;"><div class="field" style="text-align:center;">{{ $pds->living_condition == 'good' ? '/' : '' }}</div></td>
                    <td>good environment for learning</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ $pds->living_condition == 'not_good' ? '/' : '' }}</div></td>
                    <td>not-so-good environment for learning</td>
                </tr>
            </table>
        </div>

        <div style="margin-bottom:10px;">
            <div class="label">5. Do you have any physical/health condition/s?</div>
            <table style="margin-top:5px;">
                <tr>
                    <td style="width:5%;"><div class="field" style="text-align:center;">{{ $pds->health_condition == 'no' ? '/' : '' }}</div></td>
                    <td>No</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ $pds->health_condition == 'yes' ? '/' : '' }}</div></td>
                    <td>Yes, pls. specify: <br> <div class="field" style="display:inline-block; width:300px;">{{ $pds->health_condition_specify ?? '' }}</div></td>
                </tr>
            </table>
        </div>

        <div style="margin-bottom:10px;">
            <div class="label">6. Have you undergone intervention/treatment with a psychologist/psychiatrist?</div>
            <table style="margin-top:5px;">
                <tr>
                    <td style="width:5%;"><div class="field" style="text-align:center;">{{ $pds->intervention_treatment == 0 ? '/' : '' }}</div></td>
                    <td>No</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ $pds->intervention_treatment == 1 ? '/' : '' }}</div></td>
                    <td>Yes</td>
                </tr>
            </table>
        </div>

        <div style="margin-bottom:10px;">
            <div class="label">CHECK THE SEMINARS/ACTIVITIES YOU WANT TO AVAIL FROM THE GUIDANCE SERVICES UNIT</div>
            <table style="margin-top:5px;">
                <tr>
                    <td style="width:5%;"><div class="field" style="text-align:center;">{{ in_array('adjustment', $pds->intervention_types ?? []) ? '/' : '' }}</div></td>
                    <td>Adjustment (dealing with people, handling pressures, environment, class schedules, etc.)</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ in_array('self_confidence', $pds->intervention_types ?? []) ? '/' : '' }}</div></td>
                    <td>Building Self-Confidence</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ in_array('communication', $pds->intervention_types ?? []) ? '/' : '' }}</div></td>
                    <td>Developing Communication Skills</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ in_array('study_habits', $pds->intervention_types ?? []) ? '/' : '' }}</div></td>
                    <td>Study Habits</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ in_array('time_management', $pds->intervention_types ?? []) ? '/' : '' }}</div></td>
                    <td>Time Management</td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ in_array('tutorial', $pds->intervention_types ?? []) ? '/' : '' }}</div></td>
                    <td>Tutorial with Peers (Please specify the subject/s): <br> <div class="field" style="display:inline-block; width:200px;">{{ $pds->tutorial_subjects ?? '' }}</div></td>
                </tr>
                <tr>
                    <td><div class="field" style="text-align:center;">{{ in_array('other', $pds->intervention_types ?? []) ? '/' : '' }}</div></td>
                    <td>others, pls. specify:<br><div class="field" style="width:200px;">{{ $pds->intervention_other ?? '' }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- AWARDS AND RECOGNITION -->
     <br>
     <br>
     <br>
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
                <td><div class="field">{{ $award['award'] ?? '' }}</div></td>
                <td><div class="field">{{ $award['school'] ?? '' }}</div></td>
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
                    {{ $pds->signature }}
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

<div class="footer">
    <p style="font-size:10px; color:#666;">Confidential - For authorized personnel only <span class="page-number"></span></p>
</div>

</body>
</html>
