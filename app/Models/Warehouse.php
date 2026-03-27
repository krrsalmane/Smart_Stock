<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'address'];

    // According to your diagram, a warehouse "contains" products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    // It also has a Magasinier who "runs" it
    public function magasinier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected $table = 'warehouses'; // Explicitly naming it to avoid 'warhouses' typos
    
    
}
