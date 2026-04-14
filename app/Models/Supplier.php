<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address'];

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
                    ->withPivot('quantity_ordered', 'order_date', 'expected_delivery');
    }
}
