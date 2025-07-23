<?php

namespace App\Models;

/**
 * Requisition Model
 */
class Requisition extends BaseModel
{
    protected $table = 'requisitions';
    
    protected $fillable = [
        'item',
        'quantity',
        'status',
        'requestedBy',
        'date',
        'description',
        'estimated_cost',
        'priority',
    ];
    
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'estimated_cost' => 'float',
        'date' => 'date',
    ];
    
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';
    
    const PRIORITY_LOW = 'Low';
    const PRIORITY_MEDIUM = 'Medium';
    const PRIORITY_HIGH = 'High';
    const PRIORITY_URGENT = 'Urgent';
    
    /**
     * Get pending requisitions
     */
    public static function pending()
    {
        return static::where('status', static::STATUS_PENDING);
    }
    
    /**
     * Get approved requisitions
     */
    public static function approved()
    {
        return static::where('status', static::STATUS_APPROVED);
    }
    
    /**
     * Check if requisition is approved
     */
    public function isApproved()
    {
        return $this->status === static::STATUS_APPROVED;
    }
    
    /**
     * Approve the requisition
     */
    public function approve()
    {
        $this->status = static::STATUS_APPROVED;
        return $this->save();
    }
    
    /**
     * Reject the requisition
     */
    public function reject()
    {
        $this->status = static::STATUS_REJECTED;
        return $this->save();
    }
    
    /**
     * Get purchase orders for this requisition
     */
    public function purchaseOrders()
    {
        return PurchaseOrder::where('requisition_id', $this->id);
    }
}