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

        return response()->json([
            'message' => __('Category updated successfully')
        ], 200);
    }

    public function destroy($category)
    {
        $category->delete();

        return response()->json([
            'message' => __('Category removed successfully')
        ], 200);
    }
}
