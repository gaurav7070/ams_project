<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true; // Ensure timestamps are enabled

    protected $fillable = [
        'first_name',
        'last_name',
        'login_id',
        'password',
        'email',
        'account_type',
        'created_by',
        'modified_by',
        'extra1',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Boot method for UUID generation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid(); // Ensure UUID is set
            }
        });
    }

    /**
     * Relationship: User has many accounts
     */
    public function accounts()
    {
        return $this->hasMany(Account::class, 'user_id');
    }
}
