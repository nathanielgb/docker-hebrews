<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchMenuInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'stock',
        'previous_stock',
        'branch_id',
        'category_id',
        'inventory_code',
        'modified_by',
    ];

    /**
     * Get the products associated with the inventory item.
     */
    public function products()
    {
        return $this->hasMany(Menu::class, 'inventory_id', 'id');
    }

    /**
     * Get the products associated with the inventory item.
     */
    public function addons()
    {
        return $this->hasMany(MenuAddOn::class, 'inventory_id', 'id');
    }

    /**
     * Get the branch associated with the inventory item.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    /**
     * Get the category associated with the inventory item.
     */
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }
}
