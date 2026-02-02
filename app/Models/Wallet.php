<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    use RecordsActivity;
    protected $fillable = [
        'wallet' // toman
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bazistWallet()
    {
        return $this->hasMany(WalletDetails::class);
    }
}
