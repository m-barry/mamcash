<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'promotion';

    protected $fillable = [
        'code',
        'description',
        'discount',
        'active',
        'type',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = false;
}
