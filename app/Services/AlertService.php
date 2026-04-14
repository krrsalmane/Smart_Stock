<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Product;

class AlertService
{
    /**
     * Check product quantity and create alert if below threshold
     */
    public static function checkStockLevels(Product $product)
    {
        // Check if product quantity is below alert threshold
        if ($product->quantity < $product->alert_threshold) {
            // Check if alert already exists for this product (active)
            $existingAlert = Alert::where('product_id', $product->id)
                                   ->where('status', 'active')
                                   ->where('type', 'LOW_STOCK')
                                   ->first();

            // Only create if no active alert exists
            if (!$existingAlert) {
                Alert::create([
                    'product_id' => $product->id,
                    'type' => 'LOW_STOCK',
                    'status' => 'active',
                    'triggered_at' => now(),
                ]);
            }
        } else {
            // If quantity is now above threshold, dismiss any active LOW_STOCK alerts
            Alert::where('product_id', $product->id)
                 ->where('status', 'active')
                 ->where('type', 'LOW_STOCK')
                 ->update(['status' => 'resolved']);
        }
    }

    /**
     * Create alert for inventory discrepancy
     */
    public static function createDiscrepancyAlert(Product $product, $expected, $actual)
    {
        Alert::create([
            'product_id' => $product->id,
            'type' => 'DISCREPANCY',
            'status' => 'active',
            'triggered_at' => now(),
        ]);
    }

    /**
     * Create alert for product adjustment
     */
    public static function createAdjustmentAlert(Product $product)
    {
        Alert::create([
            'product_id' => $product->id,
            'type' => 'ADJUSTMENT',
            'status' => 'active',
            'triggered_at' => now(),
        ]);
    }

    /**
     * Get active alerts count
     */
    public static function getActiveAlertsCount()
    {
        return Alert::where('status', 'active')->count();
    }

    /**
     * Get low stock alerts count
     */
    public static function getLowStockAlertsCount()
    {
        return Alert::where('status', 'active')
                    ->where('type', 'LOW_STOCK')
                    ->count();
    }

    /**
     * Dismiss all alerts for a product
     */
    public static function dismissProductAlerts(Product $product)
    {
        Alert::where('product_id', $product->id)
             ->where('status', 'active')
             ->update(['status' => 'dismissed']);
    }
}
