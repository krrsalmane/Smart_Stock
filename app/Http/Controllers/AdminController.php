<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Command;
use App\Models\Mouvement;
use App\Services\AlertService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get Admin Dashboard stats and overview
     */
    public function index()
    {
        // 1. Total inventory value
        // Assuming price is stored in product table
        $totalInventoryValue = Product::sum(DB::raw('price * quantity'));

        // 2. Pending commands count
        $pendingCommands = Command::where('status', 'pending')->count();

        // 3. Low stock alerts
        $lowStockAlertsCount = AlertService::getLowStockAlertsCount();
        $activeAlertsCount = AlertService::getActiveAlertsCount();

        // 4. Recent movements (last 10)
        $recentMouvements = Mouvement::with(['product', 'user'])
                                     ->orderBy('created_at', 'desc')
                                     ->take(10)
                                     ->get();

        return response()->json([
            'message' => 'Dashboard statistics retrieved successfully',
            'data' => [
                'total_inventory_value' => $totalInventoryValue,
                'pending_commands_count' => $pendingCommands,
                'low_stock_alerts_count' => $lowStockAlertsCount,
                'total_active_alerts' => $activeAlertsCount,
                'recent_mouvements' => $recentMouvements
            ]
        ]);
    }
}
