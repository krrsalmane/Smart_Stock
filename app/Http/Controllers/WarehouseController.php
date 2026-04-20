<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    /**
     * Helper: append computed fields (isFull, currentStock, usagePercent) to warehouse data.
     */
    private function withComputedFields(Warehouse $warehouse): array
    {
        $data = $warehouse->toArray();
        $data['current_stock'] = $warehouse->currentStock();
        $data['is_full']       = $warehouse->isFull();
        $data['usage_percent'] = $warehouse->usagePercent();
        return $data;
    }

    // Get warehouses (magasinier sees only their own, admin sees all)
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'magasinier') {
            $warehouses = Warehouse::where('user_id', $user->id)->with('products', 'magasinier')->get();
        } else {
            $warehouses = Warehouse::with('products', 'magasinier')->get();
        }

        $result = $warehouses->map(fn($w) => $this->withComputedFields($w));

        return response()->json($result);
    }

    // Create a new warehouse
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'address'  => 'required|string|max:255',
            'capacity' => 'sometimes|integer|min:1',
            'user_id'  => 'sometimes|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$request->has('user_id')) {
            $request->merge(['user_id' => Auth::id()]);
        }

        $warehouse = Warehouse::create($request->all());
        $warehouse->load('products', 'magasinier');

        return response()->json([
            'message'   => 'Warehouse created successfully',
            'warehouse' => $this->withComputedFields($warehouse),
        ], 201);
    }

    // Get a specific warehouse
    public function show($id)
    {
        $warehouse = Warehouse::with('products', 'magasinier')->find($id);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        $user = Auth::user();
        if ($user->role === 'magasinier' && $warehouse->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have access to this warehouse'], 403);
        }

        return response()->json([
            'message'   => 'Warehouse retrieved successfully',
            'warehouse' => $this->withComputedFields($warehouse),
        ]);
    }

    // Update a warehouse
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        $user = Auth::user();
        if ($user->role === 'magasinier' && $warehouse->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have permission to update this warehouse'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:255',
            'address'  => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:1',
            'user_id'  => 'sometimes|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $warehouse->update($request->all());
        $warehouse->load('products', 'magasinier');

        return response()->json([
            'message'   => 'Warehouse updated successfully',
            'warehouse' => $this->withComputedFields($warehouse),
        ]);
    }

    // Delete a warehouse (blocked if products are stored inside)
    public function destroy($id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json(['error' => 'Warehouse not found'], 404);
        }

        if ($warehouse->products()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete warehouse with stored products. Reassign or remove products first.'
            ], 409);
        }

        $warehouse->delete();

        return response()->json(['message' => 'Warehouse deleted successfully']);
    }
}
