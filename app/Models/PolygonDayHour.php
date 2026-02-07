<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolygonDayHour extends Model
{
    use HasFactory;


    protected $fillable = ['city_id', 'polygon_id', 'day_id', 'hour_id', 'status'];

    public function polygon()
    {
        return $this->belongsTo(Polygon::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function hour()
    {
        return $this->belongsTo(Hour::class);
    }

}
