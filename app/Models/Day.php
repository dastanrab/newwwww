<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = ['start_at', 'end_at'];
    public function polygonDayHours()
    {
        return $this->hasMany(PolygonDayHour::class);
    }

}
