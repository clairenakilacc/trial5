<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'facility_id',
        'monitored_by',
        'no_of_stocks',
        'no_of_stocks_deducted',
        'stocks_left',
        'deducted_at',
        //'added_at',
        'no_of_stocks_added', // Add the new column to fillable
    ];

    /**
     * Relationships
     */
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
