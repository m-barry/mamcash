<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    public $timestamps = false;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone_number',
        'country',
        'city',
        'gender',
        'postal',
        'addresse',
        'birth_date',
        'created_date',
        'active',
        'account_id',
        'role_id',
    ];

    protected $hidden = [
        'password',
    ];

    // Disable remember_token: column does not exist in Spring Boot DB
    public function getRememberTokenName(): string
    {
        return '';
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'password'   => 'hashed',
            'active'     => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_user');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'id_user');
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'ROLE_ADMIN';
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }
}
