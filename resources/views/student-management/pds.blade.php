<x-app-layout>
    <!-- Navigation Header - Outside Document Content -->
    <div class="fixed top-3 right-80 z-50">
        <button id="generatePdsBtn" data-generate-url="{{ route('students.pds.generate', $student) }}" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print PDS
        </button>
    </div>
    <script>
        document.getElementById('generatePdsBtn').addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('data-generate-url');
            const win = window.open('about:blank', '_blank');
            const token = '{{ csrf_token() }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(r => r.json()).then(j => {
                if (j && j.url) {
                    win.location = j.url;
                } else {
                    win.close();
                    alert('Failed to generate PDF.');
                }
            }).catch(err => {
                try { win.close(); } catch (e) {}
                alert('Failed to generate PDF.');
            });
        });
    </script>

    <div class="py-12 page-1">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-300 shadow-md">
                <div class="px-10 py-8 text-gray-900 text-[13px] leading-tight tracking-tight relative">

                   <!-- HEADER LAYOUT -->
<div class="relative flex flex-col items-center">
    <!-- USTP Logo and Titles -->
    <div class="text-center w-full">
        <img src="/storage/ustp.png" alt="USTP Logo" class="mx-auto mb-1 w-[85px] h-auto">
        <h1 class="font-serif font-bold text-[14px] uppercase tracking-tight">
            UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES
        </h1>
        <p class="text-[10px] uppercase tracking-tight">
            Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Villanueva
        </p>
        <p class="font-semibold text-[10.5px] uppercase mt-1 tracking-wide">
            GUIDANCE AND COUNSELING SERVICES
        </p>
    </div>

    <!-- Document Code Box -->
    <div class="document-code-box absolute top-0 right-0 bg-white text-center border border-black leading-tight">
        <div class="bg-[#1b2a6b] text-white font-semibold py-[2px] text-[9px]">
            Document Code No.
        </div>
        <div class="py-[2px] font-bold text-[7px]">FM-USTP-GCS-02</div>
        <div class="grid grid-cols-2 border-t border-black text-[7px]">
            <div class="border-r border-black py-[1px] font-semibold text-[7px]">Rev. No.</div>
            <div class="py-[1px] font-semibold text-[7px]">Effective Date</div>
        </div>
        <div class="grid grid-cols-2 border-t border-black text-[7px]">
            <div class="border-r border-black py-[1px] text-[7px]">00</div>
            <div class="py-[1px] text-[7px]">03.17.25</div>
        </div>
        <div class="border-t border-black py-[2px] text-[7px] font-semibold">
            Page No. 1 of 2
        </div>

