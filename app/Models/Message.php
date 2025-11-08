<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use RecordsActivity;
    protected $fillable = [
        'title', 'text'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
