<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletLog extends Model
{
    protected $table = 'wallet_logs';

    protected $fillable = [
        'logged_at',
        'data',
    ];

    protected $casts = [
        'data' => 'array',   // JSON to array
        'logged_at' => 'date',
    ];

    public $timestamps = false; // optional if you don't use created_at/updated_at
}
