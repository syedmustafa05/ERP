<?php

namespace App\Models;

/**
 * Invoice Model
 */
class Invoice extends BaseModel
{
    protected $table = 'invoices';
    
    protected $fillable = [
        'purchase_order_id',
        'invoice_number',
        'amount',
        'status',
        'invoice_date',
        'due_date',
        'paid_date',
        'payment_method',
        'reference_number',
        'notes',
    ];
    
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'purchase_order_id' => 'integer',
        'amount' => 'float',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];
    
    const STATUS_PENDING = 'Pending';
    const STATUS_PAID = 'Paid';
    const STATUS_OVERDUE = 'Overdue';
    const STATUS_CANCELLED = 'Cancelled';
    
    const PAYMENT_BANK_TRANSFER = 'Bank Transfer';
    const PAYMENT_CHECK = 'Check';
    const PAYMENT_CREDIT_CARD = 'Credit Card';
    const PAYMENT_CASH = 'Cash';
    
    /**
     * Get pending invoices
     */
    public static function pending()
    {
        return static::where('status', static::STATUS_PENDING);
    }
    
    /**
     * Get paid invoices
     */
    public static function paid()
    {
        return static::where('status', static::STATUS_PAID);
    }
    
    /**
     * Get overdue invoices
     */
    public static function overdue()
    {
        $today = date('Y-m-d');
        $instance = new static();
        $stmt = static::$pdo->prepare("SELECT * FROM {$instance->table} WHERE status = ? AND due_date < ?");
        $stmt->execute([static::STATUS_PENDING, $today]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if invoice is pending
     */
    public function isPending()
    {
        return $this->status === static::STATUS_PENDING;
    }
    
    /**
     * Check if invoice is paid
     */
    public function isPaid()
    {
        return $this->status === static::STATUS_PAID;
    }
    
    /**
     * Check if invoice is overdue
     */
    public function isOverdue()
    {
        return $this->status === static::STATUS_PENDING && 
               $this->due_date && 
               strtotime($this->due_date) < time();
    }
    
    /**
     * Mark invoice as paid
     */
    public function markAsPaid($paymentMethod = null, $referenceNumber = null)
    {
        $this->status = static::STATUS_PAID;
        $this->paid_date = date('Y-m-d');
        
        if ($paymentMethod) {
            $this->payment_method = $paymentMethod;
        }
        
        if ($referenceNumber) {
            $this->reference_number = $referenceNumber;
        }
        
        return $this->save();
    }
    
    /**
     * Cancel the invoice
     */
    public function cancel()
    {
        $this->status = static::STATUS_CANCELLED;
        return $this->save();
    }
    
    /**
     * Get days until due
     */
    public function getDaysUntilDue()
    {
        if (!$this->due_date) {
            return null;
        }
        
        $today = new \DateTime();
        $dueDate = new \DateTime($this->due_date);
        $diff = $today->diff($dueDate);
        
        return $dueDate < $today ? -$diff->days : $diff->days;
    }
    
    /**
     * Get related purchase order
     */
    public function purchaseOrder()
    {
        return PurchaseOrder::find($this->purchase_order_id);
    }
    
    /**
     * Get total amount for all invoices
     */
    public static function getTotalAmount()
    {
        $instance = new static();
        $stmt = static::$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM {$instance->table}");
        return $stmt->fetchColumn();
    }
    
    /**
     * Get total paid amount
     */
    public static function getTotalPaidAmount()
    {
        $instance = new static();
        $stmt = static::$pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM {$instance->table} WHERE status = ?");
        $stmt->execute([static::STATUS_PAID]);
        return $stmt->fetchColumn();
    }
}