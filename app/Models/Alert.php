<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = ['status', 'triggered_at', 'type', 'product_id'];

    // An alert belongs to one specific product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function dismiss()
    {
        $this->update(['status' => 'dismissed']);
    }

    public function notifyAdmin()
    {
        // Simple log for now, can be expanded to email/notification later
        \Illuminate\Support\Facades\Log::info("Admin notified for Alert ID: " . $this->id);
    }
}
