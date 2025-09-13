<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'counselor_id',
        'appointment_id',
        'counselor_rating',
        'service_rating',
        'facility_rating',
        'overall_satisfaction',
        'counselor_feedback',
        'service_feedback',
        'suggestions',
        'concerns',
        'would_recommend',
        'recommendation_reason',
        'additional_comments',
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // Helper methods
    public function getAverageRating(): float
    {
        $ratings = array_filter([
            $this->counselor_rating,
            $this->service_rating,
            $this->facility_rating,
            $this->overall_satisfaction
        ]);
        
        return count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 1) : 0;
    }

    public function getRatingStars($rating): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $rating ? '★' : '☆';
        }
        return $stars;
    }

    public function getRecommendationLabel(): string
    {
        return $this->would_recommend ? 'Yes' : 'No';
    }

    public function getRecommendationBadgeClass(): string
    {
        return $this->would_recommend 
            ? 'bg-green-100 text-green-800' 
            : 'bg-red-100 text-red-800';
    }
} 