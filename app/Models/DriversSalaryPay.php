<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriversSalaryPay extends Model
{
    use HasFactory;
    protected $table = 'drivers_salary_payments';
    protected $fillable = ['user_id','amount'];
}
