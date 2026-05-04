<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    protected $table    = 'operators';
    protected $fillable = ['name', 'country', 'code_prefix', 'active', 'logo_url', 'show_coming_soon'];

    protected $casts = [
        'active'           => 'boolean',
        'show_coming_soon' => 'boolean',
    ];
}
