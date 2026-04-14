<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Command extends Model
{
    protected $fillable = ['status', 'ordered_at', 'total_cost', 'client_id'];

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
                    ->withPivot('quantity_ordered', 'order_date', 'expected_delivery');
    }
}
