<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KilterLocation extends Model
{
    protected $table = 'kilter_locations';

    protected $fillable = [
        'name',
    ];

    public function maps(): HasMany
    {
        return $this->hasMany(KilterMap::class, 'kokapena', 'name');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(KilterBlock::class, 'kokapena', 'name');
    }
}
