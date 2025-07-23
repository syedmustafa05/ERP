<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Requisition extends Model
{
    protected $fillable = [
        'item',
        'quantity',
        'status',
        'requestedBy',
        'date'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
