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
  

   
}
