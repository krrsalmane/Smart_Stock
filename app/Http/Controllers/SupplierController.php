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
     * Detach product from supplier
     */
    public function detachProduct(Request $request, $id, $productId = null)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $targetProductId = $productId ?? $request->input('product_id');
        if (!$targetProductId) {
            return response()->json([
                'message' => 'Product ID is required'
            ], 422);
        }

        $supplier->products()->detach($targetProductId);

        return response()->json([
            'message' => 'Product unlinked from supplier successfully',
            'supplier' => $supplier->load('products')
        ]);
    }

    /**
     * Detach command from supplier
     */
    public function detachCommand(Request $request, $id, $commandId = null)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $targetCommandId = $commandId ?? $request->input('command_id');
        if (!$targetCommandId) {
            return response()->json([
                'message' => 'Command ID is required'
            ], 422);
        }

        $supplier->commands()->detach($targetCommandId);

        return response()->json([
            'message' => 'Command unlinked from supplier successfully',
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
     * Get commands for currently authenticated supplier
     */
    public function receiveCommand()
    {
        $user = auth()->user();
        $supplier = Supplier::where('email', $user->email)->first();

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $commands = $supplier->commands()
            ->with(['client', 'products'])
            ->orderBy('commands.created_at', 'desc')
            ->get();

        $formatted = $commands->map(function ($command) {
            return [
                'command_id' => $command->id,
                'command_type' => $command->command_type,
                'client' => $command->client ? $command->client->name : 'N/A',
                'total_cost' => $command->total_cost,
                'ordered_at' => $command->ordered_at,
                'expected_at' => $command->expected_at,
                'order_date' => $command->pivot->order_date,
                'expected_delivery' => $command->pivot->expected_delivery,
                'delivery_status' => $command->pivot->status ?? 'pending',
                'shipped_at' => $command->pivot->shipped_at,
                'delivered_at' => $command->pivot->delivered_at,
                'can_decide' => ($command->pivot->status ?? 'pending') === 'pending',
            ];
        });

        return response()->json([
            'message' => 'Commands retrieved successfully',
            'commands' => $formatted
        ]);
    }

    /**
     * Accept or decline assigned command for currently authenticated supplier
     */
    public function decideCommand(Request $request, $commandId)
    {
        $user = auth()->user();
        $supplier = Supplier::where('email', $user->email)->first();

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'decision' => 'required|in:accept,decline',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $command = $supplier->commands()->where('command_id', $commandId)->first();
        if (!$command) {
            return response()->json([
                'message' => 'Command not found for this supplier'
            ], 404);
        }

        $currentStatus = $command->pivot->status ?? 'pending';
        if ($currentStatus !== 'pending') {
            return response()->json([
                'message' => 'This command has already been processed'
            ], 422);
        }

        $newStatus = $request->decision === 'accept' ? 'confirmed' : 'cancelled';

        $supplier->commands()->updateExistingPivot($commandId, [
            'status' => $newStatus,
            'notes' => $request->decision === 'accept'
                ? 'Accepted by supplier'
                : 'Declined by supplier',
        ]);

        return response()->json([
            'message' => $request->decision === 'accept'
                ? 'Command accepted successfully'
                : 'Command declined successfully'
        ]);
    }

    /**
     * Mark command as shipped for currently authenticated supplier
     */
    public function sendDelivery(Request $request, $commandId)
    {
        $user = auth()->user();
        $supplier = Supplier::where('email', $user->email)->first();

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }

        if (!$supplier->commands()->where('command_id', $commandId)->exists()) {
            return response()->json([
                'message' => 'Command not found for this supplier'
            ], 404);
        }

        $command = $supplier->commands()->where('command_id', $commandId)->first();
        if (!$command) {
            return response()->json([
                'message' => 'Command not found for this supplier'
            ], 404);
        }

        if (($command->pivot->status ?? 'pending') !== 'confirmed') {
            return response()->json([
                'message' => 'Only accepted commands can be shipped'
            ], 422);
        }

        $supplier->commands()->updateExistingPivot($commandId, [
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);

        return response()->json([
            'message' => 'Delivery sent successfully'
        ]);
    }
}
