<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    /**
     * Get all categories with their products
     */
    public function index()
    {
        $categories = Category::with('products')->get();

        return response()->json($categories);
    }

    /**
     * Create a new category
     */
    public function store(Request $request)
    {
        Log::info('Category creation attempt', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            Log::error('Category validation failed', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = Category::create($request->all());
            Log::info('Category created successfully', ['id' => $category->id]);

            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category
            ], 201);
        } catch (\Exception $e) {
            Log::error('Category creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific category with its products
     */
    public function show($id)
    {
        try {
            $category = Category::with('products')->findOrFail($id);

            return response()->json([
                'message' => 'Category retrieved successfully',
                'category' => $category
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
    }

    /**
     * Update a category
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $id,
            'description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update($request->all());

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category->load('products')
        ]);
    }

    /**
     * Delete a category
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
