<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashout extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = ['trace_code', 'amount', /*Toman*/ 'name', 'card_number', 'shaba_number', 'bank', 'operator_id','status'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function operator()
    {
        return $this->belongsTo('App\Models\User');
    }
}
