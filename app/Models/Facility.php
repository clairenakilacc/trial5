<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'connection_type',
        'facility_type',
        'cooling_tools',
        'floor_level',
        'building',
        'remarks',
        'facility_img',
    ];
}
