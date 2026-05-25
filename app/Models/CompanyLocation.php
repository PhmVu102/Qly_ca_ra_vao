<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLocation extends Model
{
    protected $table = 'company_locations';

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius_meter',
        'address',
        'is_main',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'radius_meter' => 'integer',
            'is_main' => 'boolean',
            'status' => 'boolean',
        ];
    }
}
