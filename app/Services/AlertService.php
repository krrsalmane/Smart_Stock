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
        $alertType = null;

        if ($product->quantity === 0) {
            $alertType = 'OUT_OF_STOCK';
        } elseif ($product->quantity > 0 && $product->quantity <= $product->alert_threshold) {
            $alertType = 'LOW_STOCK';
        } elseif ($product->quantity > $product->alert_threshold && $product->quantity <= ($product->alert_threshold * 2)) {
            $alertType = 'ALMOST_LOW';
        }

        if ($alertType) {
            Alert::where('product_id', $product->id)
                 ->where('status', 'active')
                 ->whereIn('type', ['OUT_OF_STOCK', 'LOW_STOCK', 'ALMOST_LOW'])
                 ->where('type', '!=', $alertType)
                 ->update(['status' => 'resolved']);

            $existingAlert = Alert::where('product_id', $product->id)
                                  ->where('status', 'active')
                                  ->where('type', $alertType)
                                  ->first();

            if ($existingAlert) {
                $existingAlert->update([
                    'triggered_at' => now(),
                ]);
            } else {
                Alert::create([
                    'product_id' => $product->id,
                    'type' => $alertType,
                    'status' => 'active',
                    'triggered_at' => now(),
                ]);
            }
        } else {
            // If quantity is now above alert_threshold * 2, dismiss active stock alerts
            Alert::where('product_id', $product->id)
                 ->where('status', 'active')
                 ->whereIn('type', ['OUT_OF_STOCK', 'LOW_STOCK', 'ALMOST_LOW'])
                 ->update(['status' => 'resolved']);
        }
    }

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
