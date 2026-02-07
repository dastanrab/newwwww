<?php

namespace App\Livewire\Dashboard\Submits;

use App\Classes\RequestSuggestionV2;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Fava;
use App\Models\Submit;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubmitMapIndexModal extends Component
{
    public $submits;
    public $name;
    public $driverId;
    public $firstSubmitText = 'تماس گرفته شد';
    public function render()
    {
        return view('livewire.dashboard.submits.submit-map-index-modal');
    }

    #[Computed]
    public function drivers()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query) {
            $query->where('is_active', 1);
        })->get();
    }

    public function updateDriver(Submit $submit)
    {

        if ($submit->status > 1) {
            return sendToast(0,'انتقال درخواست امکان پذیر نمی باشد');
        }
        if (!isset($this->driverId)) {
            return sendToast(0,'راننده ای برای انتقال انتخاب نکرده اید');

        }
        $status=Driver::query()->where('submit_id',$submit->id)->first();
        if ($status and $status->status == 3)
        {
            return sendToast(0,'این درخواست قبلا ثبت شده است');
        }
        if (in_array($this->driverId,test_drivers()))
        {
            $suggest=new RequestSuggestionV2($this->driverId);
            $suggest->regenerate_suggestion($submit->id);
        }
        $submit->update(['status' => 2]);
        if($submit->driver) {
            $driver = $submit->driver;
            $driver->user_id = $this->driverId;
            $driver->car_id = Car::where('user_id', $this->driverId)->where('is_active', true)->first()->id;
            $driver->submit_id = $submit->id;
            $driver->status = 2;
            $driver->save();
        }
        else{
            $pakban = new Driver;
            $pakban->user_id = $this->driverId;
            $pakban->car_id = Car::where('user_id', $this->driverId)->where('is_active', true)->first()->id;
            $pakban->submit_id = $submit->id;
            $pakban->status = 2;
            $pakban->save();
            Storage::put('driver-map.txt', date('Y-m-d H:i:s').'-'.$pakban->user_id);
        }

        if ($submit->city_id == 1)
        {
            //Fava::updateRequest($submit->fava_id, 2);
        }


        $user = User::find($submit->user_id);
        //Notification::send($user, new PakbanAccepted());

        sendToast(1,'درخواست انتقال یافت');
        $this->redirectRoute('d.submits.map');
    }

    public function removeFirstSubmit(Submit $submit)
    {
        $submit->flag = 1;
        $submit->save();
        sendToast(1,'تا لود دوباره نقشه منتظر بمانید...');
        $this->js('window.location.reload()');
    }
}
