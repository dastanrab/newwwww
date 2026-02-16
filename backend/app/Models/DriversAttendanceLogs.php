<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriversAttendanceLogs extends Model
{
    use HasFactory;
    protected $table ='drivers_attendance_logs';
    protected $fillable = ['id','user_id','start_at','end_at','time_length','submit_id'];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
