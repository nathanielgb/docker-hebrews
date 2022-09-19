<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $table = 'order_items';
    /**
     * The attributes that are mass assignable.
     *  STATUS:
     *  ORDERED
     *  PREPARING
     *  DONE
     *  SERVED
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'menu_id',
        'inventory_id',
        'inventory_name',
        'name',
        'from',
        'price',
        'units',
        'unit_label',
        'data',
        'qty',
        'type',
        'total_amount',
        'status',
        'served_by',
    ];

    protected $casts = [
        'data' => 'array'
    ];

    /**
     * Get the order related to the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the order related to the item.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
