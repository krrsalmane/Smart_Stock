<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    // Get all warehouses
    public function index()
    {
        $warehouses = Warehouse::with('products')->get();
        return response()->json($warehouses);
    }

    // Create a new warehouse
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $warehouse = Warehouse::create($request->all());

        return response()->json([
            'message' => 'Warehouse created successfully',
            'warehouse' => $warehouse->load('products')
        ], 201);
    }

    // Get a specific warehouse
    public function show($id)
    {
        $warehouse = Warehouse::with('products')->find($id);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        return response()->json($warehouse);
    }

    // Update a warehouse
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $warehouse->update($request->all());

        return response()->json([
            'message' => 'Warehouse updated successfully',
            'warehouse' => $warehouse->load('products')
        ]);
    }
}
