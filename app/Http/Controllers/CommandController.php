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
        $commands = Command::with(['client', 'products'])->get();
        return response()->json($commands);
    }

    // Create a new command
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ordered_at' => 'required|date',
            'client_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
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
            'status' => 'pending',
            'ordered_at' => $request->ordered_at,
            'total_cost' => $total_cost,
            'client_id' => $request->client_id,
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
   
}
