<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuInventory extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inventory_code',
        'name',
        'unit',
        'stock',
        'category_id',
        'previous_stock',
        'modified_by',
    ];

    /**
     * Get the category associated with the inventory item.
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id', 'id');
    }

    // /**
    //  * Get the products associated with the inventory item.
    //  */
    // public function products()
    // {
    //     return $this->hasMany(Menu::class, 'inventory_id', 'id');
    // }

    // /**
    //  * Get the products associated with the inventory item.
    //  */
    // public function addons()
    // {
    //     return $this->hasMany(MenuAddOn::class, 'inventory_id', 'id');
    // }
}
