<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KilterBlock extends Model
{
    protected $table = 'kilter_blocks';

    protected $fillable = [
        'name',
        'description',
        'grade',
        'kokapena',
        'map_id',
        'user_id',
        'boulder',
    ];

    public function map(): BelongsTo
    {
        return $this->belongsTo(KilterMap::class, 'map_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
