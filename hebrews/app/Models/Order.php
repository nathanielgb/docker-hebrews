<?php

namespace App\Models;

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
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

}
