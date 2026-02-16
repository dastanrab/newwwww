<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rollcall extends Model
{
    use HasFactory;


    protected $fillable = ['user_id', 'start_lat', 'start_lon', 'end_lat', 'end_lon', 'start_at', 'end_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function histories()
    {
        return $this->hasMany(RollcallHistory::class);
    }

    public static function add($data = [])
    {
        $user = auth()->user();
        $start_date = now();
        $start_date->hour = (int)$data['hour'];
        $start_date->minute = (int)$data['min'];
        $start_date->second = 0;

        $rollcall = new Rollcall;
        $rollcall->user_id = $data['driverId'];
        $rollcall->start_lat = 36.3;
        $rollcall->start_lon = 59.6;
        $rollcall->start_at = $start_date;
        $rollcall->save();
        $rollcall->histories()->create([
            'user_id' => $user->id,
            'description' => $data['description'],
            'start_at' => $start_date
        ]);
        $car = Car::where('user_id', $data['driverId'])->where('is_active', true)->first();
        $car->update(['rollcall_status' => 2]);
    }

    public function end($data = [])
    {
        $end_date = Carbon::parse($this->created_at);
        $end_date->hour = $data['hour'];
        $end_date->minute = $data['min'];
        $end_date->second = 0;

        $this->update([
            'end_lat' => 36.3,
            'end_lon' => 59.6,
            'end_at' => $end_date,
        ]);

        $this->histories()->create([
            'user_id'     => auth()->id(),
            'description' => $data['description'],
            'end_at'      => $end_date
        ]);

        $car = Car::where('user_id', $this->user_id)->where('is_active', true)->first();
        $car->update(['rollcall_status' => 1]);
        return $this;
    }

    public function edit($data = [])
    {
        $endAt = Carbon::parse($this->created_at);
        $endAt->hour = $data['endHour'];
        $endAt->minute = $data['endMin'];
        $endAt->second = 0;

        $this->update([
            'end_lat' => 36.3,
            'end_lon' => 59.6,
            'end_at' => $endAt,
        ]);

        $this->histories()->create([
            'user_id'     => auth()->id(),
            'description' => $data['description'],
            'end_at'      => $endAt
        ]);

        $car = Car::where('user_id', $this->user_id)->where('is_active', true)->first();
        $car->update(['rollcall_status' => 1]);
        return $this;
    }

}
