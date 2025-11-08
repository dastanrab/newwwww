<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Percentage extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = ['recyclable_id', 'weight', 'percent', 'price'];
}
