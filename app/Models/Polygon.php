<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polygon extends Model
{
    use HasFactory;
    use RecordsActivity;
    protected $fillable = ['region','polygon', 'middle', 'color', 'sort','has_instant','has_legal_collect','has_illegal_collect','city_id'];

    public function polygonDayHours()
    {
        return $this->hasMany(PolygonDayHour::class);
    }

}
