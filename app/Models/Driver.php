<?php

namespace App\Models;

use App\Notifications\UserNotification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class Driver extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id', 'submit_id', 'car_id', 'fava_id', 'status', 'weights', 'user_bank_code', 'fava_bank_code', 'collected_at', 'city_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function submit()
    {
        return $this->belongsTo(Submit::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function receives()
    {
        return $this->hasMany(Receive::class);
    }

    public static function add($user,$submit)
    {
        $driver = new Driver;
        $driver->user_id = $user->id;
        $driver->car_id = Car::where('user_id', $user->id)->where('is_active', true)->first()->id;
        $driver->submit_id = $submit->id;
        $driver->status = 2;
        $driver->city_id=$submit->city_id;
        $saveDriver = $driver->save();
        if($saveDriver){
            $submit->update(['status' => 2]);
//            Fava::updateRequest($submit->fava_id, 2);
            /*$$requester = User::find($submit->user_id);
            data = [
                'title' => 'درخواست شما تایید شد',
                'message' => $requester->name.' عزیز، در زمان مقرر درخواست شما جمع آوری خواهد شد',
            ];
        //    Notification::send($requester, new UserNotification(Firebase::dataFormat($data)));*/
            return $saveDriver;
        }
        else{
            return false;
        }
    }

}
