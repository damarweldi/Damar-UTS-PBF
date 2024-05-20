<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ProductController extends Controller
{
    public function index()
    {
        if (Product::count() == 0) {
            return response()->json([
                'message' => 'Product Not Avalible'
            ], 404);
        }
        $products = Product::join('categories', 'products.category_id', '=', 'categories.id')->select('products.id', 'products.name', 'products.description', 'products.price', 'products.image', 'products.expired_at', 'products.modified_by', 'products.created_at', 'products.updated_at', 'categories.name as category_name')->get();
        return response()->json($products, 200);
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            return response()->json([
                'message' => 'Invalid product ID. ID must be numeric.'
            ], 422);
        }
        $product = Product::find($id);
        if ($product) {
            return response()->json($product, 200);
        } else {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'expired_at' => 'required',
        ]);

        $category = Category::where('name', $request->category_id)->first();
        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }
        $id_category = Category::where('name', $request->category_id)->value('id');
        $validator->setData(array_merge($validator->getData(), ['category_id' => $id_category]));
        if ($validator->fails()) {
            return response()->json([
                'data' => [
                    'success' => false,
                    'errors' => $validator->errors()
                ]
            ], 422);
        }
        $imagePath = $request->file('image')->store('images', 'public');
        $product = new Product($validator->validated());
        $product->image = env('APP_URL') . '/storage/' . $imagePath;
        $product->modified_by = 'comingsoon';
        $product->save();
        return response()->json([
            'message' => 'Product created successfully'
        ], 200);
    }
    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'expired_at' => 'required',
        ]);

        $category = Category::where('name', $request->category_id)->first();
        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan', 'name' => $request->name], 404);
        }
        $id_category = Category::where('name', $request->category_id)->value('id');
        $validator->setData(array_merge($validator->getData(), ['category_id' => $id_category]));
        if ($validator->fails()) {
            return response()->json([
                'data' => [
                    'success' => false,
                    'errors' => $validator->errors()
                ]
            ], 422);
        }
        $imagePath = $request->file('image')->store('images', 'public');
        $product = Product::find($id);
        if ($product) {
            $product->update($validator->validated());
            if ($request->hasFile('image')) {
                $product->image = env('APP_URL') . '/storage/' . $imagePath;
                $product->modified_by = 'comingsoon';
                $product->save();
            }
            return response()->json([
                'message' => 'Product updated successfully'
            ], 200);
        }
        return response()->json([
            'message' => 'Product not found'
        ], 404);
    }
    public function deleteProduct($id)
    {
        if (!is_numeric($id)) {
            return response()->json([
                'data' => [
                    'success' => false,
                    'message' => 'Invalid product ID. ID must be numeric.',
                ]
            ], 422);
        }
        $produk = Product::where('id', $id)->first();
        if ($produk) {
            Product::where('id', $id)->delete();
            return response()->json([
                'data' => [
                    'success' => true,
                    'message' => 'Product deleted successfully',
                ]
            ], 200);
        } else {
            return response()->json([
                'data' => [
                    'success' => false,
                    'message' => 'Product not found',
                ]
            ], 404);
        }
    }
}
