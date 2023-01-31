<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Get the branch inventories for the Inventory Category.
     */
    public function branchInventories()
    {
        return $this->hasMany(BranchMenuInventory::class, 'category_id', 'id');
    }

    /**
     * Get the warehouse inventories for the Inventory Category.
     */
    public function wareHouseInventories()
    {
        return $this->hasMany(MenuInventory::class, 'category_id', 'id');
    }
}
