<?php

namespace App\Http\Controllers;

use App\Models\MultimediaPhoto;
use App\Models\WeatherLocation;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $heroPhoto = MultimediaPhoto::query()->inRandomOrder()->first();
        $locations = WeatherLocation::query()->orderBy('name')->get();

        return view('home', [
            'heroPhoto' => $heroPhoto,
            'weatherLocations' => $locations,
        ]);
    }
}
