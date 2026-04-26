<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    protected $fillable = [
        'quantity', 
        'product_id', 
        'user_id',
        'action',
        'notes',
        'snapshot_data'
    ];
    
    protected $casts = [
        'snapshot_data' => 'array',
    ];
    
    const UPDATED_AT = null;

    const ACTION_SNAPSHOT = 'snapshot';
    const ACTION_BEFORE_DELETE = 'before_delete';
    const ACTION_BEFORE_UPDATE = 'before_update';
    const ACTION_STOCK_IN = 'stock_in';
    const ACTION_STOCK_OUT = 'stock_out';
    const ACTION_MANUAL = 'manual';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute()
    {
        $labels = [
            self::ACTION_SNAPSHOT => 'Snapshot',
            self::ACTION_BEFORE_DELETE => 'Before Deletion',
            self::ACTION_BEFORE_UPDATE => 'Before Update',
            self::ACTION_STOCK_IN => 'Stock In',
            self::ACTION_STOCK_OUT => 'Stock Out',
            self::ACTION_MANUAL => 'Manual Archive',
        ];
        
        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Scope: Filter by action type
     */
    public function scopeWhereAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeWhereDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by specific product
     */
    public function scopeWhereProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Get snapshot of product name at time of archive
     */
    public function getProductNameAttribute()
    {
        if ($this->snapshot_data && isset($this->snapshot_data['name'])) {
            return $this->snapshot_data['name'];
        }
        return $this->product ? $this->product->name : 'Unknown Product';
    }

    /**
     * Get snapshot of product SKU at time of archive
     */
    public function getProductSkuAttribute()
    {
        if ($this->snapshot_data && isset($this->snapshot_data['sku'])) {
            return $this->snapshot_data['sku'];
        }
        return $this->product ? $this->product->sku : 'N/A';
    }
}
