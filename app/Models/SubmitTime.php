<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmitTime extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = ['instant', 'saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
}
