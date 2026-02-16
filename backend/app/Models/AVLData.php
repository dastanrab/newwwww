<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AVLData extends Model
{
    use HasFactory;
    protected $connection = 'pgsql';
    protected $table = 'avl_data';
    protected $fillable = ['device_id' , 'latitude' , 'longitude', 'timestamp', 'raw_data'];
}
