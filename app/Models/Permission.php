<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = ['name', 'label'];

    public function roles(){
        return $this->belongsToMany(Role::class);
    }
}
