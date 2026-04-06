<?php

namespace App\Http\Controllers;

use App\Models\Mouvement;
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

        return response()->json([
            'message' => 'Mouvement recorded successfully',
            'mouvement' => $mouvement->load(['product', 'command', 'user'])
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
   
}
