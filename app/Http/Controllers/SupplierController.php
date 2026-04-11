<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Get all suppliers
     */
    public function index()
    {
        $suppliers = Supplier::with(['products', 'commands'])->get();

        return response()->json([
            'message' => 'Suppliers retrieved successfully',
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Create a new supplier
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $supplier = Supplier::create($request->all());

        return response()->json([
            'message' => 'Supplier created successfully',
            'supplier' => $supplier->load(['products', 'commands'])
        ], 201);
    }

    /**
     * Get a specific supplier
     */
    public function show($id)
    {
        try {
            $supplier = Supplier::with(['products', 'commands'])->findOrFail($id);

            return response()->json([
                'message' => 'Supplier retrieved successfully',
                'supplier' => $supplier
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }
    }

    /**
     * Update a supplier
     */
    public function update(Request $request, $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:suppliers,email,' . $id,
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $supplier->update($request->all());

        return response()->json([
            'message' => 'Supplier updated successfully',
            'supplier' => $supplier->load(['products', 'commands'])
        ]);
    }

    /**
     * Delete a supplier
     */
    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully'
        ]);
    }

    /**
     * Link a product to a supplier with cost price and lead time
     */
    public function attachProduct(Request $request, $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'cost_price' => 'required|numeric|min:0',
            'lead_time' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if product is already attached
        if ($supplier->products()->where('product_id', $request->product_id)->exists()) {
            return response()->json([
                'message' => 'Product already associated with this supplier'
            ], 409);
        }

        $supplier->products()->attach($request->product_id, [
            'cost_price' => $request->cost_price,
            'lead_time' => $request->lead_time,
        ]);

        return response()->json([
            'message' => 'Product linked to supplier successfully',
            'supplier' => $supplier->load('products')
        ]);
    }

    /**
     * Link a command to a supplier
     */
    public function attachCommand(Request $request, $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'command_id' => 'required|exists:commands,id',
            'quantity_ordered' => 'required|integer|min:1',
            'order_date' => 'required|date',
            'expected_delivery' => 'required|date|after:order_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if command is already attached
        if ($supplier->commands()->where('command_id', $request->command_id)->exists()) {
            return response()->json([
                'message' => 'Command already associated with this supplier'
            ], 409);
        }

        $supplier->commands()->attach($request->command_id, [
            'quantity_ordered' => $request->quantity_ordered,
            'order_date' => $request->order_date,
            'expected_delivery' => $request->expected_delivery,
        ]);

        return response()->json([
            'message' => 'Command linked to supplier successfully',
            'supplier' => $supplier->load('commands')
        ]);
    }

    /**
     * Detach a product from a supplier
     */
    public function detachProduct($id, $productId)
    {
        try {
            $supplier = Supplier::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        if (!$supplier->products()->where('product_id', $productId)->exists()) {
            return response()->json([
                'message' => 'Product not associated with this supplier'
            ], 404);
        }

        $supplier->products()->detach($productId);

        return response()->json([
            'message' => 'Product removed from supplier successfully'
        ]);
    }

   
}
