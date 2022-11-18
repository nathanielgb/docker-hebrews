<?php

namespace App\Models;

use App\Services\TokenService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'branch_id',
        'customer_id',
        'customer_name',
        'server_name',
        'table',
        'subtotal',
        'discount_amount',
        'fees',
        'total_amount',
        'deposit_bal',
        'remaining_bal',
        'confirmed_amount',
        'amount_given',
        'payment_acc',
        'discount_type',
        'discount_unit',
        'order_type',
        'delivery_method',
        'completed',
        'pending',
        'confirmed',
        'paid',
        'reason',
        'note',
        'credited_by',
        'confirmed_by',
        'cancelled_by',
        'bank_id'
    ];

    protected $casts = [
        'table' => 'array'
    ];

    /**
     * Get the items related to order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function scopeGenerateUniqueId($query)
    {
        $orderId = (new TokenService)->generateToken('alnum', 16);
        $isUniqueId = false;

        while (!$isUniqueId) {
            $isUniqueId = $query->where('order_id', $orderId)->count() <= 0;

            if (!$isUniqueId) {
                $orderId = (new TokenService)->generateToken('alnum', 16);
            }
        }

        return $orderId;
    }
}
