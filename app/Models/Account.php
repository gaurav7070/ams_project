<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Account extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'account_name',
        'account_number',
        'account_type',
        'currency',
        'balance',
        'created_by',
        'modified_by'
    ];

    // Generate UUID automatically
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    public function accounts()
    {
        return $this->hasMany(Account::class, 'user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }
}
