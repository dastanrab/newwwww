<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedRollcall extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'start_lat', 'start_lon', 'end_lat', 'end_lon'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
