<?php

namespace App\Models;

 use Illuminate\Database\Eloquent\Model;

class Mouvement extends Model
{
    protected $fillable = ['type', 'quantity', 'note', 'product_id', 'command_id', 'user_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function command()
    {
        return $this->belongsTo(Command::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}