<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'address', 'capacity', 'user_id'];

    protected $table = 'warehouses';

    /**
     * A warehouse contains many products.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * A warehouse is managed by a Magasinier user.
     */
    public function magasinier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * FS4 / Class Diagram: isFull()
     * Returns true if the total product quantity meets or exceeds capacity.
     * Returns false if no capacity is defined (unlimited).
     */
    public function isFull(): bool
    {
        if (is_null($this->capacity) || $this->capacity <= 0) {
            return false; // No capacity limit set
        }
        $totalStock = $this->products()->sum('quantity');
        return $totalStock >= $this->capacity;
    }

    /**
     * Returns the current total stock count in the warehouse.
     */
    public function currentStock(): int
    {
        return (int) $this->products()->sum('quantity');
    }

    /**
     * Returns usage percentage (0-100).
     */
    public function usagePercent(): float
    {
        if (is_null($this->capacity) || $this->capacity <= 0) {
            return 0.0;
        }
        return min(100, round(($this->currentStock() / $this->capacity) * 100, 1));
    }
}
