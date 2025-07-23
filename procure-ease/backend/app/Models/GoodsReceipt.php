<?php

namespace App\Models;

/**
 * GoodsReceipt Model
 */
class GoodsReceipt extends BaseModel
{
    protected $table = 'goods_receipts';
    
    protected $fillable = [
        'purchase_order_id',
        'receipt_number',
        'received_date',
        'quantity_received',
        'item',
        'condition',
        'notes',
        'received_by',
    ];
    
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'purchase_order_id' => 'integer',
        'quantity_received' => 'integer',
        'received_date' => 'date',
    ];
    
    const CONDITION_GOOD = 'Good';
    const CONDITION_DAMAGED = 'Damaged';
    const CONDITION_PARTIAL = 'Partial';
    
    /**
     * Generate receipt number if not provided
     */
    protected function insert()
    {
        if (empty($this->receipt_number)) {
            $this->receipt_number = 'GR' . date('Ym') . str_pad($this->getNextReceiptNumber(), 4, '0', STR_PAD_LEFT);
        }
        
        return parent::insert();
    }
    
    /**
     * Get next receipt number sequence
     */
    private function getNextReceiptNumber()
    {
        $stmt = static::$pdo->query("SELECT COUNT(*) FROM {$this->table} WHERE receipt_number LIKE 'GR" . date('Ym') . "%'");
        return $stmt->fetchColumn() + 1;
    }
    
    /**
     * Get receipts in good condition
     */
    public static function goodCondition()
    {
        return static::where('condition', static::CONDITION_GOOD);
    }
    
    /**
     * Get damaged receipts
     */
    public static function damaged()
    {
        return static::where('condition', static::CONDITION_DAMAGED);
    }
    
    /**
     * Check if receipt is in good condition
     */
    public function isGoodCondition()
    {
        return $this->condition === static::CONDITION_GOOD;
    }
    
    /**
     * Check if receipt is damaged
     */
    public function isDamaged()
    {
        return $this->condition === static::CONDITION_DAMAGED;
    }
    
    /**
     * Mark as good condition
     */
    public function markAsGood()
    {
        $this->condition = static::CONDITION_GOOD;
        return $this->save();
    }
    
    /**
     * Mark as damaged
     */
    public function markAsDamaged()
    {
        $this->condition = static::CONDITION_DAMAGED;
        return $this->save();
    }
    
    /**
     * Get related purchase order
     */
    public function purchaseOrder()
    {
        return PurchaseOrder::find($this->purchase_order_id);
    }
}