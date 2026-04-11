<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address'];

    /**
     * Get the products that this supplier provides.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('cost_price', 'lead_time')
                    ->withTimestamps();
    }

    /**
     * Get the commands sent to this supplier.
     */
    public function commands(): BelongsToMany
    {
        return $this->belongsToMany(Command::class)
                    ->withPivot('quantity_ordered', 'order_date', 'expected_delivery')
                    ->withTimestamps();
    }
}
