<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sub',
        'from'
    ];


    protected $casts = [
        'sub' => 'array'
    ];

    /**
     * Get the menus for the Menu Category.
     */
    public function menus()
    {
        return $this->hasMany(Menu::class, 'category_id', 'id');
    }
}
