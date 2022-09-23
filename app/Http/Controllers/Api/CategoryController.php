<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\StoreCategory;
use App\Http\Requests\UpdateCategory;

class CategoryController extends Controller
{
    public function list()
    {
        return response()->json([
            'message' => __('Categories listed successfully'),
            'data'    => Category::orderBy('name')->get()
        ], 200);
    }

    public function store(StoreCategory $request)
    {
        $data = $request->validated();

        Category::create([
            'name'  => $data['name'],
            'color' => $data['color']
        ]);

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Criou uma categoria chamada " . $data['name']
        ]);

        return response()->json([
            'message' => __('Category created successfully')
        ], 200);
    }

    public function update(UpdateCategory $request, $category)
    {
        $data = $request->validated();

        $category->name  = $data['name'];
        $category->color = $data['color'];

        $category->save();

        Log::create([
            'user' => auth()->user()->name,
            'action' => "Editou a categoria " . $category->name
        ]);

        return response()->json([
            'message' => __('Category updated successfully')
        ], 200);
    }

    public function destroy($category)
    {
        Log::create([
            'user' => auth()->user()->name,
            'action' => "Removeu a categoria " . $category->name
        ]);

        $category->delete();

        return response()->json([
            'message' => __('Category removed successfully')
        ], 200);
    }
}
