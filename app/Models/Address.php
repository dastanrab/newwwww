<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;
    use RecordsActivity;
    protected $fillable = [
        'user_id', 'title', 'address', 'lat', 'lon', 'region', 'district', 'status', 'city_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function submits()
    {
        return $this->hasMany('App\Submit');
    }
}
