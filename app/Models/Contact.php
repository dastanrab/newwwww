<?php

namespace App\Models;

use App\RecordsActivity;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use RecordsActivity;
    protected $fillable = [
        'user_id', 'reply', 'name', 'email', 'subject', 'message', 'ip', 'admin_seen_at', 'user_seen_at'
    ];

    public function contactReplies()
    {
        return $this->hasMany(ContactReply::class);
    }
}
