<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    
    public function index()
    {
        return Category::where('user_id', Auth::id())->get();
    }

    
    public function store(Request $request)
    {
        $category = new Category($request->all());
        $category->user_id = Auth::id();
        $category->save();
        
        return response()->json($category, 201);
    }

    
    public function show(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($category);
    }

    
    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $category->update($request->all());
        return response()->json($category);
    }

    
    public function destroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $category->delete();
        return response()->json(null, 204);
    }
}
