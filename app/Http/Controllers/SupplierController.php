<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Get all suppliers
     */
    public function index()
    {
        $suppliers = Supplier::with(['products', 'commands'])->get();

        return response()->json([
            'message' => 'Suppliers retrieved successfully',
            'suppliers' => $suppliers
        ]);
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Supplier not found'
            ], 404);
        }
    }

  
  
   
}
