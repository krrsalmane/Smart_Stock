<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Command extends Model
{
    protected $fillable = ['status', 'command_type', 'ordered_at', 'expected_at', 'total_cost', 'client_id', 'notes', 'cancelled_at', 'cancellation_reason'];

    protected $casts = [
        'ordered_at' => 'datetime',
        'expected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity', 'unit_price');
    }

    public function client(): BelongsTo {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the suppliers this command is sent to.
     */
    public function suppliers(): BelongsToMany {
        return $this->belongsToMany(Supplier::class)
                    ->withPivot('quantity_ordered', 'order_date', 'expected_delivery', 'status', 'shipped_at', 'delivered_at', 'delivery_date', 'tracking_number', 'notes')
                    ->withTimestamps();
    }

    /**
     * Cancel this command
     */
    public function cancel($reason = null)
    {
        if ($this->status !== 'pending' && $this->status !== 'approved') {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Check if command can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    /**
     * Get tracking information from all suppliers
     */
    public function getTrackingStatus()
    {
        $tracking = [];
        foreach ($this->suppliers as $supplier) {
            $pivot = $supplier->pivot;
            $tracking[] = [
                'supplier' => $supplier->name,
                'status' => $pivot->status,
                'tracking_number' => $pivot->tracking_number,
                'shipped_at' => $pivot->shipped_at,
                'delivered_at' => $pivot->delivered_at,
                'delivery_date' => $pivot->delivery_date,
            ];
        }
        return $tracking;
    }

    /**
     * Check if order has been delivered by all suppliers
     */
    public function isFullyDelivered(): bool
    {
        if ($this->suppliers->isEmpty()) {
            return false;
        }

        foreach ($this->suppliers as $supplier) {
            $pivot = $supplier->pivot;
            if ($pivot->status !== 'delivered') {
                return false;
            }
        }

        return true;
    }

    /**
     * Get overall order status
     */
    public function getOverallStatus()
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        if ($this->suppliers->isEmpty()) {
            return $this->status;
        }

        $statuses = $this->suppliers->map(function ($supplier) {
            return $supplier->pivot->status;
        })->toArray();

        if (in_array('pending', $statuses)) return 'pending';
        if (in_array('shipped', $statuses)) return 'in_transit';
        if (count(array_unique($statuses)) === 1 && $statuses[0] === 'delivered') return 'delivered';

        return 'mixed';
    }
}
