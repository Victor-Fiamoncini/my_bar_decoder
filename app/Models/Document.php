<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory, HasTimestamps;

    protected $fillable = [
        'user_id',
        'filename',
        'code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
