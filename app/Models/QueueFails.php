<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueFails extends Model
{
    use HasFactory;
    protected $table = 'queues_fails';
    protected $fillable = ['queue','data','error'];
    protected $casts = ['data'=>'array'];
}
