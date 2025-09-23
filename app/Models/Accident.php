<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accident extends Model
{
    use HasFactory;

    protected $fillable = [
        'commune',
        'lieu',
        'latitude',
        'longitude',
        'altitude',
        'precision',
        'photos',
        'nombre_vehicules'
    ];

    protected $casts = [
        'photos' => 'array'
    ];
}