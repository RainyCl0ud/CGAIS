<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidId extends Model
{
    protected $fillable = [
        'id_code',
        'type',
        'is_used',
        'email',
    ];

    // Scope for unused IDs
    public function scopeUnused($query) {
        return $query->where('is_used', false);
    }

    // Scope for type
    public function scopeOfType($query, $type) {
        return $query->where('type', $type);
    }
}
