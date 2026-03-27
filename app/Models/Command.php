<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $fillable = ['status', 'ordered_at', 'total_cost', 'client_id'];

    public function products() {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }
}
