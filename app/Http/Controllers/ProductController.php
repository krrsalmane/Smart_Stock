<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\AlertService;
use App\Services\ArchiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    
    public function index()
    {
        $products = Product::with(['category', 'warehouse'])->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
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

        $product = Product::create($request->all());
        AlertService::checkStockLevels($product);

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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
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

        // Archive before update if significant fields are changing
        $significantFields = ['quantity', 'price', 'name', 'sku'];
        $changes = [];
        foreach ($significantFields as $field) {
            if ($request->has($field) && $product->$field != $request->$field) {
                $changes[$field] = [
                    'old' => $product->$field,
                    'new' => $request->$field
                ];
            }
        }
        
        if (!empty($changes)) {
            ArchiveService::archiveBeforeUpdate($product, $changes);
        }

        // Update only provided fields
        $product->update($request->all());

        // Check stock levels and update alerts if needed
        AlertService::checkStockLevels($product);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->load(['category', 'warehouse'])
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        try {
            ArchiveService::archiveBeforeDelete($product, 'Product deleted from system');
        } catch (\Exception $e) {
            \Log::error('Failed to archive product before deletion', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }

        AlertService::dismissProductAlerts($product);
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
