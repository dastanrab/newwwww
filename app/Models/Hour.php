<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hour extends Model
{
    use HasFactory;


    protected $fillable = ['title'];

    public function polygonDayHours()
    {
        return $this->hasMany(PolygonDayHour::class);
    }
}
