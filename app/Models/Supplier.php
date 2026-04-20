<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'rating', 'status'];

    /**
     * Get the products that this supplier provides.
     * A supplier provides many products, and a product can be provided by many suppliers.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('cost_price', 'lead_time');
    }

    /**
     * Get the commands sent to this supplier.
     * A supplier receives many commands, and a command can be sent to many suppliers.
     */
    public function commands(): BelongsToMany
    {
        return $this->belongsToMany(Command::class)
                    ->withPivot('quantity_ordered', 'order_date', 'expected_delivery', 'status', 'shipped_at', 'delivered_at', 'delivery_date', 'tracking_number', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get pending orders from this supplier
     */
    public function pendingOrders()
    {
        return $this->commands()
                    ->wherePivot('status', 'pending')
                    ->orderByPivot('order_date', 'desc');
    }

    /**
     * Get shipped orders from this supplier
     */
    public function shippedOrders()
    {
        return $this->commands()
                    ->wherePivot('status', 'shipped')
                    ->orderByPivot('shipped_at', 'desc');
    }

    /**
     * Get delivered orders from this supplier
     */
    public function deliveredOrders()
    {
        return $this->commands()
                    ->wherePivot('status', 'delivered')
                    ->orderByPivot('delivered_at', 'desc');
    }

    /**
     * Get active orders (pending or shipped)
     */
    public function activeOrders()
    {
        return $this->commands()
                    ->whereIn('command_supplier.status', ['pending', 'shipped'])
                    ->orderByPivot('order_date', 'desc');
    }

    /**
     * Mark a command as shipped
     */
    public function shipCommand($commandId, $trackingNumber = null, $notes = null)
    {
        return $this->commands()->updateExistingPivot($commandId, [
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_number' => $trackingNumber,
            'notes' => $notes,
        ]);
    }

    /**
     * Mark a command as delivered
     */
    public function confirmDelivery($commandId, $deliveryDate = null, $notes = null)
    {
        return $this->commands()->updateExistingPivot($commandId, [
            'status' => 'delivered',
            'delivered_at' => $deliveryDate ?? now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Get supplier performance metrics
     */
    public function getPerformanceMetrics()
    {
        $totalOrders = $this->commands()->count();
        $deliveredOrders = $this->deliveredOrders()->count();
        $deliveryRate = $totalOrders > 0 ? ($deliveredOrders / $totalOrders) * 100 : 0;

        return [
            'total_orders' => $totalOrders,
            'delivered_orders' => $deliveredOrders,
            'pending_orders' => $this->pendingOrders()->count(),
            'shipped_orders' => $this->shippedOrders()->count(),
            'delivery_rate' => round($deliveryRate, 2),
            'rating' => $this->rating ?? 0,
        ];
    }

    /**
     * Get average lead time in days
     */
    public function getAverageLeadTime()
    {
        return $this->products()
                    ->avg('product_supplier.lead_time') ?? 0;
    }

    /**
     * Check if supplier is active/reliable
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->rating >= 3;
    }
}
