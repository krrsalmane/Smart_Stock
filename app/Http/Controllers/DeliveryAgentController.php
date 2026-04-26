<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\Mouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeliveryAgentController extends Controller
{
    /**
     * Get all deliveries assigned to the current delivery agent
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get commands assigned to this delivery agent
        $commands = Command::where('delivery_agent_id', $user->id)
            ->with(['client', 'products', 'suppliers'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'message' => 'Deliveries retrieved successfully',
            'deliveries' => $commands
        ]);
    }

    /**
     * Get a specific delivery details
     */
    public function show($id)
    {
        try {
            $command = Command::with(['client', 'products', 'suppliers'])
                ->findOrFail($id);
            
            // Verify this delivery is assigned to the current agent
            if ((int)$command->delivery_agent_id !== (int)auth()->id()) {
                return response()->json(['error' => 'Unauthorized - This delivery is not assigned to you'], 403);
            }
            
            return response()->json([
                'message' => 'Delivery retrieved successfully',
                'delivery' => $command
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Delivery not found'], 404);
        }
    }

    /**
     * Start a delivery (change status to in_transit)
     */
    public function startDelivery($id)
    {
        try {
            $command = Command::findOrFail($id);
            
            // Verify assignment
            if ((int)$command->delivery_agent_id !== (int)auth()->id()) {
                return response()->json(['error' => 'Unauthorized - This delivery is not assigned to you'], 403);
            }
            
            if (!in_array($command->status, ['approved', 'pending'])) {
                return response()->json([
                    'error' => 'Cannot start delivery',
                    'current_status' => $command->status
                ], 422);
            }
            
            $command->update([
                'status' => 'in_transit',
                'delivery_started_at' => now(),
            ]);
            
            return response()->json([
                'message' => 'Delivery started successfully',
                'delivery' => $command->load(['client', 'products'])
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Delivery not found'], 404);
        }
    }

    /**
     * Update delivery status and location
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $command = Command::findOrFail($id);
            
            // Verify assignment
            if ((int)$command->delivery_agent_id !== (int)auth()->id()) {
                return response()->json(['error' => 'Unauthorized - This delivery is not assigned to you'], 403);
            }
            
            $validator = Validator::make($request->all(), [
                'status' => 'sometimes|in:in_transit,delayed,delivered',
                'current_location' => 'sometimes|string|max:255',
                'notes' => 'sometimes|string|max:500',
            ]);
            
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            
            $updateData = $request->only(['status', 'current_location', 'notes']);
            $command->update($updateData);
            
            return response()->json([
                'message' => 'Delivery status updated successfully',
                'delivery' => $command->load(['client', 'products'])
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Delivery not found'], 404);
        }
    }

    /**
     * Complete delivery - creates stock movements and marks as delivered
     */
    public function completeDelivery(Request $request, $id)
    {
        try {
            $command = Command::findOrFail($id);
            
            // Verify assignment
            if ((int)$command->delivery_agent_id !== (int)auth()->id()) {
                return response()->json(['error' => 'Unauthorized - This delivery is not assigned to you'], 403);
            }
            
            if ($command->status !== 'in_transit') {
                return response()->json([
                    'error' => 'Cannot complete delivery',
                    'current_status' => $command->status,
                    'message' => 'Delivery must be in transit before completion'
                ], 422);
            }
            
            $validator = Validator::make($request->all(), [
                'notes' => 'sometimes|string|max:500',
                'delivery_location' => 'sometimes|string|max:255',
            ]);
            
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            
            // Update command status
            $command->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'delivery_location' => $request->input('delivery_location'),
                'notes' => $request->input('notes', $command->notes),
            ]);
            
            // Create stock OUT movements for each product in the command
            foreach ($command->products as $product) {
                $pivot = $product->pivot;
                $quantity = $pivot->quantity;
                
                // Create movement record
                Mouvement::create([
                    'type' => 'OUT',
                    'quantity' => $quantity,
                    'note' => 'Delivery completed for command #' . $command->id . ' - ' . ($request->input('notes') ?? 'Product received'),
                    'product_id' => $product->id,
                    'command_id' => $command->id,
                    'user_id' => auth()->id(),
                ]);
                
                // Update product quantity
                $product->decrement('quantity', $quantity);
            }
            
            return response()->json([
                'message' => 'Delivery completed successfully - Stock movements created',
                'delivery' => $command->load(['client', 'products'])
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Delivery not found'], 404);
        }
    }

    /**
     * Create or update a movement for a delivery (e.g., IN_TRANSIT, OUT_FOR_DELIVERY, DELIVERED)
     * This allows the delivery agent to log the physical transit status
     */
    public function updateMouvement(Request $request, $commandId)
    {
        try {
            $command = Command::findOrFail($commandId);
            
            // Verify assignment
            if ((int)$command->delivery_agent_id !== (int)auth()->id()) {
                return response()->json(['error' => 'Unauthorized - This delivery is not assigned to you'], 403);
            }
            
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:IN_TRANSIT,OUT_FOR_DELIVERY,DELIVERED,RETURNED',
                'product_id' => 'sometimes|exists:products,id',
                'quantity' => 'sometimes|integer|min:1',
                'note' => 'required|string|max:500',
                'current_location' => 'sometimes|string|max:255',
            ]);
            
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            
            // Update command's current location if provided
            if ($request->has('current_location')) {
                $command->update(['current_location' => $request->current_location]);
            }
            
            // If product_id and quantity are provided, create movement for specific product
            if ($request->has('product_id') && $request->has('quantity')) {
                $movement = Mouvement::create([
                    'type' => $request->type,
                    'quantity' => $request->quantity,
                    'note' => $request->note . ' - Delivery update for command #' . $command->id,
                    'product_id' => $request->product_id,
                    'command_id' => $command->id,
                    'user_id' => auth()->id(),
                ]);
                
                return response()->json([
                    'message' => 'Movement created successfully',
                    'movement' => $movement->load(['product', 'command'])
                ]);
            }
            
            // Otherwise, create movements for all products in the command
            $movements = [];
            foreach ($command->products as $product) {
                $pivot = $product->pivot;
                $quantity = $pivot->quantity;
                
                $movement = Mouvement::create([
                    'type' => $request->type,
                    'quantity' => $quantity,
                    'note' => $request->note . ' - Delivery update for command #' . $command->id,
                    'product_id' => $product->id,
                    'command_id' => $command->id,
                    'user_id' => auth()->id(),
                ]);
                
                $movements[] = $movement;
                
                // If DELIVERED, update product quantity
                if ($request->type === 'DELIVERED') {
                    $product->increment('quantity', $quantity);
                }
            }
            
            // If status is DELIVERED, update command status
            if ($request->type === 'DELIVERED') {
                $command->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                    'delivery_location' => $request->input('current_location'),
                ]);
            }
            
            return response()->json([
                'message' => 'Movement(s) created successfully',
                'movements' => $movements,
                'command_status' => $command->status
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Command not found'], 404);
        }
    }

    /**
     * Get all movements for a specific delivery
     */
    public function getMovements($commandId)
    {
        try {
            $command = Command::findOrFail($commandId);
            
            // Verify assignment (or admin access)
            $user = auth()->user();
            if ($user->role !== 'admin' && $command->delivery_agent_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $movements = Mouvement::where('command_id', $commandId)
                ->with(['product', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'message' => 'Movements retrieved successfully',
                'movements' => $movements
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Command not found'], 404);
        }
    }

    /**
     * Get available deliveries (not yet assigned to any agent)
     * Admin-only endpoint
     */
    public function getAvailableDeliveries()
    {
        $user = auth()->user();
        
        // Only admins can see all unassigned deliveries
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $commands = Command::whereNull('delivery_agent_id')
            ->whereIn('status', ['pending', 'approved'])
            ->with(['client', 'products'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'message' => 'Available deliveries retrieved',
            'deliveries' => $commands
        ]);
    }

    /**
     * Assign a delivery agent to a command (Admin-only)
     */
    public function assignDeliveryAgent(Request $request, $commandId, $agentId)
    {
        $user = auth()->user();
        
        // Only admins can assign
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $command = Command::findOrFail($commandId);
            
            // Verify the agent exists and has the correct role
            $agent = \App\Models\User::findOrFail($agentId);
            if ($agent->role !== 'delivery_agent') {
                return response()->json(['error' => 'User is not a delivery agent'], 422);
            }
            
            $command->update([
                'delivery_agent_id' => $agentId,
                'assigned_at' => now(),
            ]);
            
            return response()->json([
                'message' => 'Delivery agent assigned successfully',
                'delivery' => $command->load(['client', 'products'])
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Command or agent not found'], 404);
        }
    }
}
