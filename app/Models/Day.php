<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;


    protected $fillable = ['start_at', 'end_at'];
    public function polygonDayHours()
    {
        return $this->hasMany(PolygonDayHour::class);
    }

}
