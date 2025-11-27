<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function productMovements()
    {
        return $this->hasMany(ProductMovement::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'admin_id');
    }

    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function isOperator()
    {
        return $this->role && $this->role->name === 'operator';
    }

    public function isSupplier()
    {
        return $this->role && $this->role->name === 'supplier';
    }

    public function isStore()
    {
        return $this->role && $this->role->name === 'store';
    }
}