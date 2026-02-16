<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recyclable extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'price'/*Toman*/, 'max_price'
    ];

    public function percentages()
    {
        return $this->hasMany('App\Models\Percentage');
    }
}
