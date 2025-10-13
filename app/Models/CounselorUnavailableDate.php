<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounselorUnavailableDate extends Model
{
    use HasFactory;

    protected $table = 'counselor_unavailable_dates';

    protected $fillable = [
        'counselor_id',
        'date',
        'is_unavailable',
    ];

    protected $casts = [
        'date' => 'date',
        'is_unavailable' => 'boolean',
    ];
}
