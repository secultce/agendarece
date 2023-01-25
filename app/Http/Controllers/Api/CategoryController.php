<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\StoreCategory;
use App\Http\Requests\UpdateCategory;
use App\Models\Log;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        $categories = Category::orderBy('name');
        $sector     = $request->sector ? $request->sector->id : (auth()->user()->sector ? auth()->user()->sector->id : null);

        if ($sector) $categories->where('sector_id', $sector);

        return response()->json([
            'message' => __('Categories listed successfully'),
            'data'    => $categories->get()
        ], 200);
    }

    public function store(StoreCategory $request)
    {
        $data = $request->validated();

        Category::create([
            'sector_id' => auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'],
            'name'      => $data['name'],
            'color'     => $data['color']
        ]);

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Criou uma categoria chamada " . $data['name']
        ]);

        return response()->json([
            'message' => __('Category created successfully')
        ], 200);
    }

    public function update(UpdateCategory $request, $category)
    {
        $data = $request->validated();

        $category->sector_id = auth()->user()->role->tag !== 'administrator' ? (auth()->user()->sector->id ?? null) : $data['sector'];
        $category->name      = $data['name'];
        $category->color     = $data['color'];

        $category->save();

        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Editou a categoria " . $category->name
        ]);

        return response()->json([
            'message' => __('Category updated successfully')
        ], 200);
    }

    public function destroy($category)
    {
        Log::create([
            'sector' => auth()->user()->sector->name ?? null,
            'user'   => auth()->user()->name,
            'action' => "Removeu a categoria " . $category->name
        ]);

        $category->delete();

        return response()->json([
            'message' => __('Category removed successfully')
        ], 200);
    }
}
