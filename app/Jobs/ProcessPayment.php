<?php

namespace App\Jobs;

use App\Models\AsanPardakht;
use App\Models\BazistWallet;
use App\Models\Car;
use App\Models\Fava;
use App\Models\Firebase;
use App\Models\User;
use App\Models\Recyclable;
use App\Models\Referrer;
use App\Models\Submit;
use App\Models\Wallet;
use App\Notifications\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Kavenegar\KavenegarApi;


class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $driver;
    protected $submit;
    protected $user_rrn;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $submit,$user_rrn)
    {
        $this->user     = $user;
        $this->submit   = $submit;
        $this->driver   = $submit->driver;
        $this->user_rrn = $user_rrn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $receives = [];
        $user = $this->user;


        $user_rrn = $this->user_rrn ?? 13287 . $this->driver->id;
        $this->driver->update([
            'user_bank_code' => $user_rrn,
        ]);

        /*$data = [
            'title' => 'بازیست - جمع آوری درخواست',
            'message' => $user->name.' عزیز، درخواست شما با موفقیت جمع آوری شد',
        ];
        Notification::send($user, new UserNotification(Firebase::dataFormat($data)));*/

    }
}
