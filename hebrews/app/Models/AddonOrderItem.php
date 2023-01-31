<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonOrderItem extends Model
{
    use HasFactory;

    protected $table = 'addon_order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'order_item_id',
        'addon_id',
        'inventory_id',
        'inventory_name',
        'inventory_code',
        'menu_id',
        'unit',
        'unit_label',
        'qty',
        'is_dinein'
    ];

    /**
     * Get the order related to the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the Addon item related to the order addon item.
     */
    public function addon()
    {
        return $this->belongsTo(MenuAddOn::class, 'addon_id', 'id');
    }

    /**
     * Get the order item related to the order addon item.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'order_item_id');
    }

}
