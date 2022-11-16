<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'units',
        'reg_price',
        'retail_price',
        'wholesale_price',
        'rebranding_price',
        'distributor_price',
        'category_id',
        'inventory_id',
        'sub_category',
    ];

    /**
     * Get the category that  the menu belongs.
     */
    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    /**
     * Get the inventory associated with the menu item.
     */
    public function inventory()
    {
        return $this->belongsTo(BranchMenuInventory::class, 'id', 'inventory_id');
    }

    /**
     * Get the order items associated with the menu item.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'id', 'inventory_id');
    }


}
