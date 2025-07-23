<?php

namespace App\Models;

/**
 * Vendor Model
 */
class Vendor extends BaseModel
{
    protected $table = 'vendors';
    
    protected $fillable = [
        'name',
        'contact',
        'email',
        'phone',
        'address',
        'is_active',
        'rating',
    ];
    
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'float',
    ];
    
    /**
     * Get active vendors
     */
    public static function active()
    {
        return static::where('is_active', true);
    }
    
    /**
     * Get inactive vendors
     */
    public static function inactive()
    {
        return static::where('is_active', false);
    }
    
    /**
     * Check if vendor is active
     */
    public function isActive()
    {
        return $this->is_active === true;
    }
    
    /**
     * Activate the vendor
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }
    
    /**
     * Deactivate the vendor
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }
    
    /**
     * Set vendor rating
     */
    public function setRating($rating)
    {
        $this->rating = max(0, min(5, $rating)); // Ensure rating is between 0 and 5
        return $this->save();
    }
    
    /**
     * Get purchase orders for this vendor
     */
    public function purchaseOrders()
    {
        return PurchaseOrder::where('vendor_id', $this->id);
    }
    
    /**
     * Get vendor's total order value
     */
    public function getTotalOrderValue()
    {
        $orders = $this->purchaseOrders();
        $total = 0;
        foreach ($orders as $order) {
            $total += $order['total_amount'] ?? 0;
        }
        return $total;
    }
}