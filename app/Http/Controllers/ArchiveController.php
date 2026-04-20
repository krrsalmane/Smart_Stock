<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArchiveController extends Controller
{
    /**
     * Get all archived records
     */
    public function index(Request $request)
    {
        $query = Archive::with(['product', 'user']);

        // Implement methods requested by class diagram
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('date')) {
            // Get by specific date
            $query->whereDate('created_at', $request->date);
        }

        $archives = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Archives retrieved',
            'archives' => $archives,
            'total' => $archives->count()
        ]);
    }

    /**
     * Get specific archive details
     */
    public function show($id)
    {
        try {
            $archive = Archive::with(['product', 'user'])->findOrFail($id);
            return response()->json([
                'message' => 'Archive detail retrieved',
                'archive' => $archive
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Archive not found'], 404);
        }
    }

    /**
     * Archive product snapshot
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // We assume user_id comes from authenticated user in a real scenario
        // but for now we accept it or fallback
        $userId = $request->user_id ?? auth()->id() ?? 1; // Fallback to 1 if not provided

        $archive = Archive::create([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'user_id' => $userId,
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Product snapshot archived successfully',
            'archive' => $archive
        ], 201);
    }
}
