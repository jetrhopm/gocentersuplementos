<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    protected $fillable = [
        'postal_code',
        'settlement',
        'settlement_type',
        'municipality',
        'state',
        'city',
        'zone',
        'state_code',
        'municipality_code',
    ];
}
