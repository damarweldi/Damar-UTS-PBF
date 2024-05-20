<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function create_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $category_name = Category::where('name', $request->name)->first();
        if ($category_name) {
            return response()->json(['message' => 'Category name already exists'], 422);
        }
        Category::create($validator->validated());
        return response()->json([
            'message' => 'Category created successfully',
            'data-send' => $validator->validate()
        ], 200);
    }
    public function get_category()
    {
        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => Category::all(),
        ]);
    }

    public function update_category(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category_name = Category::where('name', $request->name)->first();
        if ($category_name) {
            return response()->json(['message' => 'Category name already exists'], 422);
        }
        $category->update($validator->validated());
        return response()->json([
            'message' => 'Category updated successfully',
        ], 200);
    }

    public function delete_category($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
