<?php

namespace App\Http\Controllers;

use App\Models\Mouvement;
use App\Models\Product;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MouvementController extends Controller
{
    // Get all mouvements
    public function index()
    {
        $mouvements = Mouvement::with(['product', 'command', 'user'])->get();
        return response()->json($mouvements);
    }

    // Create a new mouvement
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:IN,OUT,ADJ',
            'quantity' => 'required|integer|min:1',
            'note' => 'sometimes|string|max:255',
            'product_id' => 'required|exists:products,id',
            'command_id' => 'sometimes|exists:commands,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $mouvement = Mouvement::create($request->all());

        // Update product quantity based on movement type
        $product = Product::find($request->product_id);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
        if ($request->type === 'IN') {
            $product->increment('quantity', $request->quantity);
        } elseif ($request->type === 'OUT') {
            $product->decrement('quantity', $request->quantity);
        } elseif ($request->type === 'ADJ') {
            // For adjustment, set the quantity directly if provided
            if ($request->has('new_quantity')) {
                $product->quantity = $request->new_quantity;
                $product->save();
            }
            AlertService::createAdjustmentAlert($product);
        }

        // Refresh product data
        $product->refresh();

        // Check stock levels and trigger alerts
        AlertService::checkStockLevels($product);

        return response()->json([
            'message' => 'Mouvement recorded successfully',
            'mouvement' => $mouvement->load(['product', 'command', 'user']),
            'product' => $product
        ], 201);
    }

    // Get a specific mouvement
    public function show($id)
    {
        $mouvement = Mouvement::with(['product', 'command', 'user'])->find($id);

        if (!$mouvement) {
            return response()->json(['error' => 'Mouvement not found'], 404);
        }

        return response()->json($mouvement);
    }

    // Update a mouvement
    public function update(Request $request, $id)
    {
        $mouvement = Mouvement::find($id);

        if (!$mouvement) {
            return response()->json(['error' => 'Mouvement not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|in:IN,OUT,ADJ',
            'quantity' => 'sometimes|integer|min:1',
            'note' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $mouvement->update($request->all());

        return response()->json([
            'message' => 'Mouvement updated successfully',
            'mouvement' => $mouvement->load(['product', 'command', 'user'])
        ]);
    }
}

