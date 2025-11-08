<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferrerMobile extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = ['user_id', 'mobile'];
}
