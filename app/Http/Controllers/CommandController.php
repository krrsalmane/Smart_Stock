<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommandController extends Controller
{
    // Get all commands
    public function index()
    {
        $user = auth()->user();
        
        // If client, show only their commands
        if ($user->role === 'client') {
            $commands = Command::where('client_id', $user->id)->with(['client', 'products'])->get();
        } else {
            // Admin/magasinier sees all commands
            $commands = Command::with(['client', 'products'])->get();
        }
        
        return response()->json($commands);
    }

    // Create a new command
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command_type' => 'sometimes|string|max:50',
            'ordered_at'   => 'required|date',
            'expected_at'  => 'sometimes|date|after_or_equal:ordered_at',
            'client_id'    => 'required|exists:users,id',
            'products'              => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity'   => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Calculate total cost
        $total_cost = 0;
        foreach ($request->products as $product) {
            $total_cost += $product['quantity'] * $product['unit_price'];
        }

        // Create command
        $command = Command::create([
            'status'       => 'pending',
            'command_type' => $request->command_type ?? 'purchase',
            'ordered_at'   => $request->ordered_at,
            'expected_at'  => $request->expected_at,
            'total_cost'   => $total_cost,
            'client_id'    => $request->client_id,
        ]);

        // Attach products to command
        foreach ($request->products as $product) {
            $command->products()->attach($product['product_id'], [
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
            ]);
        }

        return response()->json([
            'message' => 'Command created successfully',
            'command' => $command->load(['client', 'products'])
        ], 201);
    }

    // Get a specific command
    public function show($id)
    {
        $command = Command::with(['client', 'products'])->find($id);

        if (!$command) {
            return response()->json(['error' => 'Command not found'], 404);
        }

        return response()->json($command);
    }

    // Update command status
    public function update(Request $request, $id)
    {
        $command = Command::find($id);

        if (!$command) {
            return response()->json(['error' => 'Command not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,approved,received,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $command->update($request->all());

        return response()->json([
            'message' => 'Command updated successfully',
            'command' => $command->load(['client', 'products'])
        ]);
    }

    /**
     * Cancel a command (only if status is pending or approved)
     */
    public function cancel(Request $request, $id)
    {
        $command = Command::find($id);

        if (!$command) {
            return response()->json(['error' => 'Command not found'], 404);
        }

        // Check authorization - only client or admin can cancel
        $user = auth()->user();
        if ($user->role !== 'admin' && $command->client_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$command->canBeCancelled()) {
            return response()->json([
                'error' => 'Command cannot be cancelled',
                'current_status' => $command->status
            ], 422);
        }

        $reason = $request->input('reason');
        $command->cancel($reason);

        return response()->json([
            'message' => 'Command cancelled successfully',
            'command' => $command->load(['client', 'products'])
        ]);
    }

    /**
     * Get tracking status of a command from all suppliers
     */
    public function tracking($id)
    {
        $command = Command::with(['client', 'suppliers', 'products'])->find($id);

        if (!$command) {
            return response()->json(['error' => 'Command not found'], 404);
        }

        // Check authorization
        $user = auth()->user();
        if ($user->role === 'client' && $command->client_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'message' => 'Tracking information retrieved',
            'command' => [
                'id' => $command->id,
                'status' => $command->status,
                'overall_status' => $command->getOverallStatus(),
                'is_fully_delivered' => $command->isFullyDelivered(),
                'ordered_at' => $command->ordered_at,
                'expected_at' => $command->expected_at,
                'total_cost' => $command->total_cost,
                'products_count' => $command->products()->count(),
            ],
            'tracking_details' => $command->getTrackingStatus()
        ]);
    }
}
