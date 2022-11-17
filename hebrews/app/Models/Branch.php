<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location'
    ];


    /**
     * Get the inventory items of branch.
     */
    public function inventories()
    {
        return $this->hasMany(BranchMenuInventory::class, 'branch_id', 'id');
    }
}
