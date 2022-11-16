<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'branch_id',
        'username',
        'email',
        'password',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the items related to admin user.
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'admin_id', 'id');
    }

    /**
     * Get the branch related to admin user.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    // /**
    //  * Set the user's name.
    //  *
    //  * @param  string  $value
    //  * @return void
    //  */
    // public function getBranchNamesAttribute()
    // {
    //     if ($this->branch) {
    //         $labels = Branch::whereIn('id', $this->branch)->pluck('name')->toArray();

    //         return $labels;
    //     }
    // }

}
