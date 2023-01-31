<?php

namespace App\Models;

use App\Services\TokenService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $total_stocks;

    use HasFactory;
    protected $table = 'order_items';
    /**
     * The attributes that are mass assignable.
     *  STATUS:
     *  ORDERED
     *  PREPARING
     *  DONE
     *  SERVED
     *  VOID
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_item_id',
        'order_id',
        'menu_id',
        'inventory_id',
        'inventory_name',
        'inventory_code',
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
        'kitchen_cleared',
        'dispatcher_cleared',
        'production_cleared',
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
     * Get the menu related to the item.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    /**
     * Get the addons of order.
     */
    public function addons()
    {
        return $this->hasMany(AddonOrderItem::class, 'order_item_id', 'order_item_id');
    }

    /**
     * Get the iventory related to the item.
     */
    public function inventory()
    {
        return $this->belongsTo(BranchMenuInventory::class, 'inventory_id', 'id');
    }

    /**
     * Get the addons items according to order type
     */
    public function getAddonItems($isdinein)
    {
        return MenuAddOn::whereHas('inventory')->where('menu_id', $this->menu_id)->where('is_dinein', $isdinein)->get();
    }

    /**
     * Get the addon item according to order type
     */
    public function getAddonItem($isdinein, $inventory_id)
    {
        return MenuAddOn::where('inventory_id', $inventory_id)->whereHas('inventory')->where('menu_id', $this->menu_id)->where('is_dinein', $isdinein)->first();
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
