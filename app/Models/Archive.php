<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{

protected $fillable = ['quantity', 'product_id', 'user_id'];
    
    
    const UPDATED_AT = null;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
