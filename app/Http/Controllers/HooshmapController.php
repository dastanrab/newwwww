<?php

namespace App\Http\Controllers;

use App\Classes\RequestSuggestionV2;
use App\Models\Address;
use App\Models\ArchiveApp;
use App\Models\ArchiveLegal;
use App\Models\ArchiveNotLegal;
use App\Models\ArchivePhone;
use App\Models\Day;
use App\Models\Hour;
use App\Models\Percentage;
use App\Models\Polygon;
use App\Models\PolygonDayHour;
use App\Models\ReceiveArchive;
use App\Models\Role;
use App\Models\Submit;
use App\Models\SubmitTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HooshmapController extends Controller
{
   public function health()
   {
       return response()->json(['status'=>200,'result'=>gmdate('Y-m-d H:i:s')]);
   }
   public function document(){
       return response()->json(['status'=>200,'result'=>gmdate('Y-m-d H:i:s')]);
   }
    public function prices(Request $request){
        if (request()->has('date') and isValidDateFormat(request()->input('date'))) {
            $date = request()->input('date');
        }
        else{
            $date = date('Y-m-d');
        }
        $prices=today_prices($date);
        $data=[];
        for($i=1;$i<=19;$i++)
        {
            $data[]=['GoodRef'=>$i,'Price'=>($prices->$i*50)/100];
        }

        return response()->json(['status'=>200,'result'=>$data]);
    }
    public function times(Request $request){
        $validator = Validator::make($request->all(), [
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>400,'result'=>'','message'=>'مختصات خود را وارد کنید']);
        }
        if (request()->has('date') and isValidDateFormat(request()->input('date'))) {
            $date = request()->input('date');
        }else {
            $date = date('Y-m-d');
        }
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        return response()->json(['status'=>200,'result'=>schedule($lat,$lng,$date)]);
    }
    public function status(Request $request){
        $validator = Validator::make($request->all(), [
            'OrderId' => ['required', 'numeric', 'exists:hoosh_map_orders,hoosh_order_id'],
            'RequestId' => ['nullable', 'numeric', 'exists:submits,id'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>400,'result'=>'','message'=>'شناسه سفارش یا شناسه درخواست را وارد کنید']);
        }
        if ($request->query('RequestId') !== null) {
            $status=Submit::query()->where('id',$request->query('RequestId'))->first()->status;


        }else{
            $submit_id=DB::table('hoosh_map_orders')->where('hoosh_order_id',$request->query('OrderId'))->first()->submit_id;
            $status=Submit::query()->where('id',$submit_id)->first()->status;
        }
        $info=$this->hoosh_ordeer_statuses($status);
        return response()->json(['status'=>200,'result'=>['Message'=>$info[1],'StatusRef'=>$info[0]]]);
    }
    public function cancel(Request $request){
        $validator = Validator::make($request->all(), [
            'OrderId' => ['required', 'numeric', 'exists:hoosh_map_orders,hoosh_order_id'],
            'RequestId' => ['nullable', 'numeric', 'exists:submits,id'],
            'Message'=>['nullable', 'string']
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>400,'result'=>'','message'=>'شناسه سفارش یا شناسه درخواست را وارد کنید']);
        }
        if ($request->query('RequestId') !== null) {
            $submit_id=$request->query('RequestId');
        }else{
            $submit_id=DB::table('hoosh_map_orders')->where('hoosh_order_id',$request->input('OrderId'))->first()->submit_id;
        }
        $submit=Submit::query()->where('id',$submit_id)->where('status',1)->first();
        if(!$submit){
            return response()->json(['status'=>200,'result'=>['Message'=>'درخواست در وضعیت قابل لغو نیست','StatusRef'=>5]]);
        }
        try {
            DB::beginTransaction();
            $suggest=new RequestSuggestionV2(0);
            $suggest->cancelDriversSubmit($submit->id);
            $submit->status = 5;
            $submit->canceled_at = now()->format('Y-m-d H:i:s');
            $submit->save();
            $archive = ReceiveArchive::where('date', Carbon::parse($submit->start_deadline)->format('Y-m-d'))->where('type', 1)->first();
            if($archive) {
                $archive->update([
                    'submit_delete' => $archive->submit_delete + 1,
                ]);
            }
            else{
                ReceiveArchive::create([
                    'date' => now()->format('Y-m-d'),
                    'submit_delete' => 1,
                    'type' => 1,
                ]);
            }
            if ($submit->user->legal) {
                ArchiveLegal::submitDelete($submit);
            } else {
                ArchiveNotLegal::submitDelete($submit);
            }
            if ($submit->submit_phone) {
                ArchivePhone::submitDelete($submit);
            } else {
                ArchiveApp::submitDelete($submit);
            }
            DB::commit();
            return response()->json(['status'=>200,'result'=>['Message'=>'درخواست با موفقیت کنسل شد','StatusRef'=>1]]);
        }catch (\Exception $exception)
        {
            DB::rollBack();
            return response()->json(['status'=>500,'result'=>['Message'=>'خطا در حذف درخواست ','StatusRef'=>0]]);

        }



    }
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'RequestDate' => 'required|date_format:Y-m-d',
            'OrderId' => 'required|numeric|unique:hoosh_map_orders,hoosh_order_id',
            'DeadlineFromDateTime' => 'required|date_format:Hi',
            'DeadlineToDateTime' => 'required|date_format:Hi',
            'CreateDate' => 'required|date_format:Y-m-d H:i:s',
            'Lat' => 'required|numeric|between:-90,90',
            'Long' => 'required|numeric|between:-180,180',
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'Mobile' => ['required', 'regex:/^09\d{9}$/'],
            'IsLegal' => 'required|boolean',
            'GuildTypeRef' => 'nullable|exists:guilds,id',
            'GuildTitle' => 'nullable|string|max:255',
            'Address' => 'required|string|max:1000',
            'Region' => 'required|integer',
            'District' => 'required|integer',
            'Message' => 'nullable|string|max:1000',
        ],$this->validation_messages());

        $validator->sometimes(['GuildTypeRef', 'GuildTitle'], 'required', function ($input) {
            return $input->IsLegal == 1;
        });
        if($validator->fails()){
            return response()->json(['status'=>400,'result'=>'','message'=>$validator->messages()->first()]);

        }
        $start_hour=get_start_date($request->DeadlineFromDateTime);
        $date = Carbon::parse($request->RequestDate);
        $hour = (int) $start_hour;
        $now = Carbon::now();
        if ($date->isBefore($now->copy()->startOfDay())) {
            return response()->json(['status'=>403,'result'=>'','message'=>'از تاریخ مورد نظر گذشته است']);
        }

        if ($date->isToday() && $hour < $now->hour) {
            return response()->json(['status'=>403,'result'=>'','message'=>'از بازه مورد نظر عبور کرده ایم']);
        }
        $user=$this->create_user($request->all());
        if(Submit::where('user_id', $user->id)->where('status', 1)->first()){
            return response()->json(['status'=>403,'result'=>'','message'=>'شما یک درخواست فعال دارید']);
        }
        if($request->Instant == 1 && SubmitTime::find(1)->instant == 0){
            return response()->json(['status'=>403,'result'=>'','message'=>'درحال حاضر ثبت درخواست فوری غیرفعال می باشد.']);
        }
        if($request->Instant == 1 && SubmitTime::find(1)->instant == 0){
            return response()->json(['status'=>403,'result'=>'','message'=>'درحال حاضر ثبت درخواست فوری غیرفعال می باشد.']);
        }
        if($request->Instant == 1 && !Submit::immediateValidate(verta()->format('G'))){
            return response()->json(['status'=>403,'result'=>'','message'=>'درحال حاضر ثبت درخواست فوری در این بازه غیرفعال می باشد.']);
        }
        $address_id=$this->create_address($user->id,$request->only(["Address","Region","District","Lat","Long"]));
        if(isset($request->RequestDate) && !$this->scheduleValidation($address_id,$request->RequestDate,$start_hour,$user)){
            return response()->json(['status'=>403,'result'=>'','message'=>'ثبت درخواست در زمان انتخاب شده امکان پذیر نمی باشد.']);
        }
        $submit=$this->addSubmit($user->id,$request->RequestDate,$start_hour,$user,$address_id,$request->Instant,$request->OrderId);

        return response()->json(['status'=>200,'result'=>['submit'=>$submit],'message'=>'درخواست با موفقیت ایجاد شد']);

    }
    private function create_user($data)
    {

        $user=User::query()->where('mobile',$data['Mobile'])->first();
        if ($user) {
            return $user;
        }else{
            try {

                return DB::transaction(function () use ($data) {
                    $user=User::query()->create([
                        'mobile' => $data['Mobile'],
                        'name' => $data['FirstName'],
                        'lastname' => $data['LastName'],
                        'legal' => (int) $data['IsLegal'],
                        'guild_id' => (int) $data['GuildTypeRef'],
                        'guild_title' => $data['GuildTitle'],
                        'password' => Hash::make($data['Mobile']),
                        'city_id'=>1
                    ]);
                    $user->roles()->sync([Role::where('name','user')->first()->id]);
                    $user->wallets()->create(['wallet' => 0]);
                    return $user;
                });
            } catch (\Exception $e) {
                response()->json(['status'=>500,'result'=>'','message'=>'خطا در ایجاد کاربر'])->send();
                exit;
            }
        }
    }
    private function create_address($user_id,$data)
    {
        try {
            return DB::transaction(function () use ($data,$user_id) {
                return Address::query()->create(
                        ['title'=>'-','user_id'=>$user_id,'address'=>$data['Address'],'region'=>$data['Region'],'district'=>$data['District'],'lat'=>$data['Lat'],'lon'=>$data['Long'],'city_id'=>1]
                )->id;
            });
        } catch (\Exception $e) {
            response()->json(['status'=>500,'result'=>'','message'=>'خطا در ایجاد آدرس'])->send();
            exit;
        }
    }
    private function scheduleValidation($addressId,$day,$hour,$user){
        $dayOfWeek = Day::find(verta()->parse($day)->format('w')+1);
        $hourOfDay = Hour::where('start_at',$hour)->first();
        $address = Address::find($addressId);
        $district = xDistrict([$address->lat,$address->lon]);
        $polygon = Polygon::where('region',$district)->first();
        if (!isset($polygon->id))
        {
            return false;
        }
        $polygonDayHours = PolygonDayHour::query()->where('city_id',$user->city_id)->where('polygon_id',$polygon->id)->where('day_id',$dayOfWeek->id)->where('hour_id',$hourOfDay->id)->first();
        if(!$polygonDayHours->status){
            return false;
        }
        elseif(verta()->parse($day)->isToday() && verta()->format('G') >= $hour){
            return false;
        }
        return true;
    }
    private function addSubmit($registrantId,$day,$start_hour,$user,$address_id,$isInstant,$order_id){
        if($isInstant == 1){
            $start_deadline = now()->format('Y-m-d H:i:s');
            $end_deadline = now()->addHour()->format('Y-m-d H:i:s');
            $is_instant = 1;
        }
        else{
            $start_deadline = Carbon::createFromFormat('Y-m-d H:i:s', $day.' 00:00:00')->hour($start_hour)->toDateTimeString();
            $end_deadline = Carbon::createFromFormat('Y-m-d H:i:s', $day.' 00:00:00')->hour($start_hour+3)->toDateTimeString();
            $is_instant = 0;
        }
        $address = Address::find($address_id);
        $district = getAddressRegion([$address->lat,$address->lon]);
        try {
            DB::beginTransaction();
            $submit = new Submit;
            $submit->registrant_id = $registrantId;
            $submit->user_id = $user->id;
            $submit->start_deadline = $start_deadline;
            $submit->end_deadline = $end_deadline;
            $submit->is_instant = $is_instant;
            $submit->recyclables = json_encode(['GoodRef' => 1, 'Quantity' => 1, 'Price' => Percentage::where('recyclable_id', 1)->where('is_legal', false)->where('weight', 1)->first()->price * 10]);
            $city_id = $address->city_id;
            $submit->address_id = $address->id;
            $submit->region_id= @$district;
            $submit->city_id = $city_id;
            $submit->cashout_type = 'bazist';
            $submit->cashout_instant = 0;
            $submit->submit_phone = 0;
            $submit->type=1;
            $submit->save();
            DB::table('hoosh_map_orders')->insert(['hoosh_order_id'=>$order_id,'submit_id'=>$submit->id,'created_at'=>now(),'updated_at'=>now()]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            response()->json(['status'=>500,'result'=>'','message'=>'خطا در ایجاد درخواست'])->send();
            exit;
        }


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
        return $submit;
    }
    private function validation_messages()
    {
        return [
            'RequestDate.required' => 'تاریخ درخواست الزامی است.',
            'RequestDate.date_format' => 'فرمت تاریخ درخواست معتبر نیست (مثلاً: 1402-01-15).',
            'OrderId.required' => 'کد سفارش الزامی است.',
            'OrderId.numeric' => 'کد سفارش باید عددی باشد.',
            'DeadlineFromDateTime.required' => 'زمان شروع بازه الزامی است.',
            'DeadlineFromDateTime.date_format' => 'فرمت زمان شروع بازه باید به صورت ساعت و دقیقه باشد (مثلاً: 0900).',
            'DeadlineToDateTime.required' => 'زمان پایان بازه الزامی است.',
            'DeadlineToDateTime.date_format' => 'فرمت زمان پایان بازه باید به صورت ساعت و دقیقه باشد (مثلاً: 1200).',
            'CreateDate.required' => 'تاریخ ایجاد الزامی است.',
            'CreateDate.date_format' => 'فرمت تاریخ ایجاد معتبر نیست (مثلاً: 1402-01-15 12:00:00).',
            'Lat.required' => 'عرض جغرافیایی الزامی است.',
            'Lat.numeric' => 'عرض جغرافیایی باید عددی باشد.',
            'Lat.between' => 'عرض جغرافیایی باید بین -90 تا 90 باشد.',
            'Long.required' => 'طول جغرافیایی الزامی است.',
            'Long.numeric' => 'طول جغرافیایی باید عددی باشد.',
            'Long.between' => 'طول جغرافیایی باید بین -180 تا 180 باشد.',
            'FirstName.required' => 'نام الزامی است.',
            'LastName.required' => 'نام خانوادگی الزامی است.',
            'Mobile.required' => 'شماره موبایل الزامی است.',
            'Mobile.regex' => 'فرمت شماره موبایل معتبر نیست (مثلاً: 09123456789).',
            'IsLegal.required' => 'وضعیت حقیقی/حقوقی الزامی است.',
            'IsLegal.boolean' => 'مقدار وضعیت حقوقی باید فقط 0 یا 1 باشد.',
            'GuildTypeRef.required' => 'نوع صنف برای اشخاص حقوقی الزامی است.',
            'GuildTitle.required' => 'عنوان صنف برای اشخاص حقوقی الزامی است.',
            'Address.required' => 'آدرس الزامی است.',
            'Region.required' => 'ناحیه الزامی است.',
            'District.required' => 'منطقه الزامی است.',
        ];
    }
    private function hoosh_ordeer_statuses($status_id)
    {
        $hoosh_statuses=[1=>[1,'درخواست در انتظار قبول توسط راننده'],2=>[2,'درخواست توسط راننده قبول شد و راننده در اسرع وقت جهت تحویل پسماند نزد شما می آید'],3=>[4,'درخواست جمع آوری شده است'],4=>[5,'درخواست توسط راننده کنسل شد'],5=>[6,'درخواست توسط شهروند کنسل شد']];
        return $hoosh_statuses[$status_id];
    }
}
