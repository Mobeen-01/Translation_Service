<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TranslationKey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'description',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }
}