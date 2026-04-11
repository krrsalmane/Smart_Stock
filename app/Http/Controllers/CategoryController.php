<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Get all categories with their products
     */
    public function index()
    {
        $categories = Category::with('products')->get();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'categories' => $categories
        ]);
    }

    /**
     * Create a new category
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::create($request->all());

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category->load('products')
        ], 201);
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
    }


   
}
