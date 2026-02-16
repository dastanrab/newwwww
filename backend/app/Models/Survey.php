<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = ['submit_id', 'title'];

    public function submit()
    {
        return $this->belongsTo(Submit::class);
    }
}
