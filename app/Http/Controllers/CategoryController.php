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

   

   


   
}
