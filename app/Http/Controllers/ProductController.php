<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        
        try {
        // 1. Get sort parameters with fallbacks
        $sortBy = $request->input('sortBy', 'id');
        $sortDir = $request->input('sortDir', 'desc');

        // 2. Read 'per_page' from React (accepts both per_page and perpage, defaults to 10)
        $perPage = $request->input('per_page', $request->input('perpage', 10));

        // 3. Search filter
            $search = $request->input('search');

            $products = Product::with('category')
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'LIKE', "%{$search}%");
                })
                ->orderBy($sortBy, $sortDir)
                ->paginate($perPage);

            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'price'       => 'required|numeric|min:0',
                'stock'       => 'required|integer|min:0',
                'skin_type'   => 'nullable|string|max:255',
                'category_id' => 'nullable|exists:categories,id',
                'description' => 'nullable|string',
                'product_image' => 'nullable|string',
            ]);

            $product = Product::create($validated);

            return response()->json($product, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'name'          => 'sometimes|string|max:255',
                'price'         => 'sometimes|numeric|min:0',
                'stock'         => 'sometimes|integer|min:0',
                'skin_type'     => 'sometimes|nullable|string|max:255',
                'category_id'   => 'sometimes|nullable|exists:categories,id',
                'description'   => 'sometimes|nullable|string',
                'product_image' => 'sometimes|nullable|string',
            ]);

            $product = Product::findOrFail($id);
            $product->update($validated);

            return response()->json($product);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
