<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierController extends Controller
{
    /**
     * Get all suppliers
     */
    public function index()
    {
        $suppliers = Supplier::with(['products', 'commands'])->get();

        return response()->json($suppliers);
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
        } catch (ModelNotFoundException $e) {
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
        $supplier = Supplier::find($id);
        if (!$supplier) {
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
        $supplier = Supplier::find($id);
        if (!$supplier) {
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
     * Assign products to a supplier
     */
    public function assignProducts(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
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

        // Check if product is already assigned
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
     * Assign a command to a supplier
     */
    public function assignCommand(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
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

        // Check if command is already assigned
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
     * Get commands assigned to a supplier
     */
    public function getCommands($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $commands = $supplier->commands()->with('products')->get();

        return response()->json([
            'message' => 'Commands retrieved successfully',
            'commands' => $commands
        ]);
    }

    /**
     * Get products supplied by a supplier
     */
    public function getProducts($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $products = $supplier->products()->with('commands')->get();

        return response()->json([
            'message' => 'Products retrieved successfully',
            'products' => $products
        ]);
    }

    /**
     * Update command status (for supplier portal)
     */
    public function updateCommandStatus(Request $request, $supplierId, $commandId)
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,confirmed,shipped,delivered,cancelled',
            'delivered_at' => 'sometimes|date',
            'shipped_at' => 'sometimes|date',
            'notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the pivot table
        $supplier->commands()->updateExistingPivot($commandId, $request->only(['status', 'delivered_at', 'shipped_at', 'notes']));

        return response()->json([
            'message' => 'Command status updated successfully'
        ]);
    }

    /**
     * Update command delivery info
     */
    public function updateCommandDelivery(Request $request, $supplierId, $commandId)
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'delivery_date' => 'required|date',
            'tracking_number' => 'sometimes|string',
            'notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update pivot to show delivery sent
        $updateData = [
            'status' => 'shipped',
            'shipped_at' => now(),
            'expected_delivery' => $request->delivery_date,
        ];

        if ($request->has('tracking_number')) {
            $updateData['tracking_number'] = $request->tracking_number;
        }
        if ($request->has('notes')) {
            $updateData['notes'] = $request->notes;
        }

        $supplier->commands()->updateExistingPivot($commandId, $updateData);

        return response()->json([
            'message' => 'Delivery sent successfully'
        ]);
    }

    /**
     * Confirm command shipment
     */
    public function confirmShipment(Request $request, $supplierId, $commandId)
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        // Update pivot to show delivery confirmed
        $supplier->commands()->updateExistingPivot($commandId, [
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        return response()->json([
            'message' => 'Delivery confirmed successfully'
        ]);
    }

    /**
     * Get supplier dashboard statistics
     */
    public function getDashboardStats($supplierId)
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $totalProducts = $supplier->products()->count();
        $totalCommands = $supplier->commands()->count();
        $totalDeliveredCommands = $supplier->commands()->where('status', 'delivered')->count();

        return response()->json([
            'total_products' => $totalProducts,
            'total_commands' => $totalCommands,
            'total_delivered_commands' => $totalDeliveredCommands,
        ]);
    }
}
