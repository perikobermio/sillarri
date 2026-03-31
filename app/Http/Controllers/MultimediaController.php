<?php

namespace App\Http\Controllers;

use App\Models\MultimediaPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MultimediaController extends Controller
{
    public function index(Request $request): View
    {
        $photos = MultimediaPhoto::query()
            ->where('user_id', $request->user()->id)
            ->with('user')
            ->latest()
            ->get();

        return view('multimedia.index', [
            'photos' => $photos,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'tags' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:20480'],
        ]);

        $path = $request->file('image')->store('multimedia', 'public');

        MultimediaPhoto::create([
            'user_id' => $request->user()->id,
            'title' => trim((string) $data['title']),
            'description' => $data['description'] ?? null,
            'tags' => $data['tags'] ?? null,
            'image_path' => $path,
        ]);

        return redirect()
            ->route('multimedia')
            ->with('status', 'Argazkia ondo igo da.');
    }
}
