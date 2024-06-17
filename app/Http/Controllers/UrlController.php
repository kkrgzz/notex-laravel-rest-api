<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function index()
    {
        return Url::all();
    }

    public function store(Request $request)
    {
        $url = Url::create($request->all());
        return response()->json($url, 201);
    }

    public function show(Url $url)
    {
        return $url;
    }

    public function update(Request $request, Url $url)
    {
        $url->update($request->all());
        return response()->json($url);
    }

    public function destroy(Url $url)
    {
        $url->delete();
        return response()->json(null, 204);
    }
}
