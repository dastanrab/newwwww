<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeshanApiLog extends Model
{
    protected $table = 'neshan_api_logs';
    protected $fillable = ['is_driver_location','user_id','start_info','endpoint','request_data','response_data','status_code','error_message'];
    protected $casts = ['response_data' => 'array'];
}
