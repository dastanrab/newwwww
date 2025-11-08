<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverWallet extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = ['user_id', 'amount', /*Rial*/ 'city_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
