<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Providers\CacheServiceProvider;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'email',
        'phone_number',
        'password',
        'role',
        'availability_status',
        'unavailable_from',
        'unavailable_to',
        'is_active',
        'student_id',
        'faculty_id',
        'staff_id',
        'course_id',
        'course_category',
        'year_level',
        'pending_email',
        'pending_email_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'unavailable_from' => 'datetime:H:i',
            'unavailable_to' => 'datetime:H:i',
            'is_active' => 'boolean',
        ];
    }

    // Role helper methods
    public function isStudent() { return $this->role === 'student'; }
    public function isFaculty() { return $this->role === 'faculty'; }
    public function isCounselor() { return $this->role === 'counselor'; }
    public function isAssistant() { return $this->role === 'assistant'; }
    public function isStaff() { return $this->role === 'staff'; }

    // Active status helper methods
    public function isActive() { return $this->is_active; }
    public function isInactive() { return !$this->is_active; }

    // Static method to count active counselors
    public static function activeCounselorsCount()
    {
        return self::where('role', 'counselor')->where('is_active', true)->count();
    }

    /**
     * Get the role display name
     */
    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            'student' => 'Student',
            'faculty' => 'Faculty',
            'counselor' => 'Counselor',
            'assistant' => 'Assistant',
            'staff' => 'Non-Teaching Staff',
            default => ucfirst($this->role)
        };
    }

    // Privilege helper methods
    public function canManageUsers() { return $this->isCounselor() || $this->isAssistant(); }
    public function canApproveAppointments() { return $this->isCounselor() || $this->isAssistant(); }
    public function canGenerateReports() { return $this->isCounselor() || $this->isAssistant(); }
    public function canCreateCounselingHistory() { return $this->isCounselor() || $this->isAssistant(); }
    public function canHandleUrgentAppointments() { return $this->isCounselor() || $this->isAssistant(); }
    public function canSendOfficialNotifications() { return $this->isCounselor() || $this->isAssistant(); }
    public function canViewAppointmentNotes() { return $this->isCounselor() || $this->isAssistant(); }
    public function canAccessBackupData() { return $this->isCounselor() || $this->isAssistant(); }
    public function canManageAllAppointments() { return $this->isCounselor() || $this->isAssistant(); }
    public function canManageSchedules() { return $this->isCounselor() || $this->isAssistant(); }
    public function canViewStudentPDS() { return $this->isCounselor() || $this->isAssistant(); }
    public function canBookAppointments() { return $this->isStudent() || $this->isFaculty() || $this->isStaff(); }
    
    // Assistant-specific methods
    public function canViewUserProfiles() { return $this->isAssistant() || $this->isCounselor(); }
    public function canViewCounselingHistory() { return $this->isAssistant() || $this->isCounselor(); }
    public function canViewRecentAppointments() { return $this->isAssistant() || $this->isCounselor(); }
    public function canViewSchedules() { return $this->isAssistant() || $this->isCounselor(); }
    public function canReceiveNotifications() { return $this->isAssistant() || $this->isCounselor(); }

    // Relationships
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function counselorAppointments()
    {
        return $this->hasMany(Appointment::class, 'counselor_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'counselor_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function personalDataSheet()
    {
        return $this->hasOne(PersonalDataSheet::class);
    }

    public function feedbackForms()
    {
        return $this->hasMany(FeedbackForm::class);
    }

    public function receivedFeedback()
    {
        return $this->hasMany(FeedbackForm::class, 'counselor_id');
    }
    

    // Helper methods
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name . ' ' . $this->last_name;
        if ($this->middle_name) {
            $name = $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
        }
        if ($this->name_extension) {
            $name .= ' ' . $this->name_extension;
        }
        return $name;
    }

    // Alias for backward compatibility
    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function getUnreadNotificationsCount(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Availability helpers
     */
    public function isAvailable(): bool
    {
        return $this->availability_status === 'AVAILABLE';
    }

    public function isOnLeave(): bool
    {
        return $this->availability_status === 'ON_LEAVE';
    }

    public function isTemporarilyUnavailable(): bool
    {
        return $this->availability_status === 'UNAVAILABLE';
    }

    /**
     * Determine if counselor is available for a specific slot.
     *
     * Rules:
     * - AVAILABLE: always available (other checks like schedule still apply separately)
     * - ON_LEAVE / UNAVAILABLE with no time range: full-day unavailability (no slots allowed)
     * - ON_LEAVE / UNAVAILABLE with a time range: unavailable only within that range
     * - Check CounselorUnavailableDate for specific date unavailability
     */
    public function isAvailableForSlot(Carbon $date, Carbon $slotStart, Carbon $slotEnd): bool
    {
        if (!$this->isCounselor()) {
            return true;
        }

        // Check if date is marked as unavailable in CounselorUnavailableDate
        $isDateUnavailable = \App\Models\CounselorUnavailableDate::where('counselor_id', $this->id)
            ->where('date', $date->toDateString())
            ->where('expires_at', '>', Carbon::now('Asia/Manila'))
            ->exists();

        if ($isDateUnavailable) {
            return false;
        }

        if ($this->availability_status === 'AVAILABLE') {
            return true;
        }

        // Full-day unavailability
        if (!$this->unavailable_from || !$this->unavailable_to) {
            return false;
        }

        // Compare only times (ignore date portion)
        $unavailableStart = Carbon::createFromTime(
            $this->unavailable_from->format('H'),
            $this->unavailable_from->format('i')
        );
        $unavailableEnd = Carbon::createFromTime(
            $this->unavailable_to->format('H'),
            $this->unavailable_to->format('i')
        );

        $slotStartTime = Carbon::createFromTime($slotStart->format('H'), $slotStart->format('i'));
        $slotEndTime = Carbon::createFromTime($slotEnd->format('H'), $slotEnd->format('i'));

        // Overlap check between slot and unavailable window
        $overlapsUnavailable = $slotStartTime < $unavailableEnd && $slotEndTime > $unavailableStart;

        return !$overlapsUnavailable;
    }

    public function getPendingAppointments()
    {
        return $this->appointments()->pending()->orderBy('appointment_date')->orderBy('start_time');
    }

    public function getUpcomingAppointments()
    {
        return $this->appointments()->upcoming()->orderBy('appointment_date')->orderBy('start_time');
    }

    /**
     * Generate a new email verification token
     */
    public function generateEmailVerificationToken()
    {
        $this->email_verification_token = \Illuminate\Support\Str::random(64);
        $this->save();
        return $this->email_verification_token;
    }

    /**
     * Verify the email using the provided token
     */
    public function verifyEmailWithToken($token)
    {
        if ($this->email_verification_token === $token && !$this->hasVerifiedEmail()) {
            $this->markEmailAsVerified();
            $this->email_verification_token = null;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Generate a pending email change
     */
    public function generatePendingEmailChange($newEmail)
    {
        $this->pending_email = $newEmail;
        $this->pending_email_token = \Illuminate\Support\Str::random(64);
        $this->save();
        return $this->pending_email_token;
    }

    /**
     * Verify pending email change
     */
    public function verifyPendingEmailChange($token)
    {
        if ($this->pending_email_token === $token && $this->pending_email) {
            // Update the email with the pending email
            $this->email = $this->pending_email;
            $this->pending_email = null;
            $this->pending_email_token = null;
            $this->markEmailAsVerified();
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Clear pending email change
     */
    public function clearPendingEmailChange()
    {
        $this->pending_email = null;
        $this->pending_email_token = null;
        $this->save();
    }

    /**
     * Check if user has a pending email change
     */
    public function hasPendingEmailChange()
    {
        return !empty($this->pending_email) && !empty($this->pending_email_token);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::updated(function ($user) {
            // Clear cache when user is updated to ensure changes are reflected immediately
            CacheServiceProvider::clearRelatedCaches('User', $user->id);
        });
    }
}
