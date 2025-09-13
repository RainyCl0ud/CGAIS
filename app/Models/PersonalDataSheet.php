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
        'birth_date',
        'birth_place',
        'sex',
        'civil_status',
        'citizenship',
        'height',
        'weight',
        'blood_type',
        'mobile_number',
        'telephone_number',
        'permanent_address',
        'present_address',
        'father_name',
        'father_occupation',
        'father_contact',
        'mother_name',
        'mother_occupation',
        'mother_contact',
        'guardian_name',
        'guardian_relationship',
        'guardian_contact',
        'elementary_school',
        'elementary_year_graduated',
        'high_school',
        'high_school_year_graduated',
        'college',
        'college_year_graduated',
        'course',
        'year_level',
        'student_id_number',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_number',
        'emergency_contact_address',
        'medical_conditions',
        'allergies',
        'medications',
        'hobbies',
        'interests',
        'goals',
        'concerns',
    ];

    protected $casts = [
        'birth_date' => 'date',
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