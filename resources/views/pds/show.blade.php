<x-app-layout>
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
        <div class="py-[2px] font-bold text-[7px]">{{ $documentCode->document_code_no ?? 'FM-USTP-GCS-02' }}</div>
        <div class="grid grid-cols-2 border-t border-black text-[7px]">
            <div class="border-r border-black py-[1px] font-semibold text-[7px]">Rev. No.</div>
            <div class="py-[1px] font-semibold text-[7px]">Effective Date</div>
        </div>
        <div class="grid grid-cols-2 border-t border-black text-[7px]">
            <div class="border-r border-black py-[1px] text-[7px]">{{ $documentCode->revision_no ?? '00' }}</div>
            <div class="py-[1px] text-[7px]">{{ $documentCode->effective_date ?? '03.17.25' }}</div>
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
                            leading-tight bg-white cursor-pointer relative overflow-hidden"
                            onclick="document.getElementById('photoInput').click()">

                            @if($pds->photo)
                                <img src="{{ asset('storage/' . $pds->photo) }}" alt="ID Photo" class="w-full h-full object-cover">
                            @else
                                <div class="text-center">
                                    <p>Click to</p>
                                    <p>upload</p>
                                    <p>photo</p>
                                </div>
                            @endif
                            <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden" form="pdsForm" onchange="previewImage(event)">
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

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4">
                        <p class="text-sm text-blue-800">
                            <strong>Note:</strong> Fields marked with "(Auto-filled from profile)" are read-only and can only be edited in your 
                            <a href="{{ route('profile.edit') }}" class="underline font-medium">Profile Settings</a>. 
                            This ensures consistency across your account information.
                        </p>
                    </div>

                    <!-- Personal Background -->
                    <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] tracking-wide">
                        PERSONAL BACKGROUND
                    </div>

                    <form method="POST" action="{{ route('pds.update') }}" id="pdsForm" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label>Course/Track:</label>
                                <input type="text" name="course" value="{{ old('course', $pds->course) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Major/Strand:</label>
                                <input type="text" name="major" value="{{ old('major', $pds->major) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Grade/Year Level:</label>
                                <input type="text" name="year_level" value="{{ old('year_level', $pds->year_level) }}" class="border-b border-gray-700 w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 mb-2">
                            <div>
                                <label>First Name: @if(!empty($pds->first_name) || !empty(Auth::user()->first_name))<span class="text-xs text-gray-500">(Auto-filled from profile)</span>@endif</label>
                                @php
                                    $firstNameValue = $pds->first_name ?: Auth::user()->first_name;
                                    $isFirstNameReadonly = !empty($pds->first_name) || !empty(Auth::user()->first_name);
                                @endphp
                                @if($isFirstNameReadonly)
                                    <div class="border-b border-gray-400 bg-gray-100 w-full text-gray-600 py-1 px-1 cursor-not-allowed select-none">
                                        {{ $firstNameValue }}
                                    </div>
                                    <input type="hidden" name="first_name" value="{{ $firstNameValue }}">
                                @else
                                    <input type="text" name="first_name" value="{{ old('first_name', $firstNameValue) }}" class="border-b border-gray-700 w-full">
                                @endif
                            </div>
                            <div>
                                <label>Gender:</label>
                                <input type="text" name="sex" value="{{ old('sex', $pds->sex) }}" class="border-b border-gray-700 w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-x-4 mb-2">
                            <div>
                                <label>Date of Birth:</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date', optional($pds->birth_date)->format('Y-m-d')) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Age:</label>
                                <input type="text" name="age" value="{{ old('age', $pds->age) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Last Name: @if(!empty($pds->last_name) || !empty(Auth::user()->last_name))<span class="text-xs text-gray-500">(Auto-filled from profile)</span>@endif</label>
                                @php
                                    $lastNameValue = $pds->last_name ?: Auth::user()->last_name;
                                    $isLastNameReadonly = !empty($pds->last_name) || !empty(Auth::user()->last_name);
                                @endphp
                                @if($isLastNameReadonly)
                                    <div class="border-b border-gray-400 bg-gray-100 w-full text-gray-600 py-1 px-1 cursor-not-allowed select-none">
                                        {{ $lastNameValue }}
                                    </div>
                                    <input type="hidden" name="last_name" value="{{ $lastNameValue }}">
                                @else
                                    <input type="text" name="last_name" value="{{ old('last_name', $lastNameValue) }}" class="border-b border-gray-700 w-full">
                                @endif
                            </div>
                            <div>
                                <label>Middle Name: @if(!empty($pds->middle_name) || !empty(Auth::user()->middle_name))<span class="text-xs text-gray-500">(Auto-filled from profile)</span>@endif</label>
                                @php
                                    $middleNameValue = $pds->middle_name ?: Auth::user()->middle_name;
                                    $isMiddleNameReadonly = !empty($pds->middle_name) || !empty(Auth::user()->middle_name);
                                @endphp
                                @if($isMiddleNameReadonly)
                                    <div class="border-b border-gray-400 bg-gray-100 w-full text-gray-600 py-1 px-1 cursor-not-allowed select-none">
                                        {{ $middleNameValue }}
                                    </div>
                                    <input type="hidden" name="middle_name" value="{{ $middleNameValue }}">
                                @else
                                    <input type="text" name="middle_name" value="{{ old('middle_name', $middleNameValue) }}" class="border-b border-gray-700 w-full">
                                @endif
                            </div>
                        </div>

                        <div class="mb-2">
                            <label>Place of Birth:</label>
                            <input type="text" name="birth_place" value="{{ old('birth_place', $pds->birth_place) }}" class="border-b border-gray-700 w-full">
                        </div>

                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label>Civil Status:</label>
                                <input type="text" name="civil_status" value="{{ old('civil_status', $pds->civil_status) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Religion:</label>
                                <input type="text" name="religion" value="{{ old('religion', $pds->religion) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Contact Number: @if(!empty($pds->contact_number) || !empty(Auth::user()->phone_number))<span class="text-xs text-gray-500">(Auto-filled from profile)</span>@endif</label>
                                @php
                                    $contactNumberValue = $pds->contact_number ?: (Auth::user()->phone_number ?? '');
                                    $isContactNumberReadonly = !empty($pds->contact_number) || !empty(Auth::user()->phone_number);
                                @endphp
                                @if($isContactNumberReadonly)
                                    <div class="border-b border-gray-400 bg-gray-100 w-full text-gray-600 py-1 px-1 cursor-not-allowed select-none">
                                        {{ $contactNumberValue }}
                                    </div>
                                    <input type="hidden" name="contact_number" value="{{ $contactNumberValue }}">
                                @else
                                    <input type="text" name="contact_number" value="{{ old('contact_number', $contactNumberValue) }}" class="border-b border-gray-700 w-full">
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 mb-2">
                            <div>
                                <label>Email Address: @if(!empty($pds->email) || !empty(Auth::user()->email))<span class="text-xs text-gray-500">(Auto-filled from profile)</span>@endif</label>
                                @php
                                    $emailValue = $pds->email ?: (Auth::user()->email ?? '');
                                    $isEmailReadonly = !empty($pds->email) || !empty(Auth::user()->email);
                                @endphp
                                @if($isEmailReadonly)
                                    <div class="border-b border-gray-400 bg-gray-100 w-full text-gray-600 py-1 px-1 cursor-not-allowed select-none">
                                        {{ $emailValue }}
                                    </div>
                                    <input type="hidden" name="email" value="{{ $emailValue }}">
                                @else
                                    <input type="email" name="email" value="{{ old('email', $emailValue) }}" class="border-b border-gray-700 w-full">
                                @endif
                            </div>
                            <div>
                                <label>Permanent Address:</label>
                                <input type="text" name="permanent_address" value="{{ old('permanent_address', $pds->permanent_address) }}" class="border-b border-gray-700 w-full">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label>Present Address:</label>
                            <input type="text" name="present_address" value="{{ old('present_address', $pds->present_address) }}" class="border-b border-gray-700 w-full">
                        </div>

                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label>School Last Attended:</label>
                                <input type="text" name="last_school" value="{{ old('last_school', $pds->last_school) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Location of School:</label>
                                <input type="text" name="school_location" value="{{ old('school_location', $pds->school_location) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Previous Course/Grade:</label>
                                <input type="text" name="previous_course" value="{{ old('previous_course', $pds->previous_course) }}" class="border-b border-gray-700 w-full">
                            </div>
                        </div>

                        <!-- Family Background -->
                        <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] tracking-wide">
                            FAMILY BACKGROUND
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 mb-2">
                            <div>
                                <label>Name of Father:</label>
                                <input type="text" name="father_name" value="{{ old('father_name', $pds->father_name) }}" class="border-b border-gray-700 w-full">
                                <label>Age:</label>
                                <input type="text" name="father_age" value="{{ old('father_age', $pds->father_age) }}" class="border-b border-gray-700 w-full">
                                <label>Contact No:</label>
                                <input type="text" name="father_contact" value="{{ old('father_contact', $pds->father_contact) }}" class="border-b border-gray-700 w-full">
                                <label>Occupation:</label>
                                <input type="text" name="father_occupation" value="{{ old('father_occupation', $pds->father_occupation) }}" class="border-b border-gray-700 w-full">
                                <label>Educational Attainment:</label>
                                <input type="text" name="father_education" value="{{ old('father_education', $pds->father_education) }}" class="border-b border-gray-700 w-full">
                            </div>

                            <div>
                                <label>Name of Mother:</label>
                                <input type="text" name="mother_name" value="{{ old('mother_name', $pds->mother_name) }}" class="border-b border-gray-700 w-full">
                                <label>Age:</label>
                                <input type="text" name="mother_age" value="{{ old('mother_age', $pds->mother_age) }}" class="border-b border-gray-700 w-full">
                                <label>Contact No:</label>
                                <input type="text" name="mother_contact" value="{{ old('mother_contact', $pds->mother_contact) }}" class="border-b border-gray-700 w-full">
                                <label>Occupation:</label>
                                <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $pds->mother_occupation) }}" class="border-b border-gray-700 w-full">
                                <label>Educational Attainment:</label>
                                <input type="text" name="mother_education" value="{{ old('mother_education', $pds->mother_education) }}" class="border-b border-gray-700 w-full">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label>Parents' Permanent Address:</label>
                            <input type="text" name="parents_address" value="{{ old('parents_address', $pds->parents_address) }}" class="border-b border-gray-700 w-full">
                        </div>

                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label>Husband/Wife (If Married):</label>
                                <input type="text" name="spouse_name" value="{{ old('spouse_name', $pds->spouse_name) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Contact No:</label>
                                <input type="text" name="spouse_contact" value="{{ old('spouse_contact', $pds->spouse_contact) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Occupation:</label>
                                <input type="text" name="spouse_occupation" value="{{ old('spouse_occupation', $pds->spouse_occupation) }}" class="border-b border-gray-700 w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-x-4 mb-2">
                            <div>
                                <label>Educational Attainment:</label>
                                <input type="text" name="spouse_education" value="{{ old('spouse_education', $pds->spouse_education) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Name of Guardian (if applicable):</label>
                                <input type="text" name="guardian_name" value="{{ old('guardian_name', $pds->guardian_name) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Age:</label>
                                <input type="text" name="guardian_age" value="{{ old('guardian_age', $pds->guardian_age) }}" class="border-b border-gray-700 w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-x-4 mb-2">
                            <div>
                                <label>Contact No:</label>
                                <input type="text" name="guardian_contact" value="{{ old('guardian_contact', $pds->guardian_contact) }}" class="border-b border-gray-700 w-full">
                            </div>
                            <div>
                                <label>Occupation:</label>
                                <input type="text" name="guardian_occupation" value="{{ old('guardian_occupation', $pds->guardian_occupation) }}" class="border-b border-gray-700 w-full">
                            </div>
                        </div>

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
                                <div class="py-[2px] font-bold text-[9px]">{{ $documentCode->document_code_no ?? 'FM-USTP-GCS-02' }}</div>
                                <div class="grid grid-cols-2 border-t border-black text-[9px]">
                                    <div class="border-r border-black py-[1px] font-semibold">Rev. No.</div>
                                    <div class="py-[1px] font-semibold">Effective Date</div>
                                </div>
                                <div class="grid grid-cols-2 border-t border-black text-[9px]">
                                    <div class="border-r border-black py-[1px]">{{ $documentCode->revision_no ?? '00' }}</div>
                                    <div class="py-[1px]">{{ $documentCode->effective_date ?? '03.17.25' }}</div>
                                </div>
                                <div class="border-t border-black py-[2px] text-[9px] font-semibold">
                                    Page No. {{ $documentCode->page_no ?? '2 of 2' }}
                                </div>
                            </div>
                        </div>

                        <!-- Subheader Line -->
                        <p class="text-center text-[11px] mt-2 leading-tight">
                            C.M. Recto Avenue, Lapasan, Cagayan de Oro City 9000 Philippines <br>
                            Tel Nos. +63 (88) 856 1738; Telefax +63 (88) 856 4696 | http://www.ustp.edu.ph
                        </p>

                        <!-- SECTION HEADER -->
                        <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] text-center tracking-wide mt-6">
                            OTHER INFORMATION
                        </div>

                        <!-- QUESTIONNAIRE -->
                        <div class="space-y-4">
                            <div>
                                <p><strong>1.</strong> Why did you choose this course/program?</p>
                                <textarea name="reason_for_course" rows="3" class="border-b border-gray-700 w-full mt-1">{{ old('reason_for_course', $pds->reason_for_course) }}</textarea>
                            </div>

                            <div>
                                <p><strong>2.</strong> How would you describe your family? Please put a check (/) mark on the space provided.</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    <label><input type="radio" name="family_description" value="harmonious" {{ old('family_description', $pds->family_description) == 'harmonious' ? 'checked' : '' }}> a family with harmonious relationship among family members</label><br>
                                    <label><input type="radio" name="family_description" value="conflict" {{ old('family_description', $pds->family_description) == 'conflict' ? 'checked' : '' }}> a family having conflict with some family members</label><br>
                                    <label><input type="radio" name="family_description" value="separated" {{ old('family_description', $pds->family_description) == 'separated' ? 'checked' : '' }}> a family with separated parents</label><br>
                                    <label><input type="radio" name="family_description" value="abroad" {{ old('family_description', $pds->family_description) == 'abroad' ? 'checked' : '' }}> a family with parents working abroad</label><br>
                                    <label><input type="radio" name="family_description" value="other" {{ old('family_description', $pds->family_description) == 'other' ? 'checked' : '' }}> others, pls. specify</label>
                                    <input type="text" name="family_description_other" value="{{ old('family_description_other', $pds->family_description_other ?? '') }}" class="border-b border-gray-700 w-full">
                                </div>
                            </div>

                            <div>
                                <p><strong>3.</strong> Where do you live right now? Please put a check (/) mark on the space provided.</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    <label><input type="radio" name="living_situation" value="home" {{ old('living_situation', $pds->living_situation) == 'home' ? 'checked' : '' }}> at home</label>
                                    <label><input type="radio" name="living_situation" value="boarding" {{ old('living_situation', $pds->living_situation) == 'boarding' ? 'checked' : '' }}> boarding house</label>
                                    <label><input type="radio" name="living_situation" value="relatives" {{ old('living_situation', $pds->living_situation) == 'relatives' ? 'checked' : '' }}> relatives</label>
                                    <label><input type="radio" name="living_situation" value="friends" {{ old('living_situation', $pds->living_situation) == 'friends' ? 'checked' : '' }}> friends</label>
                                    <label><input type="radio" name="living_situation" value="other" {{ old('living_situation', $pds->living_situation) == 'other' ? 'checked' : '' }}> others, pls. specify</label>
                                    <input type="text" name="living_situation_other" value="{{ old('living_situation_other', $pds->living_situation_other ?? '') }}" class="border-b border-gray-700 w-full">
                                </div>
                            </div>

                            <div>
                                <p><strong>4.</strong> Describe your living condition. Please put a check (/) mark on the space provided.</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    <label><input type="radio" name="living_condition" value="good" {{ old('living_condition', $pds->living_condition) == 'good' ? 'checked' : '' }}> good environment for learning</label>
                                    <label><input type="radio" name="living_condition" value="not_good" {{ old('living_condition', $pds->living_condition) == 'not_good' ? 'checked' : '' }}> not-so-good environment for learning</label>
                                </div>
                            </div>

                            <div>
                                <p><strong>5.</strong> Do you have any physical/health condition/s?</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    <label><input type="radio" name="health_condition" value="no" {{ old('health_condition', $pds->health_condition) == 'no' ? 'checked' : '' }}> No</label><br>
                                    <label><input type="radio" name="health_condition" value="yes" {{ old('health_condition', $pds->health_condition) == 'yes' ? 'checked' : '' }}> Yes, pls. specify</label>
                                    <input type="text" name="health_condition_specify" value="{{ old('health_condition_specify', $pds->health_condition_specify ?? '') }}" class="border-b border-gray-700 w-full">
                                </div>
                            </div>

                            <div>
                                <p><strong>6.</strong> Have you undergone intervention/treatment with a psychologist/psychiatrist?</p>
                                <div class="pl-6 mt-1 space-y-[2px]">
                                    <label><input type="radio" name="intervention" value="no" {{ old('intervention', $pds->intervention) == 'no' ? 'checked' : '' }}> No</label><br>
                                    <label><input type="radio" name="intervention" value="yes" {{ old('intervention', $pds->intervention) == 'yes' ? 'checked' : '' }}> Yes</label><br>
                                    </div>
                                    <div class="mt-2">
                                         <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] text-center tracking-wide mt-6">
                            CHECK THE SEMINARS/ACTIVITIES YOU WANT TO AVAIL FROM THE GUIDANCE SERVICES UNIT
                        </div>

                                    <label><input type="checkbox" name="intervention_types[]" value="adjustment" {{ in_array('adjustment', old('intervention_types', $pds->intervention_types ?? [])) ? 'checked' : '' }}> Adjustment (dealing with people, handling pressures, environment, class schedules, etc.)</label><br>
                                    <label><input type="checkbox" name="intervention_types[]" value="self_confidence" {{ in_array('self_confidence', old('intervention_types', $pds->intervention_types ?? [])) ? 'checked' : '' }}> Building Self-Confidence</label><br>
                                    <label><input type="checkbox" name="intervention_types[]" value="communication" {{ in_array('communication', old('intervention_types', $pds->intervention_types ?? [])) ? 'checked' : '' }}> Developing Communication Skills</label><br>
                                    <label><input type="checkbox" name="intervention_types[]" value="study_habits" {{ in_array('study_habits', old('intervention_types', $pds->intervention_types ?? [])) ? 'checked' : '' }}> Study Habits</label><br>
                                    <label><input type="checkbox" name="intervention_types[]" value="time_management" {{ in_array('time_management', old('intervention_types', $pds->intervention_types ?? [])) ? 'checked' : '' }}> Time Management</label><br>
                                    <label><input type="checkbox" name="intervention_types[]" value="tutorial_peers" {{ in_array('tutorial_peers', old('intervention_types', $pds->intervention_types ?? [])) ? 'checked' : '' }}> Tutorial with Peers (Please specify the subject/s)</label>
                                    <input type="text" name="tutorial_subjects" value="{{ old('tutorial_subjects', $pds->tutorial_subjects ?? '') }}" class="border-b border-gray-700 w-full"><br>
                                    <label><input type="checkbox" name="intervention_types[]" value="other" {{ in_array('other', old('intervention_types', $pds->intervention_types ?? [])) ? 'checked' : '' }}> others, pls. specify</label>
                                    <input type="text" name="intervention_other" value="{{ old('intervention_other', $pds->intervention_other ?? '') }}" class="border-b border-gray-700 w-full">
                                </div>
                            </div>

                                 <div class="font-bold bg-black text-white px-2 py-1 mb-3 uppercase text-[12px] text-center tracking-wide mt-6">
                            AWARDS AND RECOGNITION
                        </div>

                            <!-- AWARDS SECTION -->
                            <div>
                                <p class="font-semibold mt-4">AWARDS/RECOGNITION RECEIVED &nbsp;&nbsp;&nbsp;&nbsp; NAME OF SCHOOL/ORGANIZATION &nbsp;&nbsp;&nbsp;&nbsp; YEAR</p>
                                <div class="border-b border-gray-700 w-full mt-1"></div>

                                @for($i = 0; $i < 4; $i++)
                                <div class="grid grid-cols-3 gap-2 mt-2">
                                    <input type="text" name="awards[{{ $i }}][award]" value="{{ old('awards.' . $i . '.award', $pds->awards[$i]['award'] ?? '') }}" class="border-b border-gray-700 w-full" placeholder="Award/Recognition">
                                    <input type="text" name="awards[{{ $i }}][school]" value="{{ old('awards.' . $i . '.school', $pds->awards[$i]['school'] ?? '') }}" class="border-b border-gray-700 w-full" placeholder="School/Organization">
                                    <input type="text" name="awards[{{ $i }}][year]" value="{{ old('awards.' . $i . '.year', $pds->awards[$i]['year'] ?? '') }}" class="border-b border-gray-700 w-full" placeholder="Year">
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
                                    @php
                                        $signatureValue = $pds->signature ?: (Auth::user()->first_name . ' ' . (Auth::user()->middle_name ?? '') . ' ' . Auth::user()->last_name);
                                        $isSignatureReadonly = !empty($pds->signature) || !empty(Auth::user()->first_name);
                                        $signatureValue = trim(preg_replace('/\s+/', ' ', $signatureValue)); // Remove extra spaces
                                    @endphp
                                    @if($isSignatureReadonly)
                                        <div class="border-b border-gray-400 bg-gray-100 w-[250px] text-center text-gray-600 py-1 px-1 cursor-not-allowed select-none">
                                            {{ $signatureValue }}
                                        </div>
                                        <input type="hidden" name="signature" value="{{ $signatureValue }}">
                                    @else
                                        <input type="text" name="signature" value="{{ old('signature', $signatureValue) }}" placeholder="E-signature or Name" class="border-b border-gray-800 w-[250px] text-center">
                                    @endif
                                    <p class="text-[12px] mt-1">SIGNATURE OVER PRINTED NAME</p>
                                </div>
                                <div class="text-center">
                                    <input type="date" name="signature_date" value="{{ old('signature_date', $pds->signature_date ? $pds->signature_date->format('Y-m-d') : \Carbon\Carbon::now('Asia/Manila')->format('Y-m-d')) }}" class="border-b border-gray-800 w-[150px] text-center">
                                    <p class="text-[12px] mt-1">DATE</p>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="mt-8 text-center">
                                <button type="submit" id="saveBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg">
                                    Save Personal Data Sheet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" style="z-index: 9999;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" style="position: relative; z-index: 10000;">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4" id="modalTitle">Confirm Save</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="modalMessage">
                        Some fields are blank. Are you sure you want to save the Personal Data Sheet?
                    </p>
                </div>
                <div class="flex items-center px-4 py-3">
                    <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-300 text-gray-900 text-base font-medium rounded-md w-full mr-2 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button type="button" id="confirmBtn" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Save Anyway
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const saveBtn = document.getElementById('saveBtn');
            const modal = document.getElementById('confirmationModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const confirmBtn = document.getElementById('confirmBtn');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const form = document.getElementById('pdsForm');

            // Handle form submission for main save button
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Always prevent default first
                
                const inputs = form.querySelectorAll('input:not([type="radio"]):not([type="checkbox"]), textarea');
                let hasBlankFields = false;

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        hasBlankFields = true;
                    }
                });

                if (hasBlankFields) {
                    modalTitle.textContent = 'Confirm Save';
                    modalMessage.textContent = 'Some fields are blank. Are you sure you want to save the Personal Data Sheet?';
                    modal.classList.remove('hidden');
                } else {
                    // No blank fields, submit normally
                    form.submit();
                }
            });

            cancelBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            confirmBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
                // Submit the form directly
                form.submit();
            });

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>

    <style>
        @media print {
            button, .bg-blue-600, .hover\:bg-blue-700 {
                display: none !important;
            }
            input, textarea {
                background: white !important;
                border: 1px solid black !important;
                padding: 2px !important;
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
    </style>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                // Check file size (max 2MB for 2x2 pictures)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB. Please choose a smaller image.');
                    event.target.value = '';
                    return;
                }

                // Check file type
                if (!file.type.match('image/jpeg') && !file.type.match('image/png') && !file.type.match('image/jpg') && !file.type.match('image/gif')) {
                    alert('Please select a valid image file (JPEG, PNG, or GIF).');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Proceed with preview - the frame will handle display with object-cover
                    const photoBox = document.querySelector('.photo-box');
                    const fileInput = photoBox.querySelector('input[type="file"]');
                    
                    // Clear the photo box but preserve the file input
                    photoBox.innerHTML = '';
                    
                    // Create and append the image
                    const previewImg = document.createElement('img');
                    previewImg.src = e.target.result;
                    previewImg.alt = 'ID Photo';
                    previewImg.className = 'w-full h-full object-cover';
                    photoBox.appendChild(previewImg);
                    
                    // Re-append the file input
                    photoBox.appendChild(fileInput);
                    
                    // Add click handler back to the photo box
                    photoBox.onclick = function() {
                        document.getElementById('photoInput').click();
                    };
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
