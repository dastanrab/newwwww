<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverSuggestedRequests extends Model
{
    use HasFactory;
    protected $table = 'driver_suggested_requests';

    protected $fillable = ['driver_id','submit_id','status','parent_id','start_at','is_emergency','in_regions'];
}
