<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        return $user->notes()->with(['categories', 'images', 'urls'])->get();
    }


    public function store(Request $request)
    {
        $note = new Note($request->all());
        $note->user_id = Auth::id();
        $note->save();

        if ($request->has('categories')) {
            $note->categories()->sync($request->categories);
        }

        if ($request->hasFile('images')) {
            $this->syncImages($note, $request->file('images'));
        }

        if ($request->has('urls')) {
            $this->syncUrls($note, $request->urls);
        }

        return response()->json($note->load(['categories', 'images', 'urls']), 201);
    }


    public function show(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $note->load(['categories', 'images', 'urls']);
    }


    public function update(Request $request, Note $note)
    {

        if ($note->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->all();

        if ($request->hasFile('images')) {
            $files = $request->file('images');
        } else {
            $files = [];
        }

        // Debugging için loglama
        Log::info('Data: ' . json_encode($data));
        Log::info('Files: ' . json_encode($files));

        $note->update($data);

        if ($request->has('categories')) {
            $note->categories()->sync($request->categories);
        }

        if ($request->hasFile('images')) {
            $this->syncImages($note, $request->file('images'));
        }

        if ($request->has('urls')) {
            $this->syncUrls($note, $request->urls);
        }

        return response()->json($note->load(['categories', 'images', 'urls']));
    }


    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(null, 204);
    }

    private function syncImages(Note $note, $images)
    {
        //Log::info('Images:', ['images' => $images]); // $images değişkenini loglayın.

        $note->images()->delete(); // Tüm eski resimleri sil
        foreach ($images as $image) {
            //Log::info('Processing image:', ['image' => $image]); // Her bir $image değişkenini loglayın.
            $path = $image->store('images', 'public');
            $note->images()->create(['file_path' => $path]);
        }
    }

    private function syncUrls(Note $note, $urls)
    {
        $note->urls()->delete(); // Tüm eski URL'leri sil
        foreach ($urls as $url) {
            $note->urls()->create(['url' => $url]);
        }
    }
}
