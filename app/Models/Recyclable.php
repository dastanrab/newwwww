<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recyclable extends Model
{
    use HasFactory;
    use RecordsActivity;
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'price'/*Toman*/, 'max_price'
    ];

    public function percentages()
    {
        return $this->hasMany('App\Models\Percentage');
    }
}
