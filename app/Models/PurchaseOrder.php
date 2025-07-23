<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PurchaseOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'requisition_id',
        'vendor_id',
        'order_number',
        'total_amount',
        'status',
        'order_date',
        'expected_delivery_date',
        'notes',
        'terms_conditions',
        'shipping_address',
        'billing_address',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    /**
     * The possible status values.
     */
    public const STATUS_DRAFT = 'Draft';
    public const STATUS_PENDING_APPROVAL = 'Pending Approval';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_ISSUED = 'Issued';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELLED = 'Cancelled';

    /**
     * Get all possible status values.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_APPROVED,
            self::STATUS_ISSUED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $model->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'PO';
        $year = date('Y');
        $month = date('m');
        
        $lastOrder = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? (intval(substr($lastOrder->order_number, -4)) + 1) : 1;

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the requisition that owns the purchase order.
     */
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(Requisition::class);
    }

    /**
     * Get the vendor that owns the purchase order.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the goods receipts for the purchase order.
     */
    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    /**
     * Get the invoices for the purchase order.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Scope a query to only include pending approval orders.
     */
    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL);
    }

    /**
     * Scope a query to only include issued orders.
     */
    public function scopeIssued(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ISSUED);
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to filter by vendor.
     */
    public function scopeByVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('order_date', [$from, $to]);
    }

    /**
     * Get the net amount (total - discount + tax + shipping).
     */
    public function getNetAmountAttribute(): float
    {
        return $this->total_amount - $this->discount_amount + $this->tax_amount + $this->shipping_cost;
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'success',
            self::STATUS_ISSUED => 'primary',
            self::STATUS_APPROVED => 'info',
            self::STATUS_CANCELLED => 'danger',
            default => 'warning',
        };
    }

    /**
     * Check if the order can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_PENDING_APPROVAL,
        ]);
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ]);
    }

    /**
     * Approve the purchase order.
     */
    public function approve(): bool
    {
        if ($this->status === self::STATUS_PENDING_APPROVAL) {
            return $this->update(['status' => self::STATUS_APPROVED]);
        }

        return false;
    }

    /**
     * Issue the purchase order.
     */
    public function issue(): bool
    {
        if ($this->status === self::STATUS_APPROVED) {
            return $this->update(['status' => self::STATUS_ISSUED]);
        }

        return false;
    }

    /**
     * Complete the purchase order.
     */
    public function complete(): bool
    {
        if ($this->status === self::STATUS_ISSUED) {
            return $this->update(['status' => self::STATUS_COMPLETED]);
        }

        return false;
    }

    /**
     * Cancel the purchase order.
     */
    public function cancel(): bool
    {
        if ($this->canBeCancelled()) {
            return $this->update(['status' => self::STATUS_CANCELLED]);
        }

        return false;
    }
}