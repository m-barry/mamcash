<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'account';

    protected $fillable = ['iban', 'created_date'];

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
