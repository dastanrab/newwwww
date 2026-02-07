<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
    use HasFactory;


    // نوع صنف
    protected $fillable = [
        'fava_id', 'title'
    ];
}
