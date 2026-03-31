<?php

namespace App\Http\Controllers;

use App\Models\MultimediaPhoto;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $heroPhoto = MultimediaPhoto::query()->inRandomOrder()->first();

        return view('home', [
            'heroPhoto' => $heroPhoto,
        ]);
    }
}
