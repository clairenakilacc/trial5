<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipment;

class EquipmentMonitoring extends Model
{
    use HasFactory;

    protected $table = 'equipment_monitorings';

    protected $fillable = [
        'equipment_id',
        'monitored_by',
        'monitored_date',
        'monitoring_status',
        'remarks',
        'availability',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'monitored_by');
    }
}
