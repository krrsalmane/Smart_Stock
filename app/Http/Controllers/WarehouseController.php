<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    // Get warehouses (magasinier sees only their own warehouse, admin sees all)
    public function index()
    {
        $user = Auth::user();

        // If magasinier, show only their warehouse
        if ($user->role === 'magasinier') {
            $warehouses = Warehouse::where('user_id', $user->id)->with('products', 'magasinier')->get();
        } else {
            // Admin sees all warehouses
            $warehouses = Warehouse::with('products', 'magasinier')->get();
        }

        return response()->json([
            'message' => 'Warehouses retrieved successfully',
            'warehouses' => $warehouses
        ]);
    }

    // Create a new warehouse (admin only, or assign to magasinier)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'user_id' => 'sometimes|exists:users,id', // Optional, for admin to assign a magasinier
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // If user_id not provided, assign to current magasinier
        if (!$request->has('user_id')) {
            $request->merge(['user_id' => Auth::id()]);
        }

        $warehouse = Warehouse::create($request->all());

        return response()->json([
            'message' => 'Warehouse created successfully',
            'warehouse' => $warehouse->load('products', 'magasinier')
        ], 201);
    }

    // Get a specific warehouse (magasinier can only view their own)
    public function show($id)
    {
        $warehouse = Warehouse::with('products', 'magasinier')->find($id);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        // Check access control: magasinier can only view their own warehouse
        $user = Auth::user();
        if ($user->role === 'magasinier' && $warehouse->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have access to this warehouse'], 403);
        }

        return response()->json([
            'message' => 'Warehouse retrieved successfully',
            'warehouse' => $warehouse
        ]);
    }

    // Update a warehouse (magasinier can only update their own)
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        // Check access control: magasinier can only update their own warehouse
        $user = Auth::user();
        if ($user->role === 'magasinier' && $warehouse->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have permission to update this warehouse'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $warehouse->update($request->all());

        return response()->json([
            'message' => 'Warehouse updated successfully',
            'warehouse' => $warehouse->load('products', 'magasinier')
        ]);
    }
}
