<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function translations(): BelongsToMany
    {
        return $this->belongsToMany(Translation::class, 'tag_translation');
    }
}