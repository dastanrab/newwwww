<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
    use HasFactory;
    use RecordsActivity;

    // نوع صنف
    protected $fillable = [
        'fava_id', 'title'
    ];
}