</div>


                    <!-- Title -->
                    <div class="mt-[50px] flex items-center">
                        <h2 class="flex-1 text-center font-bold text-[20px] uppercase">
                            STUDENT'S PERSONAL DATA SHEET
                        </h2>

                        <!-- 2x2 ID Photo Box -->
 <div class="photo-box border border-black w-[120px] h-[120px] 
            ml-4 sm:ml-6 md:ml-10 lg:ml-12 
            flex flex-col justify-center items-center text-[11px] font-medium 
            leading-tight bg-white relative overflow-hidden">

                            @if($student->personalDataSheet && $student->personalDataSheet->photo)
                                <img src="{{ asset('storage/' . $student->personalDataSheet->photo) }}" alt="ID Photo" class="w-full h-full object-cover">
                            @else
                                <div class="text-center text-gray-500">
                                    <p>No Photo</p>
                                    <p>Available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Student Info Header -->
                    <div class="mt-4 mb-6">
                        <div class="text-center">
                            <p class="text-lg font-semibold">{{ $student->getFullNameAttribute() }}</p>
                            <p class="text-sm text-gray-600">Student ID: {{ $student->student_id ?? 'N/A' }}</p>
                            @if($student->personalDataSheet && $student->personalDataSheet->course)
                                <p class="text-sm text-gray-600">{{ $student->personalDataSheet->course }} - {{ $student->personalDataSheet->year_level ?? $student->year_level ?? '' }}</p>
                            @elseif($student->course_category)
                                <p class="text-sm text-gray-600">{{ $student->course_category }} - {{ $student->year_level ?? '' }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Instruction Text -->
                    <p class="text-justify mb-3 mt-8">
                        <strong>The Guidance and Counseling Services (GCS)</strong> observes
                        <strong>STRICT CONFIDENTIALITY</strong> on the personal information shared in
                        this form according to the ethical principles of confidentiality and in compliance
                        with the Data Privacy Act. However, please take note that the information will be disclosed
                        under the following circumstances:
                    </p>

                    <ol class="list-decimal list-inside mb-3 ml-4">
                        <li>Threat on the life of the client (e.g. attempt to commit suicide, victim of abuse)</li>
                        <li>The client can cause danger to the lives and health of other people.</li>
                    </ol>

                    <p class="text-justify mb-3">
                        Moreover, information may also be given to agencies (e.g. DSWD, Police, Women and Children Protection Unit,
                        Rehabilitation Unit Hospitals and other health providers) that can facilitate or address client's need and situation.
                    </p>

                    <p class="text-justify mb-5">
                        <strong>Instruction:</strong> Please provide honest response to the information needed. Rest assured that data gathered
                        will be treated with utmost confidentiality in accordance with the Data Privacy Act.
                    </p>

                    <!-- Personal Background -->
                    <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] tracking-wide">
                        PERSONAL BACKGROUND
                    </div>

                    @if($student->personalDataSheet)
                    <div class="space-y-3 mb-4">
                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">Course/Track:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->course ?: $student->course_category ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Major/Strand:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->major ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Grade/Year Level:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->year_level ?: $student->year_level ?: 'Not provided' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">First Name:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->first_name ?: $student->first_name ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Gender:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->sex ?: 'Not provided' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">Date of Birth:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->birth_date ? $student->personalDataSheet->birth_date->format('Y-m-d') : 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Age:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->age ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Last Name:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->last_name ?: $student->last_name ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Middle Name:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->middle_name ?: $student->middle_name ?: 'Not provided' }}</div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="font-semibold">Place of Birth:</label>
                            <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->birth_place ?: 'Not provided' }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">Civil Status:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->civil_status ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Religion:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->religion ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Contact Number:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->contact_number ?: $student->phone_number ?: 'Not provided' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">Email Address:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->email ?: $student->email ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Permanent Address:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->permanent_address ?: 'Not provided' }}</div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="font-semibold">Present Address:</label>
                            <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->present_address ?: 'Not provided' }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">School Last Attended:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->last_school ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Location of School:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->school_location ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Previous Course/Grade:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->previous_course ?: 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Background -->
                    <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] tracking-wide">
                        FAMILY BACKGROUND
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="grid grid-cols-2 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">Name of Father:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->father_name ?: 'Not provided' }}</div>
                                <label class="font-semibold">Age:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->father_age ?: 'Not provided' }}</div>
                                <label class="font-semibold">Contact No:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->father_contact ?: 'Not provided' }}</div>
                                <label class="font-semibold">Occupation:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->father_occupation ?: 'Not provided' }}</div>
                                <label class="font-semibold">Educational Attainment:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->father_education ?: 'Not provided' }}</div>
                            </div>

                            <div>
                                <label class="font-semibold">Name of Mother:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->mother_name ?: 'Not provided' }}</div>
                                <label class="font-semibold">Age:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->mother_age ?: 'Not provided' }}</div>
                                <label class="font-semibold">Contact No:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->mother_contact ?: 'Not provided' }}</div>
                                <label class="font-semibold">Occupation:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->mother_occupation ?: 'Not provided' }}</div>
                                <label class="font-semibold">Educational Attainment:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->mother_education ?: 'Not provided' }}</div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="font-semibold">Parents' Permanent Address:</label>
                            <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->parents_address ?: 'Not provided' }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">Husband/Wife (If Married):</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->spouse_name ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Contact No:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->spouse_contact ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Occupation:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->spouse_occupation ?: 'Not provided' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">Educational Attainment:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->spouse_education ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Name of Guardian (if applicable):</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->guardian_name ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Age:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->guardian_age ?: 'Not provided' }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 mb-2">
                            <div>
                                <label class="font-semibold">Contact No:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->guardian_contact ?: 'Not provided' }}</div>
                            </div>
                            <div>
                                <label class="font-semibold">Occupation:</label>
                                <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->guardian_occupation ?: 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>

                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-lg">No Personal Data Sheet available for this student.</p>
                        </div>
                    @endif

<p class="text-[11px] italic mt-4 text-right">Pls. continue on the back page</p>

<p class="text-center text-[11px] mt-3 leading-tight">
    C.M. Recto Avenue, Lapasan, Cagayan de Oro City 9000 Philippines <br>
    Tel Nos. +63 (88) 856 1738; Telefax +63 (88) 856 4696 | http://www.ustp.edu.ph
