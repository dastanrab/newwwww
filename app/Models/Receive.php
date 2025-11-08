<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receive extends Model
{
    use HasFactory;
    use RecordsActivity;
    protected $fillable = [
        'title', 'price',/*Toman*/ 'fava_price','fava_id', 'weight'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
