<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model 
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'sku', 
        'quantity', 
        'price', 
        'alert_threshold', 
        'category_id', 
        'warehouse_id'
    ];

    // --- Relationships ---

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the warehouse where the product is stored.
     */
    public function warehouse(): BelongsTo
    {
        // Explicitly defining 'warehouse_id' is good practice if it differs 
        // from convention, though 'warehouse_id' is the standard for Warehouse::class.
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * The commands (orders) that contain the product.
     * Includes pivot data for quantity and unit price at the time of order.
     */
    public function commands(): BelongsToMany
    {
        return $this->belongsToMany(Command::class)
                    ->withPivot('quantity', 'unit_price');
    }

    /**
     * Get the stock movements associated with this product.
     */
    public function movements(): HasMany
    {
        // Keeping Mouvement as per your original code (French spelling)
        return $this->hasMany(Mouvement::class);
    }

    /**
     * Get the alerts (low stock, etc.) for this product.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Get the suppliers who provide this product.
     */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)
                    ->withPivot('cost_price', 'lead_time');
    }
}