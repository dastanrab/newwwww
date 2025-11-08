<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollcallHistory extends Model
{
    use HasFactory;

    protected $fillable = ['rollcall_id', 'user_id', 'description', 'start_at', 'end_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rollcall()
    {
        return $this->belongsTo(Rollcall::class);
    }

}
