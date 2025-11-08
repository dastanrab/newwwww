<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = ['id','s_user_id','d_user_id','amount','pay_type','reason','type','transactionable_id','transactionable_type'];
    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
