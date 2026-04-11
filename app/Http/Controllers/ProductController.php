<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    
        public function index()
    {
        $products = Product::with(['category', 'warehouse'])->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        // 1. Validation to ensure data integrity
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'sku'             => 'required|string|unique:products,sku',
            'quantity'        => 'required|integer|min:0',
            'price'           => 'required|numeric|min:0',
            'alert_threshold' => 'required|integer|min:0',
            'category_id'     => 'required|exists:categories,id',
            'warehouse_id'    => 'required|exists:warehouses,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Create the Product record
        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Product added to inventory successfully',
            'product' => $product->load(['category', 'warehouse'])
        ], 201);
    }

    public function show($id)
    {
        try {
            $product = Product::with(['category', 'warehouse', 'alerts', 'commands', 'movements'])->findOrFail($id);
            
            return response()->json([
                'message' => 'Product retrieved successfully',
                'product' => $product
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        // Validation (SKU should be unique except for current product)
        $validator = Validator::make($request->all(), [
            'name'            => 'sometimes|string|max:255',
            'sku'             => 'sometimes|string|unique:products,sku,' . $id,
            'quantity'        => 'sometimes|integer|min:0',
            'price'           => 'sometimes|numeric|min:0',
            'alert_threshold' => 'sometimes|integer|min:0',
            'category_id'     => 'sometimes|exists:categories,id',
            'warehouse_id'    => 'sometimes|exists:warehouses,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update only provided fields
        $product->update($request->all());

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->load(['category', 'warehouse'])
        ], 200);
    }

    


}
