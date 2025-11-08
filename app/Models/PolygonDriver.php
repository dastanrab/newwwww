<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolygonDriver extends Model
{
    use HasFactory;
    use RecordsActivity;
    protected $fillable = ['user_id', 'polygon_id'];

    public function polygon()
    {
        return $this->belongsTo(Polygon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
