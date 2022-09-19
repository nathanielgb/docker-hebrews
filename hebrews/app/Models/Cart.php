<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'menu_id',
        'inventory_id',
        'name',
        'type',
        'units',
        'price',
        'total',
        'qty',
        'note',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    /**
     * Get the menu associated with the cart item.
     */
    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }

    /**
     * Get the inventory associated with the cart item.
     */
    public function inventory()
    {
        return $this->hasOne(MenuInventory::class, 'id', 'inventory_id');
    }
}
