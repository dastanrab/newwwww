<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriversSalaryDetails extends Model
{
    use HasFactory;
    protected $fillable=['id','user_id','submit_id','distance','total_attendance','metals_reward','weight_price','reward_price','weight','creator_id','salary_type'];
    protected $table='drivers_salary_details';
}
