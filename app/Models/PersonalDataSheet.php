<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalDataSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'age',
        'birth_place',
        'sex',
        'civil_status',
        'religion',
        'contact_number',
        'email',
        'citizenship',
        'height',
        'weight',
        'blood_type',
        'mobile_number',
        'telephone_number',
        'permanent_address',
        'present_address',
        'father_name',
        'father_age',
        'father_occupation',
        'father_contact',
        'father_education',
        'mother_name',
        'mother_age',
        'mother_occupation',
        'mother_contact',
        'mother_education',
        'parents_address',
        'spouse_name',
        'spouse_contact',
        'spouse_occupation',
        'spouse_education',
        'guardian_name',
        'guardian_age',
        'guardian_occupation',
        'guardian_contact',
        'guardian_relationship',
        'course',
        'major',
        'year_level',
        'last_school',
        'school_location',
        'previous_course',
        'elementary_school',
        'elementary_year_graduated',
        'high_school',
        'high_school_year_graduated',
        'college',
        'college_year_graduated',
        'student_id_number',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_number',
        'emergency_contact_address',
        'medical_conditions',
        'allergies',
        'medications',
        'reason_for_course',
        'family_description',
        'family_description_other',
        'living_situation',
        'living_situation_other',
        'living_condition',
        'physical_conditions',
        'intervention_treatment',
        'intervention_details',
        'awards',
        'hobbies',
        'interests',
        'goals',
        'concerns',
        'health_condition',
        'health_condition_specify',
        'intervention',
        'intervention_types',
        'tutorial_subjects',
        'intervention_other',
        'signature',
        'signature_date',
        'photo',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'signature_date' => 'date',
        'intervention_types' => 'array',
        'awards' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function isComplete(): bool
    {
        return !empty($this->birth_date) && 
               !empty($this->birth_place) && 
               !empty($this->sex) && 
               !empty($this->mobile_number) && 
               !empty($this->permanent_address);
    }

    public function getCompletionPercentage(): int
    {
        $fields = [
            'birth_date', 'birth_place', 'sex', 'civil_status', 'citizenship',
            'mobile_number', 'permanent_address', 'father_name', 'mother_name',
            'emergency_contact_name', 'emergency_contact_number'
        ];
        
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }
        
        return round(($filled / count($fields)) * 100);
    }

    public function getSexLabel(): string
    {
        return match($this->sex) {
            'male' => 'Male',
            'female' => 'Female',
            default => 'Not specified'
        };
    }

    public function getCivilStatusLabel(): string
    {
        return match($this->civil_status) {
            'single' => 'Single',
            'married' => 'Married',
            'widowed' => 'Widowed',
            'separated' => 'Separated',
            'divorced' => 'Divorced',
            default => 'Not specified'
        };
    }
} 