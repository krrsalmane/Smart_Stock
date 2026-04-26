<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Command extends Model
{
    protected $fillable = ['status', 'command_type', 'ordered_at', 'expected_at', 'total_cost', 'client_id', 'notes', 'cancelled_at', 'cancellation_reason', 'delivery_agent_id', 'delivery_started_at', 'delivered_at', 'current_location', 'delivery_location', 'assigned_at'];

    protected $casts = [
        'ordered_at' => 'datetime',
        'expected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'delivery_started_at' => 'datetime',
        'delivered_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity', 'unit_price');
    }

    public function client(): BelongsTo {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the delivery agent assigned to this command.
     */
    public function deliveryAgent(): BelongsTo {
        return $this->belongsTo(User::class, 'delivery_agent_id');
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
        $supplierConfirmedAt = null;
        $supplierShippedAt = null;

        foreach ($this->suppliers as $supplier) {
            $pivot = $supplier->pivot;

            if (in_array($pivot->status, ['confirmed', 'shipped', 'delivered'])) {
                $confirmedAt = $pivot->updated_at ?? null;
                if ($confirmedAt && (!$supplierConfirmedAt || $confirmedAt < $supplierConfirmedAt)) {
                    $supplierConfirmedAt = $confirmedAt;
                }
            }

            if (in_array($pivot->status, ['shipped', 'delivered'])) {
                $shippedAt = $pivot->shipped_at ?? $pivot->updated_at ?? null;
                if ($shippedAt && (!$supplierShippedAt || $shippedAt < $supplierShippedAt)) {
                    $supplierShippedAt = $shippedAt;
                }
            }
        }

        $timeline = [
            [
                'label' => 'Order placed',
                'completed' => true,
                'timestamp' => $this->created_at,
            ],
            [
                'label' => 'Order approved',
                'completed' => $this->status !== 'pending',
                'timestamp' => $this->status !== 'pending' ? $this->updated_at : null,
            ],
            [
                'label' => 'Supplier confirmed',
                'completed' => $supplierConfirmedAt !== null,
                'timestamp' => $supplierConfirmedAt,
            ],
            [
                'label' => 'Shipped by supplier',
                'completed' => $supplierShippedAt !== null,
                'timestamp' => $supplierShippedAt,
            ],
            [
                'label' => 'Out for delivery',
                'completed' => in_array($this->status, ['in_transit', 'delayed', 'delivered']),
                'timestamp' => in_array($this->status, ['in_transit', 'delayed', 'delivered']) ? ($this->delivery_started_at ?? $this->updated_at) : null,
            ],
        ];

        if ($this->status === 'delayed') {
            $timeline[] = [
                'label' => 'Delivery delayed',
                'completed' => true,
                'timestamp' => $this->updated_at,
            ];
        } else {
            $timeline[] = [
                'label' => 'Delivered',
                'completed' => $this->status === 'delivered',
                'timestamp' => $this->status === 'delivered' ? ($this->delivered_at ?? $this->updated_at) : null,
            ];
        }

        return $timeline;
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
