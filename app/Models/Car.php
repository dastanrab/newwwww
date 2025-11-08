<?php

namespace App\Models;

use App\Events\UserEvent;
use App\Models\Driver;
use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = ['fava_id', 'user_id', 'plaque', 'plaque_1', 'plaque_2', 'plaque_3', 'plaque_4', 'type', 'type_id', 'is_active', 'rollcall_status', 'ip'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public static function alphabet()
    {
        return ['الف', 'ب', 'پ', 'ت', 'ث', 'ج', 'چ', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'ژ', 'س', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن', 'و', 'ه', 'ی'];
    }

    public static function types()
    {
        return collect([
            (object)[
                'name' => 1,
                'label' => 'آریسان',
            ],
            (object)[
                'name' => 2,
                'label' => 'پیکان وانت',
            ],
            (object)[
                'name' => 3,
                'label' => 'پراید 151(وانت)',
            ],
            (object)[
                'name' => 4,
                'label' => 'مزدا وانت',
            ],
            (object)[
                'name' => 5,
                'label' => 'نیسان وانت',
            ],
            (object)[
                'name' => 6,
                'label' => 'فوتون',
            ],
            (object)[
                'name' => 7,
                'label' => 'کروس',
            ],
            (object)[
                'name' => 8,
                'label' => 'ایسوزو',
            ],
            (object)[
                'name' => 9,
                'label' => 'آمیکو',
            ]
        ]);
    }

    public static function insert($data = [])
    {
        $driver = User::find($data['driverId']);
        $driver->roles()->sync([Role::getRoleId('driver')]);
        event(new UserEvent('گروه کاربری کاربر با آی‌دی ' . $driver->id . ' به «راننده» تغییر پیدا کرد.'));
        $plaque = $data['plaque1'] . '/' . $data['plaque2'] . '/' . $data['plaque3'] . '/' . $data['plaque4'];

        $car = $driver->cars()->create([
            'plaque'    => $plaque,
            'plaque_1'  => $data['plaque1'],
            'plaque_2'  => $data['plaque2'],
            'plaque_3'  => $data['plaque3'],
            'plaque_4'  => $data['plaque4'],
            'type'      => Car::types()->where('name',$data['type'])->first()->label,
            'type_id'   => $data['type'],
            'is_active' => $data['isActive'],
        ]);

        $location = new Location;
        $location->car_id = $car->id;
        $location->lat = 36.3341329;
        $location->long = 59.5949651;
        $location->save();

        $first_pakban = Driver::first();
        $warehouse = new WarehouseDaily;
        $warehouse->user_id = $driver->id;
        $warehouse->weight = 0;
        $warehouse->operator_id = auth()->id();
        $warehouse->created_at = now();
        $warehouse->save();
        return $car;
    }
}
