<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCode extends Model
{
    protected $fillable = [
        'type',
        'document_code_no',
        'revision_no',
        'effective_date',
        'page_no',
    ];
}
