<?php

namespace App\Models;

use App\Services\TokenService;
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
        'order_item_id',
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
        'note',
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
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the order related to the item.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    public function scopeGenerateUniqueId($query)
    {
        $ordItemId = (new TokenService)->generateToken('alnum', 16);
        $isUniqueId = false;

        while (!$isUniqueId) {
            $isUniqueId = $query->where('order_item_id', $ordItemId)->count() <= 0;

            if (!$isUniqueId) {
                $ordItemId = (new TokenService)->generateToken('alnum', 16);
            }
        }

        return $ordItemId;
    }

}
