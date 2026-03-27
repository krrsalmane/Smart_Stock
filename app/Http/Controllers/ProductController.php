<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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


}
