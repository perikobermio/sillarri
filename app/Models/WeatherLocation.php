<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherLocation extends Model
{
    protected $fillable = [
        'name',
        'label',
        'lat',
        'lon',
    ];
}
