<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // Menampilkan daftar kategori
    public function index()
    {
        return resposne()->json(Category::all());
    }

    // Menampilkan detail kategori
    public function show(Category $category)
    {
        return response()->json($category);
    }

    // Mengirim data kategori
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|unique:categories,category_name',
        ]);

        $category = Category::create($request->all());

        return response()->json($category, 201);
    }

    // Update data kategori
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|unique:categories,category_name,' . $category->id_category . ',id_category'
        ]);

        $category->update($request->all());

        return response()->json($category);
    }

    // Menghapus data kategori
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Kategori Deleted Successfully'
        ]);
    }
}
