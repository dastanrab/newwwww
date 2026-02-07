<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolygonDriver extends Model
{
    use HasFactory;

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
