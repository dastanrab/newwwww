<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referrer extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id', 'referrer_amount', 'rewarded_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
