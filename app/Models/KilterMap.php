<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KilterMap extends Model
{
    protected $table = 'kilter_maps';

    protected $fillable = [
        'name',
        'kokapena',
        'image',
        'image_physical_path',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(KilterBlock::class, 'map_id');
    }
}
