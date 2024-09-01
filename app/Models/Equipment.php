<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Equipment extends Model
{
    use HasFactory;

    // const ITEM_PREFIX = 'ITEM';
    // const ITEM_COLUMN = 'item_number';
    // const PROPERTY_PREFIX = 'PROP';
    // const PROPERTY_COLUMN = 'property_number';
    // const CONTROL_PREFIX = 'CTRL';
    // const CONTROL_COLUMN = 'control_number';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = [];

    protected $fillable = [
        'unit_no',
        'description',
        'specifications',
        'facility_id',
        'category_id',
        'status',
        'date_acquired',
        'supplier',
        'amount',
        'estimated_life',
        'item_no',
        'property_no',
        'control_no',
        'serial_no',
        'no_of_stocks',
        'restocking_point',
        'person_liable',
        'remarks',
        'stock_unit_id',
        'name',
        'availability'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function stockUnit()
    {
        return $this->belongsTo(StockUnit::class, 'stock_unit_id');
    }

    // public static function generateItemNumber()
    // {
    //     $prefix = 'ITEM';
    //     $year = date('Y');

    //     try {

    //         return DB::transaction(function () use ($prefix, $year) {

    //             $latestNumber = self::whereYear('created_at', $year)
    //                 ->where('item_number', 'LIKE', "$prefix-$year-%")
    //                 ->orderBy('item_number', 'desc')
    //                 ->value('item_number');


    //             $nextNumber = '001';
    //             if ($latestNumber) {

    //                 $latestNumberPart = explode('-', $latestNumber)[2];
    //                 $nextNumber = str_pad((int)$latestNumberPart + 1, 3, '0', STR_PAD_LEFT);
    //             }

    //             return "$prefix-$year-$nextNumber";
    //         });
    //     } catch (\Exception $e) {

    //         Log::error('Failed to generate item number: ' . $e->getMessage());

    //         return null;
    //     }
    // }

    // public static function generateUniqueUnitID(): string
    // {
    //     $date = Carbon::now()->format('Ymd'); 
    //     $timestamp = Carbon::now()->timestamp; 

    //     $attempts = 0;
    //     $maxAttempts = 100; 

    //     do {
    //         $sequence = rand(1000, 9999); 
    //         $unitID = sprintf('%s-%d-%d', $date, $timestamp, $sequence);

    //         $exists = self::where('unit_id', $unitID)->exists();

    //         $attempts++;
    //     } while ($exists && $attempts < $maxAttempts);

    //     if ($attempts >= $maxAttempts) {
    //         throw new \Exception('Failed to generate a unique unit ID after multiple attempts.');
    //     }

    //     return $unitID;
    // }

    // public static function generateUniqueItemNumber()
    // {
    //     return self::generateUniqueNumber(self::ITEM_PREFIX, self::ITEM_COLUMN);
    // }

    // public static function generateUniquePropertyNumber()
    // {
    //     return self::generateUniqueNumber(self::PROPERTY_PREFIX, self::PROPERTY_COLUMN);
    // }

    // public static function generateUniqueControlNumber()
    // {
    //     return self::generateUniqueNumber(self::CONTROL_PREFIX, self::CONTROL_COLUMN);
    // }

    // private static function generateUniqueNumber($prefix, $columnName)
    // {
    //     $year = date('Y'); 

    //     try {           
    //         return DB::transaction(function () use ($prefix, $columnName, $year) {               
    //             $latestNumber = self::whereYear('created_at', $year)
    //                 ->where($columnName, 'LIKE', "$prefix-$year-%")
    //                 ->orderBy($columnName, 'desc')
    //                 ->value($columnName); 

    //             $nextNumber = '001';
    //             if ($latestNumber) {

    //                 $latestNumberPart = explode('-', $latestNumber)[2];
    //                 $nextNumber = str_pad((int)$latestNumberPart + 1, 3, '0', STR_PAD_LEFT);
    //             }

    //             return "$prefix-$year-$nextNumber";
    //         });
    //     } catch (\Exception $e) {

    //         Log::error('Failed to generate unique number: ' . $e->getMessage());            
    //         return null;
    //     }
    // }
}
