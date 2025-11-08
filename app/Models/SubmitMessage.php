<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmitMessage extends Model
{
    use HasFactory;
    use RecordsActivity;
    protected $fillable = [
        'user_id', 'submit_id', 'text', 'admin_seen', 'driver_seen'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function submit()
    {
        return $this->belongsTo(Submit::class);
    }

    public static function driverMessages()
    {
        return [
            1 => 'نامناسب بودن وضعیت پسماند',
            2 => 'عدم پاسخگویی شهروند در زمان مراجعه',
            3 => 'موکول به زمان دیگر',
            4 => 'مسدود بودن آدرس',
            5 => 'آدرس اشتباه',
        ];
    }

    public static function operatorCancelMessages()
    {
        return [
            1 => 'نامناسب بودن وضعیت پسماند',
            2 => 'خرابی خودرو',
            3 => 'عدم پاسخگویی شهروند در زمان مراجعه',
            5 => 'موکول به زمان دیگر',
            4 => 'سایر موارد',
        ];
    }

    public static function driverMessagesApi()
    {
        return [
            [
                'value' => 1,
                'label' => 'نامناسب بودن وضعیت پسماند',
            ],
            [
                'value' => 2,
                'label' => 'عدم پاسخگویی شهروند در زمان مراجعه',
            ],
            [
                'value' => 3,
                'label' => 'موکول به زمان دیگر',
            ],
            [
                'value' => 4,
                'label' => 'مسدود بودن آدرس',
            ],
            [
                'value' => 5,
                'label' => 'آدرس اشتباه',
            ]
        ];
    }

    public static function operatorMessages()
    {
        return [
            1 => 'انجام شود',
            2 => 'با تاخیر انجام شود',
        ];
    }
}
