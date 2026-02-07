<?php

namespace App\Livewire\Dashboard\Submits;

use App\Models\Address;
use App\Models\ArchiveApp;
use App\Models\ArchiveLegal;
use App\Models\ArchiveNotLegal;
use App\Models\ArchivePhone;
use App\Models\Day;
use App\Models\Hour;
use App\Models\Iban;
use App\Models\Percentage;
use App\Models\Polygon;
use App\Models\PolygonDayHour;
use App\Models\ReceiveArchive;
use App\Models\User;
use App\Models\Submit;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmitTelIndexForm extends Component
{
    #[Url]
    public $userId;
    public $user;
    public $card;
    public $cashout = 'card';
    public $address;
    public $cards;
    public $addresses;
    public $weeks;
    public $week;
    public $hour;
    public $hours;
    public $submitType = 'time';

    public function mount()
    {
        $this->user = User::find($this->userId);
        $this->cards = $this->getCards();
        $this->addresses = $this->getAddresses();
        $this->weeks = $this->getWeeks();
        $this->hours = $this->getHours();
    }

    public function render()
    {
        return view('livewire.dashboard.submits.submit-tel-index-form');
    }

    public function getCards()
    {
        return Iban::where('user_id',$this->userId)->get();
    }

    public function getAddresses()
    {
        return Address::where('user_id',$this->userId)->whereIn('status',[1,2])->get();
    }

    public function getWeeks()
    {
        $arr = [];
        for ($i=0;$i<7;$i++){
            $arr[] = (object)[
                'key' => verta()->addDays($i)->format('Y-m-d'),
                'title' => verta()->addDays($i)->format('l n/j'),
            ];
        }
        return $arr;
    }
    public function getHours($after = 0)
    {
        $arr = [];
        $res = [];
        $arr[] = (object)[
            'key' => '9',
            'title' => '9-12',
        ];
        $arr[] = (object)[
            'key' => '11',
            'title' => '11-14',
        ];
        $arr[] = (object)[
            'key' => '13',
            'title' => '13-16',
        ];
        $arr[] = (object)[
            'key' => '15',
            'title' => '15-18',
        ];
        $arr[] = (object)[
            'key' => '17',
            'title' => '17-20',
        ];

        if($after){
            foreach ($arr as $item){
                if($item->key > $after){
                    $res[] = $item;
                }
            }
        }

        else{
            $res = $arr;
        }
        return $res;
    }

    public function store()
    {
        $user = auth()->user();
        $this->validate([
            'card' => $this->cashout == 'card' ? 'required|exists:ibans,id' : 'nullable',
            'address' => 'required|exists:addresses,id',
            'cashout' => 'required|in:card,bazist,aap',
            'submitType' => 'required|in:instant,time',
            'week' => $this->submitType == 'time' ? 'required' : 'nullable',
            'hour' => $this->submitType == 'time' ? 'required' : 'nullable',
        ],
        [
            'card' => 'کارت را وارد نمایید',
            'cashout' => 'نوع واریزی را مشخص نمایید',
            'week' => 'زمان تحویل را مشخص نمایید',
            'hour' => 'ساعت تحویل را مشخص نمایید'
        ]);
        $city_id = User::cityId();
        $address = Address::find($this->address);
        /*$findRegion = bazistDistrict([$address->lat, $address->lon]);
        if(!$findRegion){
            sendToast(0,'متاسفانه فعلا در این آدرس فعال نیستیم');
            return;
        }*/
        if ($this->submitType == 'time') {
            /*if (verta()->format('Y-m-d') == $this->week && verta()->format('H') >= $this->hour) {
                sendToast(0, 'در این زمان امکان ثبت درخواست ممکن نمی باشد');
                return;
            }*/
            $start_deadline = verta()->parse($this->week.' '.$this->hour.':00')->formatGregorian('Y-m-d H:i:s');
            $end_deadline = Carbon::parse($start_deadline)->addHours(3);
            $is_instant = false;
            /*$daysOfWeek = verta()->parse($this->week)->format('N');
            $findPolygon = Polygon::where('region', $findRegion)->first(); //
            $findDay = Day::find($daysOfWeek);
            $findHour = Hour::where('start_at', $this->hour)->first();
            $polygonDayHour = PolygonDayHour::where([
                'city_id' => $city_id,
                'polygon_id' => $findPolygon->id,
                'day_id' => $findDay->id,
                'hour_id' => $findHour->id,
            ])->first();
            if ($polygonDayHour->status == 0) {
                sendToast(0, 'ثبت درخواست در زمان انتخاب شده ممکن نیست');
                return;
            }*/
        } else {
            $is_instant = true;
            $start_deadline = now();
            $end_deadline = now()->addhour();
        }
        $price_mobile = Percentage::where('recyclable_id', 1)->where('is_legal', false)->where('weight', 1)->first()->price;
        $recyclables_id = json_decode('[1]');
        $recyclables_price = json_decode('[' . $price_mobile . ']');
        $recyclables_weight = json_decode('[1]');
        $recyclables = [];
        foreach ($recyclables_id as $key => $value) {
            array_push($recyclables, ['GoodRef' => $value, 'Quantity' => $recyclables_weight[$key], 'Price' => $recyclables_price[$key] * 10]);
        }
        $district = getAddressRegion([$address->lat,$address->lon]);
        $submit = new Submit;
        $submit->registrant_id = $user->id;
        $submit->user_id = $this->userId;
        $submit->start_deadline = $start_deadline;
        $submit->end_deadline = $end_deadline;
        $submit->is_instant = $is_instant;
        $submit->recyclables = json_encode($recyclables);
        $submit->address_id = $address->id;
        $submit->region_id=@$district;
        $submit->city_id = $address->city_id;
        $submit->submit_phone = true;
        $submit->cashout_type = $this->cashout;
        $submit->iban_id = $this->cashout == 'card' ? $this->card : null;
        $submit->cashout_instant = $this->cashout == 'card';
        $submit->save();


            $archive_id = ReceiveArchive::new($submit);
            if ($user->legal) {
                ArchiveLegal::new($submit, $archive_id);
            } else {
                ArchiveNotLegal::new($submit, $archive_id);
            }
            if ($submit->submit_phone) {
                ArchivePhone::new($submit, $archive_id);
            } else {
                ArchiveApp::new($submit, $archive_id);
            }


        sendToast(1,'درخواست با موفقیت ایجاد شد');
        $this->redirectRoute('d.submits.tel');
    }

    #[On('reload-iban')]
    public function reloadIban()
    {
        $this->cards = $this->getCards();
    }

    #[On('reload-address')]
    public function reloadAddress()
    {
        $this->address = Address::latest()->first()->id;
        $this->addresses = $this->getAddresses();
    }

    #[On('card-remove')]
    public function cardRemove(Iban $card)
    {
        $card->delete();
        $this->cards = $this->getCards();
        $this->dispatch('reload-iban');
    }

    #[On('address-remove')]
    public function addressRemove(Address $address)
    {
        $address->delete();
        $this->addresses = $this->getAddresses();
        $this->dispatch('reload-iban');
    }


}
