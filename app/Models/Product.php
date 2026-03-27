<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    public function commands() {
        return $this->belongsToMany(Command::class)
                    ->withPivot('quantity', 'unit_price');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
