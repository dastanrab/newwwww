<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id', 'fava_id', 'warehouse_id', 'io', 'car_id', 'bascule_bill_number', 'details'
    ];

    // public function getCreatedAtAttribute($value)
    // {
    //     return Verta::instance($value)->format('Y/n/j H:i:s');
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function warehouseItem()
    {
        return $this->hasMany(WarehouseItem::class);
    }

    public static function titles()
    {
        return collect([
            (object)[
                'id' => User::azadiId(),
                'title' => 'انبار آزادی'
            ],
            (object)[
                'id' => User::mayameyId(),
                'title' => 'انبار میامی'
            ]
        ]);
    }
}
