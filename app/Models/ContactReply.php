<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactReply extends Model
{
    use HasFactory;


    protected $fillable = ['contact_id', 'user_id', 'message'];

    // protected $touches = ['contact'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function contact()
    {
        return $this->belongsTo('App\Contact');
    }
}
