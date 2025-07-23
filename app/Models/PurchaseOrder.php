<?php

namespace App\Models;

/**
 * PurchaseOrder Model
 */
class PurchaseOrder extends BaseModel
{
    protected $table = 'purchase_orders';
    
    protected $fillable = [
        'requisition_id',
        'vendor_id',
        'order_number',
        'total_amount',
        'status',
        'order_date',
        'expected_delivery_date',
        'notes',
    ];
    
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'requisition_id' => 'integer',
        'vendor_id' => 'integer',
        'total_amount' => 'float',
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
    ];
    
    const STATUS_PENDING_APPROVAL = 'Pending Approval';
    const STATUS_APPROVED = 'Approved';
    const STATUS_ISSUED = 'Issued';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_CANCELLED = 'Cancelled';
    
    /**
     * Generate order number if not provided
     */
    protected function insert()
    {
        if (empty($this->order_number)) {
            $this->order_number = 'PO' . date('Ym') . str_pad($this->getNextOrderNumber(), 4, '0', STR_PAD_LEFT);
        }
        
        return parent::insert();
    }
    
    /**
     * Get next order number sequence
     */
    private function getNextOrderNumber()
    {
        $stmt = static::$pdo->query("SELECT COUNT(*) FROM {$this->table} WHERE order_number LIKE 'PO" . date('Ym') . "%'");
        return $stmt->fetchColumn() + 1;
    }
    
    /**
     * Get pending approval orders
     */
    public static function pendingApproval()
    {
        return static::where('status', static::STATUS_PENDING_APPROVAL);
    }
    
    /**
     * Get completed orders
     */
    public static function completed()
    {
        return static::where('status', static::STATUS_COMPLETED);
    }
    
    /**
     * Check if order is pending approval
     */
    public function isPendingApproval()
    {
        return $this->status === static::STATUS_PENDING_APPROVAL;
    }
    
    /**
     * Approve the order
     */
    public function approve()
    {
        $this->status = static::STATUS_APPROVED;
        return $this->save();
    }
    
    /**
     * Issue the order
     */
    public function issue()
    {
        $this->status = static::STATUS_ISSUED;
        return $this->save();
    }
    
    /**
     * Complete the order
     */
    public function complete()
    {
        $this->status = static::STATUS_COMPLETED;
        return $this->save();
    }
    
    /**
     * Cancel the order
     */
    public function cancel()
    {
        $this->status = static::STATUS_CANCELLED;
        return $this->save();
    }
    
    /**
     * Get related requisition
     */
    public function requisition()
    {
        return Requisition::find($this->requisition_id);
    }
    
    /**
     * Get related vendor
     */
    public function vendor()
    {
        return Vendor::find($this->vendor_id);
    }
    
    /**
     * Get goods receipts for this order
     */
    public function goodsReceipts()
    {
        return GoodsReceipt::where('purchase_order_id', $this->id);
    }
    
    /**
     * Get invoices for this order
     */
    public function invoices()
    {
        return Invoice::where('purchase_order_id', $this->id);
    }
}