<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AlertController extends Controller
{
    /**
     * Get all alerts with product details
     */
    public function index(Request $request)
    {
        $query = Alert::with('product');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by product_id
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $alerts = $query->orderBy('triggered_at', 'desc')->get();

        return response()->json($alerts);
    }

    /**
     * Get active alerts count
     */
    public function getActiveCount()
    {
        $count = Alert::where('status', 'active')->count();

        return response()->json([
            'active_alerts' => $count
        ]);
    }

    /**
     * Get low stock alerts
     */
    public function getLowStockAlerts()
    {
        $alerts = Alert::where('type', 'LOW_STOCK')
                       ->where('status', 'active')
                       ->with('product')
                       ->orderBy('triggered_at', 'desc')
                       ->get();

        return response()->json($alerts);
    }

    /**
     * Get a specific alert
     */
    public function show($id)
    {
        try {
            $alert = Alert::with('product')->findOrFail($id);

            return response()->json([
                'message' => 'Alert retrieved successfully',
                'alert' => $alert
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Alert not found'
            ], 404);
        }
    }

    /**
     * Update alert status (mark as read/dismissed)
     */
    public function update(Request $request, $id)
    {
        $alert = Alert::find($id);
        if (!$alert) {
            return response()->json([
                'message' => 'Alert not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:active,dismissed,resolved',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $alert->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Alert updated successfully',
            'alert' => $alert
        ]);
    }

    /**
     * Delete an alert
     */
    public function destroy($id)
    {
        $alert = Alert::find($id);
        if (!$alert) {
            return response()->json([
                'message' => 'Alert not found'
            ], 404);
        }

        $alert->delete();

        return response()->json([
            'message' => 'Alert deleted successfully'
        ]);
    }
}
