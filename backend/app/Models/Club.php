<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $fillable = ['title', 'sub_title', 'content', 'image', 'brand_icon', 'status', 'score','user_id','has_site','discount_type','discount_value'];
    protected $dates = ['deleted_at'];

    public function categories()
    {
        return $this->belongsToMany(ClubCategory::class,'category_club','club_id', 'category_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
