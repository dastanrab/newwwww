<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;


    protected $fillable = ['fava_id', 'car_id', 'lat', 'long', 'speed', 'date'];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
