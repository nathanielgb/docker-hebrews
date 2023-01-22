<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'desc',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];
}
