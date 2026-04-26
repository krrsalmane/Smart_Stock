<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Product;
use App\Services\ArchiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Archive::with(['product', 'user']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $archives = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Archives retrieved',
            'archives' => $archives,
            'total' => $archives->count()
        ]);
    }

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'action' => 'sometimes|string|max:50',
            'notes' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product = Product::find($request->product_id);
        
        $archive = ArchiveService::snapshot(
            $product, 
            $request->action ?? Archive::ACTION_MANUAL,
            $request->notes
        );

        return response()->json([
            'message' => 'Product snapshot archived successfully',
            'archive' => $archive
        ], 201);
    }

    /**
     * Get historical inventory value at a specific date
     */
    public function getHistoricalValue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $date = new \DateTime($request->date);
        $historicalData = ArchiveService::getInventoryValueAtDate($date);

        return response()->json([
            'message' => 'Historical inventory value retrieved',
            'data' => $historicalData
        ]);
    }

    /**
     * Get inventory changes over a time period
     */
    public function getChanges(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $startDate = new \DateTime($request->start_date);
        $endDate = new \DateTime($request->end_date);
        $changes = ArchiveService::getInventoryChanges($startDate, $endDate);

        return response()->json([
            'message' => 'Inventory changes retrieved',
            'data' => $changes
        ]);
    }

    /**
     * Get archive statistics
     */
    public function getStatistics()
    {
        $totalArchives = Archive::count();
        $archivesByAction = Archive::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->get();
        
        $recentArchives = Archive::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $productsArchived = Archive::distinct('product_id')->count('product_id');

        return response()->json([
            'message' => 'Archive statistics retrieved',
            'statistics' => [
                'total_archives' => $totalArchives,
                'products_archived' => $productsArchived,
                'by_action' => $archivesByAction,
                'recent_archives' => $recentArchives,
            ]
        ]);
    }

    /**
     * Clean up old archives
     */
    public function cleanup(Request $request)
    {
        $keepCount = $request->input('keep_count', 50);
        
        $deleted = ArchiveService::cleanupOldArchives($keepCount);

        return response()->json([
            'message' => "Cleaned up {$deleted} old archives",
            'deleted_count' => $deleted
        ]);
    }
}
