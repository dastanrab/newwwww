<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = ['ip'];

    public function getCreatedAtAttribute($value)
    {
        return Verta::instance($value);
    }
}
