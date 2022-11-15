<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchMenuInventory extends Model
{
    use HasFactory;

    protected $table = 'branch_menu_inventories';


    protected $fillable = [
        'name',
        'unit',
        'stock',
        'previous_stock',
        'branch_id',
        'inventory_code',
        'modified_by',
    ];

}
