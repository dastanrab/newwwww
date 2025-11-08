<?php

namespace App\Models;

use App\RecordsActivity;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'subject_type',
        'description',
        'changes',
        'path',
        'method_type',
        'user_agent',
        'ip',
        'result'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'changes' => 'array',
    ];

    /*public function getCreatedAtAttribute($value)
    {
        return Verta::instance($value)->format('Y/n/j H:i:s');
    }*/

    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
