<?php

namespace App\Livewire\Dashboard\Submits;

use App\Classes\RequestSuggestionV2;
use App\Models\Car;
use App\Models\City;
use App\Models\Fava;
use App\Models\Firebase;
use App\Models\Polygon;
use App\Models\Submit;
use App\Models\SubmitMessage;
use App\Models\User;
use App\Models\Driver;
use App\Notifications\UserNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SubmitAllIndexList extends Component
{
    use WithPagination;
    #[Url]
    public $status;
    #[Url]
    public $time;
    #[Url]
    public $search;
    #[Url]
    public $driver;
    #[Url]
    public $sort;
    public $row = 7;
    public $toDriver;
    public $isEmergency;
    public $messageId;
    public $text;
    public $polygons;
    private  $city_id = 1;

    public function boot()
    {
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=City::all()->pluck('id')->toArray();
        }
        else{
            $this->city_id=[$this->city_id];
        }
        $this->polygons = Polygon::all();
    }

    public function render()
    {
        return view('livewire.dashboard.submits.submit-all-index-list');
    }

    #[Computed]
    public function drivers()
    {
        return User::with('roles')->whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })->whereHas('cars', function ($query) {
            $query->where('is_active', 1);
        })->orderBy('created_at', 'ASC')->get();
    }

    #[Computed]
    public function messages()
    {
        return SubmitMessage::operatorMessages();
    }

    #[Computed]
    public function submits()
    {
        $this->time = $this->time ? (int)$this->time : $this->time;
        $city = $this->city_id ;
        $query = Submit::whereIn('submits.city_id',$city);
        if($this->search){
            if (is_numeric($this->search)) {
                $query->whereHas('user', function($q){
                    $q->where('mobile', 'LIKE', "%{$this->search}%");
                });
            } else {
                $query->whereHas('user', function($q){
                    $q->whereRaw("CONCAT(name, ' ', lastname) LIKE '%{$this->search}%'");
                });
            }
        }
        if($this->status == 'active'){
            if($this->time){
                $query = $query->where('submits.start_deadline', now()->hour($this->time)->startOfHour());
            }
            else{
                $query = $query->whereDate('submits.start_deadline', '<=', Carbon::today());
            }
            $query = $query->where('submits.status', 2);
        }
        elseif($this->status == 'tomorrow'){

            if($this->time){
                $query = $query->where('submits.start_deadline', '>=', Carbon::tomorrow()->hour($this->time)->startOfHour());
            }
            else{
                $query = $query->whereDate('submits.start_deadline', '>=', Carbon::tomorrow());
            }
            $query = $query->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere('status', 2);
            });
        }
        elseif($this->status == 'done'){
            $query = $query->whereDate('start_deadline', Carbon::today());

            if($this->time){
                $query = $query->where('submits.start_deadline', Carbon::today()->hour($this->time)->startOfHour());
            }
            else{
                $query = $query->whereDate('start_deadline', Carbon::today());
            }
            $query = $query->where('submits.status', 3)->join('drivers', 'drivers.submit_id', '=', 'submits.id')
                ->select('submits.*')
                ->with('user')->with(['address' => function ($query) {
                    $query->withTrashed();
                }]);

            if($this->sort == 'firstCollection'){
                $query = $query->orderBy('drivers.collected_at');
            }
            elseif($this->sort == 'firstCollection'){
                $query = $query->orderBy('drivers.collected_at');
            }
            elseif($this->sort == 'mostWeight'){
                $query = $query->orderBy('drivers.weights', 'DESC');
            }
            elseif($this->sort == 'lowestWeight'){
                $query = $query->orderBy('drivers.weights', 'ASC');
            }
            else{
                $query = $query->orderBy('drivers.collected_at', 'desc');
            }

        }
        else{
            if($this->time){
                $query = $query->where('submits.start_deadline', now()->hour($this->time)->startOfHour());
            }
            else{
                $query = $query->whereDate('submits.start_deadline', '<=', Carbon::today());
            }
            $query = $query->where('submits.status', 1);
        }
        if($this->driver){
            $query = $query->whereHas('drivers.user', function (Builder $query) {
                $query->where(function ($query) {
                    $query->where('id', $this->driver);
                });
            });
        }

        $query = $query->with('user', 'drivers')->with(['address' => function ($query) {
            $query->withTrashed();
        }]);
        //dd($query->toRawSql());
        if($this->status != 'done'){
            $query = $query->orderBy('submits.end_deadline');
        }
        return $query->paginate($this->row);

    }

    public function changeDriver(Submit $submit)
    {
        $this->validate([
            'toDriver' => 'required|exists:users,id'
        ],
        [
            'toDriver' => 'راننده را به درستی انتخاب کنید'
        ]);
        $status=Driver::query()->where('submit_id',$submit->id)->first();
        if ($status and $status->status == 3)
        {
           return sendToast(0,'این درخواست قبلا ثبت شده است');
        }
        /*$drivers = Driver::where('user_id', $this->toDriver)->where('status', 2)->count();
        if ($drivers >= 4) {
            sendToast(0,'این راننده ۴ درخواست فعال دارد');
            return;
        }*/


        if (in_array($this->toDriver,test_drivers()))
        {
            $suggest=new RequestSuggestionV2($this->toDriver);
            $suggest->regenerate_suggestion($submit->id);
        }
        $submit->update(['status' => 2]);
        if($submit->driver) {
            $submit->driver->update([
                'user_id' => $this->toDriver,
                'car_id' => Car::where('user_id', $this->toDriver)->where('is_active', true)->first()->id,
                'status' => 2,
            ]);
        }
        else{
            Driver::create([
                'user_id' => $this->toDriver,
                'car_id' => Car::where('user_id', $this->toDriver)->where('is_active', true)->first()->id,
                'submit_id' => $submit->id,
                'status' => 2,
                'city_id' => $submit->city_id,
            ]);
            $submit = Submit::find($submit->id);
            Storage::put('driver-submitAll.txt', date('Y-m-d H:i:s').'-'.$this->toDriver);
        }
        if ($submit->city_id == 1) {
            Fava::updateRequest($submit->fava_id, 2);
        }

        /*$data = [
            'title' => 'درخواست شما تایید شد',
            'message' => $submit->user->name.' عزیز، در زمان مقرر درخواست شما جمع آوری خواهد شد',
        ];
        Notification::send($submit->user, new UserNotification(Firebase::dataFormat($data)));*/

        /*$data = [
            'title' => 'درخواستی به شما تعلق گرفت',
            'message' => $submit->driver->user->name.' عزیز، لطفا در زمان مقرر درخواست را جمع آوری کنید',
        ];
        Notification::send($submit->driver->user, new UserNotification(Firebase::dataFormat($data),'driver-app'));*/
        $this->dispatch('remove-modal');
        sendToast(1,'راننده تغییر یافت');
    }

    public function storeMessage(Submit $submit)
    {
        $user = auth()->user();
        $messages = SubmitMessage::operatorMessages();
        if($this->text){
            $text = $this->text;
        }
        else{
            $text = $messages[$this->messageId];
        }
        $save = $submit->messages()->create([
            'user_id' => $user->id,
            'text' => $text,
        ]);
        if($save){
            $submit->messages()->where('user_id','=',$submit->driver->user_id)->where('admin_seen',0)->update(['admin_seen' => 1]);
            sendToast(1,'پیام ثبت شد');
            $this->reset('messageId');
            $this->reset('text');
            return;
        }
        sendToast(0,'ثبت پیام با اشکال روبرو شد');
    }

    #[On('submit-cancel')]
    public function submitCancel(Submit $submit, $messageId)
    {
        if(!$messageId){
            return sendToast(0,'پیام را مشخص نمایید');
        }
        $operator = auth()->user();
        //$messageId = 4;
        $messages = SubmitMessage::operatorCancelMessages();
        $driver = Driver::where('submit_id', $submit->id)->with('receives')->first();
        /*if ($submit->status !== 2) {
            sendToast(0,'درخواست های فعال قابلیت لغو دارند');
            return;
        }*/

        if($driver) {
            $driver->status = 4;
            $driver->save();
            foreach ($driver->receives as $receive) {
                $receive->delete();
            }
        }

        $submit->status = 4;
        $submit->total_amount = 0;
        $submit->cancel = $messages[$messageId];
        $submit->canceller_id = $operator->id;
        $submit->canceled_at = now()->format('Y-m-d H:i:s');
        $submit->save();
        $suggest=new RequestSuggestionV2(0);
        $suggest->cancelDriversSubmit($submit->id);
        if ($submit->city_id == 1)
        {
            Fava::updateRequest($submit->fava_id, 5, 4);
        }

        if(0) {
            // تکیف این بخش چیه؟؟
            $submit_new = new Submit;
            $submit_new->user_id = $submit->user_id;
            $submit_new->start_deadline = $submit->start_deadline;
            $submit_new->end_deadline = $submit->end_deadline;
            $submit_new->recyclables = $submit->recyclables;
            $submit_new->address_id = $submit->address_id;
            $submit_new->save();

            $last_submit = Submit::where('user_id', $submit->user_id)->latest()->with('address')->first();
            $user = User::where('id', $submit->user_id)->first();

            $url = 'https://msb.mashhad.ir/IWMS/Database/Proxy/InsertRequestService_db?wsdl';

            $RequestDate = new Carbon($last_submit->created_at);
            $RequestDate = $RequestDate->toIso8601String();
            $DeadlineFromDateTime = new Carbon($last_submit->start_deadline);
            $DeadlineFromDateTime = $DeadlineFromDateTime->toIso8601String();
            $DeadlineToDateTime = new Carbon($last_submit->end_deadline);
            $DeadlineToDateTime = $DeadlineToDateTime->toIso8601String();

            $parameters = [
                'RequestId' => $last_submit->id,
                'CustomerRef' => $user->fava_id,
                'RequetDate' => $RequestDate,
                'DeadlineFromDateTime' => $DeadlineFromDateTime,
                'DeadlineToDateTime' => $DeadlineToDateTime,
                'StatusRef' => 1,
                'DriverCancellationReasonRef' => null,
                'Lat' => $last_submit->address->lat,
                'Long' => $last_submit->address->lon,
                'CarRef' => null,
                'Address' => $last_submit->address->address,
                'Region' => $last_submit->address->region,
                'District' => $last_submit->address->district,
                'DetailJson' => $last_submit->recyclables,
            ];

            $fava = fava($url, 'InsertRequestService', array($parameters));

            $submit_new->fava_id = $fava->RowSet0->RowSet0_Row->HeaderId;

            $submit_new->save();
        }
        $this->dispatch('remove-modal');

    }


    #[On('search')]
    public function search($search)
    {
        $this->resetPage();
        $this->search = $search;
    }
    #[On('city')]
    public function city($city)
    {
        $this->resetPage();
        $this->city_id = session('city',1) ;
        if ($this->city_id == 0){
            $this->city_id=City::all()->pluck('id')->toArray();
        }
        else{
            $this->city_id=[$this->city_id];
        }
    }

    #[On('status')]
    public function status($status)
    {
        $this->resetPage();
        $this->dispatch('reset-time');
        $this->dispatch('reset-sort');
        $this->status = $status;
    }

    #[On('time')]
    public function time($time)
    {
        $this->resetPage();
        $this->time = $time;
    }

    #[On('driver')]
    public function driver($driver)
    {
        $this->resetPage();
        $this->driver = $driver;
    }
    #[On('sort')]
    public function sort($sort)
    {
        $this->resetPage();
        $this->sort = $sort;
    }

    #[On('row')]
    public function row($row)
    {
        $this->resetPage();
        if(isset($_COOKIE['table-row'])) unset($_COOKIE['table-row']);
        setcookie('table-row',$row,time() + ((86400 * 365))*10, "/"); // 86400 = 1 day
        $this->row = $row;
    }
}