</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SECOND PAGE -->
    <div class="py-12 page-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-300 shadow-md">
                <div class="px-10 py-8 text-gray-900 text-[13px] leading-tight tracking-tight relative">
                    <div class="text-gray-900 text-[13px] leading-tight tracking-tight">

                        <!-- HEADER (Same as Page 1) -->
                        <div class="relative flex flex-col items-center">
                            <div class="text-center w-full">
                                <img src="/storage/ustp.png" alt="USTP Logo" class="mx-auto mb-1 w-[85px] h-auto">
                                <h1 class="font-serif font-bold text-[14px] uppercase tracking-tight">
                                    UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES
                                </h1>
                                <p class="text-[10px] uppercase tracking-tight">
                                    Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Villanueva
                                </p>
                                <p class="font-semibold text-[10.5px] uppercase mt-1 tracking-wide">
                                    GUIDANCE AND COUNSELING SERVICES
                                </p>
                            </div>

                            <!-- Document Code Box -->
                            <div class="document-code-box absolute top-0 right-0 bg-white text-center border border-black leading-tight">
                                <div class="bg-[#1b2a6b] text-white font-semibold py-[2px] text-[9px]">
                                    Document Code No.
                                </div>
                                <div class="py-[2px] font-bold text-[9px]">FM-USTP-GCS-02</div>
                                <div class="grid grid-cols-2 border-t border-black text-[9px]">
                                    <div class="border-r border-black py-[1px] font-semibold">Rev. No.</div>
                                    <div class="py-[1px] font-semibold">Effective Date</div>
                                </div>
                                <div class="grid grid-cols-2 border-t border-black text-[9px]">
                                    <div class="border-r border-black py-[1px]">00</div>
                                    <div class="py-[1px]">03.17.25</div>
                                </div>
                                <div class="border-t border-black py-[2px] text-[9px] font-semibold">
                                    Page No. 2 of 2
                                </div>
                            </div>
                        </div>

                        <!-- Subheader Line -->
                        <p class="text-center text-[11px] mt-2 leading-tight">
                            C.M. Recto Avenue, Lapasan, Cagayan de Oro City 9000 Philippines <br>
                            Tel Nos. +63 (88) 856 1738; Telefax +63 (88) 856 4696 | http://www.ustp.edu.ph
                        </p>

                        @if($student->personalDataSheet)
                        <!-- SECTION HEADER -->
                        <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] text-center tracking-wide mt-6">
                            OTHER INFORMATION
                        </div>

                        <!-- QUESTIONNAIRE -->
                        <div class="space-y-4">
                            <div>
                                <p><strong>1.</strong> Why did you choose this course/program?</p>
                                <div class="border-b border-gray-400 pb-1 mt-1 min-h-[60px]">{{ $student->personalDataSheet->reason_for_course ?: 'Not provided' }}</div>
                            </div>

                            <div>
                                <p><strong>2.</strong> How would you describe your family? Please put a check (/) mark on the space provided.</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    @php
                                        $familyDesc = $student->personalDataSheet->family_description;
                                    @endphp
                                    <div class="{{ $familyDesc == 'harmonious' ? 'font-bold' : '' }}">
                                        {{ $familyDesc == 'harmonious' ? '✓' : '☐' }} a family with harmonious relationship among family members
                                    </div>
                                    <div class="{{ $familyDesc == 'conflict' ? 'font-bold' : '' }}">
                                        {{ $familyDesc == 'conflict' ? '✓' : '☐' }} a family having conflict with some family members
                                    </div>
                                    <div class="{{ $familyDesc == 'separated' ? 'font-bold' : '' }}">
                                        {{ $familyDesc == 'separated' ? '✓' : '☐' }} a family with separated parents
                                    </div>
                                    <div class="{{ $familyDesc == 'abroad' ? 'font-bold' : '' }}">
                                        {{ $familyDesc == 'abroad' ? '✓' : '☐' }} a family with parents working abroad
                                    </div>
                                    <div class="{{ $familyDesc == 'other' ? 'font-bold' : '' }}">
                                        {{ $familyDesc == 'other' ? '✓' : '☐' }} others, pls. specify
                                    </div>
                                    @if($familyDesc == 'other')
                                        <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->family_description_other ?: 'Not specified' }}</div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <p><strong>3.</strong> Where do you live right now? Please put a check (/) mark on the space provided.</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    @php
                                        $livingSituation = $student->personalDataSheet->living_situation;
                                    @endphp
                                    <div class="{{ $livingSituation == 'home' ? 'font-bold' : '' }}">
                                        {{ $livingSituation == 'home' ? '✓' : '☐' }} at home
                                    </div>
                                    <div class="{{ $livingSituation == 'boarding' ? 'font-bold' : '' }}">
                                        {{ $livingSituation == 'boarding' ? '✓' : '☐' }} boarding house
                                    </div>
                                    <div class="{{ $livingSituation == 'relatives' ? 'font-bold' : '' }}">
                                        {{ $livingSituation == 'relatives' ? '✓' : '☐' }} relatives
                                    </div>
                                    <div class="{{ $livingSituation == 'friends' ? 'font-bold' : '' }}">
                                        {{ $livingSituation == 'friends' ? '✓' : '☐' }} friends
                                    </div>
                                    <div class="{{ $livingSituation == 'other' ? 'font-bold' : '' }}">
                                        {{ $livingSituation == 'other' ? '✓' : '☐' }} others, pls. specify
                                    </div>
                                    @if($livingSituation == 'other')
                                        <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->living_situation_other ?: 'Not specified' }}</div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <p><strong>4.</strong> Describe your living condition. Please put a check (/) mark on the space provided.</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    @php
                                        $livingCondition = $student->personalDataSheet->living_condition;
                                    @endphp
                                    <div class="{{ $livingCondition == 'good' ? 'font-bold' : '' }}">
                                        {{ $livingCondition == 'good' ? '✓' : '☐' }} good environment for learning
                                    </div>
                                    <div class="{{ $livingCondition == 'not_good' ? 'font-bold' : '' }}">
                                        {{ $livingCondition == 'not_good' ? '✓' : '☐' }} not-so-good environment for learning
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p><strong>5.</strong> Do you have any physical/health condition/s?</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    @php
                                        $healthCondition = $student->personalDataSheet->health_condition;
                                    @endphp
                                    <div class="{{ $healthCondition == 'no' ? 'font-bold' : '' }}">
                                        {{ $healthCondition == 'no' ? '✓' : '☐' }} No
                                    </div>
                                    <div class="{{ $healthCondition == 'yes' ? 'font-bold' : '' }}">
                                        {{ $healthCondition == 'yes' ? '✓' : '☐' }} Yes, pls. specify
                                    </div>
                                    @if($healthCondition == 'yes')
                                        <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->health_condition_specify ?: 'Not specified' }}</div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <p><strong>6.</strong> Have you undergone intervention/treatment with a psychologist/psychiatrist?</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    @php
                                        $intervention = $student->personalDataSheet->intervention;
                                    @endphp
                                    <div class="{{ $intervention == 'no' ? 'font-bold' : '' }}">
                                        {{ $intervention == 'no' ? '✓' : '☐' }} No
                                    </div>
                                    <div class="{{ $intervention == 'yes' ? 'font-bold' : '' }}">
                                        {{ $intervention == 'yes' ? '✓' : '☐' }} Yes
                                    </div>
                                    </div>
                                    <div class="mt-2">
                                         <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] text-center tracking-wide mt-6">
                            CHECK THE SEMINARS/ACTIVITIES YOU WANT TO AVAIL FROM THE GUIDANCE SERVICES UNIT
                        </div>

                                    @php
                                        $interventionTypes = $student->personalDataSheet->intervention_types ?? [];
                                    @endphp
                                    <div class="{{ in_array('adjustment', $interventionTypes) ? 'font-bold' : '' }}">
                                        {{ in_array('adjustment', $interventionTypes) ? '✓' : '☐' }} Adjustment (dealing with people, handling pressures, environment, class schedules, etc.)
                                    </div>
                                    <div class="{{ in_array('self_confidence', $interventionTypes) ? 'font-bold' : '' }}">
                                        {{ in_array('self_confidence', $interventionTypes) ? '✓' : '☐' }} Building Self-Confidence
                                    </div>
                                    <div class="{{ in_array('communication', $interventionTypes) ? 'font-bold' : '' }}">
                                        {{ in_array('communication', $interventionTypes) ? '✓' : '☐' }} Developing Communication Skills
                                    </div>
                                    <div class="{{ in_array('study_habits', $interventionTypes) ? 'font-bold' : '' }}">
                                        {{ in_array('study_habits', $interventionTypes) ? '✓' : '☐' }} Study Habits
                                    </div>
                                    <div class="{{ in_array('time_management', $interventionTypes) ? 'font-bold' : '' }}">
                                        {{ in_array('time_management', $interventionTypes) ? '✓' : '☐' }} Time Management
                                    </div>
                                    <div class="{{ in_array('tutorial_peers', $interventionTypes) ? 'font-bold' : '' }}">
                                        {{ in_array('tutorial_peers', $interventionTypes) ? '✓' : '☐' }} Tutorial with Peers (Please specify the subject/s)
                                    </div>
                                    @if(in_array('tutorial_peers', $interventionTypes))
                                        <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->tutorial_subjects ?: 'Not specified' }}</div>
                                    @endif
                                    <div class="{{ in_array('other', $interventionTypes) ? 'font-bold' : '' }}">
                                        {{ in_array('other', $interventionTypes) ? '✓' : '☐' }} others, pls. specify
                                    </div>
                                    @if(in_array('other', $interventionTypes))
                                        <div class="border-b border-gray-400 pb-1">{{ $student->personalDataSheet->intervention_other ?: 'Not specified' }}</div>
                                    @endif
                                </div>
                            </div>

                                 <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] text-center tracking-wide mt-6">
                            AWARDS AND RECOGNITION
                        </div>

                            <!-- AWARDS SECTION -->
                            <div>
                                <p class="font-semibold mt-4">AWARDS/RECOGNITION RECEIVED &nbsp;&nbsp;&nbsp;&nbsp; NAME OF SCHOOL/ORGANIZATION &nbsp;&nbsp;&nbsp;&nbsp; YEAR</p>
                                <div class="border-b border-gray-400 w-full mt-1"></div>

                                @php
                                    $awards = $student->personalDataSheet->awards ?? [];
                                @endphp
                                @for($i = 0; $i < 4; $i++)
                                <div class="grid grid-cols-3 gap-2 mt-2">
                                    <div class="border-b border-gray-400 pb-1">{{ $awards[$i]['award'] ?? '' }}</div>
                                    <div class="border-b border-gray-400 pb-1">{{ $awards[$i]['school'] ?? '' }}</div>
                                    <div class="border-b border-gray-400 pb-1">{{ $awards[$i]['year'] ?? '' }}</div>
                                </div>
                                @endfor
                            </div>

                            <!-- CERTIFICATION -->
                            <p class="mt-6 text-justify">
                                I hereby certify that all entries on the form are true and correct. I also agree to allow GCS to use the information/data for research purposes.
                            </p>

                            <div class="mt-6 flex justify-between px-10">
                                <div></div>
                                <div class="text-center">
                                    <div class="border-b border-gray-800 w-[250px] text-center pb-1">
                                        {{ $student->personalDataSheet->signature ?: ($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name) }}
                                    </div>
                                    <p class="text-[12px] mt-1">SIGNATURE OVER PRINTED NAME</p>
                                </div>
                                <div class="text-center">
                                    <div class="border-b border-gray-800 w-[150px] text-center pb-1">
                                        {{ $student->personalDataSheet->signature_date ? $student->personalDataSheet->signature_date->format('Y-m-d') : '' }}
                                    </div>
                                    <p class="text-[12px] mt-1">DATE</p>
                                </div>
                            </div>
                        </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p class="text-lg">No Personal Data Sheet available for this student.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>



    <style>
        @media print {
            /* Hide navigation elements during printing */
            .fixed {
                display: none !important;
            }
            
            /* Hide all buttons during printing */
            button {
                display: none !important;
            }
            
            .border-b {
                border-bottom: 1px solid black !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .page-1 {
                page-break-after: always;
            }
            .py-12 {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }
            .max-w-5xl {
                max-width: none !important;
                width: 100% !important;
            }
            .px-10 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            .py-8 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
            .text-[13px] {
                font-size: 13px !important;
            }
            .text-[11px] {
                font-size: 11px !important;
            }
            .text-[12px] {
                font-size: 12px !important;
            }
            .text-[20px] {
                font-size: 20px !important;
            }
            .text-[14px] {
                font-size: 14px !important;
            }
            .text-[10px] {
                font-size: 10px !important;
            }
            .text-[10.5px] {
                font-size: 10.5px !important;
            }
            .text-[9px] {
                font-size: 9px !important;
            }
            .text-[7px] {
                font-size: 7px !important;
            }
        }
        
        .document-code-box {
            width: 150px;
            transform: scale(0.85);
            transform-origin: top right;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.15);
        }

        /* Make smaller on very small screens */
        @media (max-width: 640px) {
            .document-code-box {
                transform: scale(0.75);
                width: 180px;
                top: 0.25rem;
                right: 0.25rem;
            }
        }

        /* Adjust slightly for medium screens */
        @media (min-width: 768px) and (max-width: 1024px) {
            .document-code-box {
                transform: scale(0.9);
                width: 220px;
            }
        }

        /* Ensure the nav bar button doesn't interfere with content on mobile */
        @media (max-width: 768px) {
            .fixed.top-6.right-48 {
                top: 1.5rem !important;
                right: 12rem !important;
            }
        }
    </style>
</x-app-layout>
