<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuAddOn extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'inventory_id',
    ];

    /**
     * Get the inventory associated with the addon item.
     */
    public function inventory()
    {
        return $this->belongsTo(BranchMenuInventory::class, 'inventory_id', 'id');
    }
}
