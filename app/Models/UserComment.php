<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserComment extends Model
{
    use HasFactory;
    use RecordsActivity;
    protected $fillable = [
        'user_id', 'operator_id', 'text'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
