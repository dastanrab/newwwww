<?php

namespace App\Http\Controllers\Api\User;

use App\Classes\RequestSuggestionV2;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\ArchiveApp;
use App\Models\ArchiveLegal;
use App\Models\ArchiveNotLegal;
use App\Models\ArchivePhone;
use App\Models\City;
use App\Models\Day;
use App\Models\Fava;
use App\Models\Hour;
use App\Models\Percentage;
use App\Models\Polygon;
use App\Models\PolygonDayHour;
use App\Models\ReceiveArchive;
use App\Models\Submit;
use App\Models\SubmitTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{

    /**
     * زمان‌بندی ثبت درخواست
     *
     * لیست روزها و بازه‌های زمانی مجاز برای جمع‌آوری پسماند
     * به‌همراه امکان ثبت درخواست فوری
     *
     * @group Request
     * @authenticated
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "",
     *   "data": {
     *     "list": [
     *       {
     *         "value": "1404/11/21",
     *         "label": "1404/11/21",
     *         "subLabel": "",
     *         "weekday": "امروز",
     *         "enabled": true,
     *         "hours": [
     *           {
     *             "value": "9",
     *             "label": "9 الی 12",
     *             "subLabel": "صبح",
     *             "enabled": true
     *           }
     *         ]
     *       }
     *     ],
     *     "immediate": true,
     *     "immediateText": "جمع‌آوری ۱ الی ۲ ساعته پسماند، بین ساعت ۹ تا ۱۷ هر روز"
     *   }
     * }
     */

    public function scheduling(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'addressId' => 'required|exists:addresses,id,user_id,'.$user->id,
            ],
            [
                'addressId.required' => 'لطفا آدرس را ارسال نمایید',
                'addressId.exists' => 'آدرسی برای شما با این مشخصات ثبت نشده است.',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }

        $address = Address::find($request->addressId);
        $data = Submit::schedule($user,$address);
        if(isset($data['status']) && $data['status'] == 'error'){
            return sendJson('error',$data['message']);
        }
        $data['immediateText'] = 'جمع‌آوری ۱ الی ۲ ساعته پسماند، بین ساعت ۹ تا ۱۷ هر روز';

        return sendJson('success', '', $data);
    }

    /**
     * ثبت درخواست جمع‌آوری
     *
     * کاربر می‌تواند درخواست را به‌صورت:
     * - فوری (immediate)
     * - یا زمان‌بندی‌شده (scheduled)
     * ثبت کند.
     *
     * @group Request
     * @authenticated
     *
     * @bodyParam addressId integer required شناسه آدرس انتخاب‌شده Example: 12
     *
     * @bodyParam payMethod string required روش پرداخت Example: card
     * @bodyParam cardId integer required_when:payMethod,card شناسه کارت بانکی Example: 3
     *
     * @bodyParam scheduling string required نوع زمان‌بندی Example: immediate
     * @bodyParam scheduling.day string required_when:scheduling,scheduled روز جمع‌آوری Example: 1404/11/22
     * @bodyParam scheduling.hour string required_when:scheduling,scheduled ساعت جمع‌آوری Example: 9
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "درخواست شما با موفقیت ثبت شد",
     *   "data": {
     *     "id": 336,
     *     "requestDate": {
     *       "day": "21 بهمن",
     *       "range": "9:00 الی 12:00"
     *     },
     *     "address": "دلاوران 7",
     *     "collectDate": null,
     *     "status": {
     *       "value": 1,
     *       "label": "در انتظار"
     *     },
     *     "driver": null,
     *     "cancelable": true
     *   }
     * }
     *
     * @response 200 {
     *   "status": "error",
     *   "message": "شما یک درخواست فعال دارید"
     * }
     */

    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'addressId' => 'required|exists:addresses,id,user_id,'.$user->id,
                'cardId'    => $request->payMethod == 'card' ? 'required|exists:ibans,id,user_id,'.$user->id : 'nullable',
                'payMethod' => 'required|in:aniroob,card',
                'scheduling.day' => $request->scheduling == 'immediate' ? 'nullable' : 'required',
                'scheduling.hour' => $request->scheduling == 'immediate' ? 'nullable' : 'required',
            ],
            [
                'addressId.required' => 'لطفا آدرس را ارسال نمایید',
                'addressId.exists' => 'آدرسی برای شما با این مشخصات ثبت نشده است.',
                'cardId' => 'شماره کارت انتخاب نشده است',
                'scheduling.day' => 'لطفا روز جمع آوری را انتخاب کنید',
                'scheduling.hour' => 'لطفا ساعت جمع آوری را انتخاب کنید',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        elseif(Submit::where('user_id', $user->id)->where('status', 1)->first()){
            return sendJson('error','شما یک درخواست فعال دارید');
        }
        elseif($request->scheduling == 'immediate' && SubmitTime::find(1)->instant == 0){
            return sendJson('error','درحال حاضر ثبت درخواست فوری غیرفعال می باشد.');
        }
        elseif($request->scheduling == 'immediate' && !Submit::immediateValidate(verta()->format('G'))){
            return sendJson('error','درحال حاضر ثبت درخواست فوری در این بازه غیرفعال می باشد.');
        }
        elseif(isset($request->scheduling['day']) && !Submit::scheduleValidation($request->addressId,$request->scheduling['day'],$request->scheduling['hour'])){
            return sendJson('error','ثبت درخواست در زمان انتخاب شده امکان پذیر نمی باشد.');
        }

        Submit::add($user->id,$user,$request);

        return sendJson('success','درخواست شما با موفقیت ثبت شد', $user->currentSubmit());

    }

    /**
     * جزئیات یک درخواست
     *
     * @group Request
     * @authenticated
     *
     * @urlParam submit integer required شناسه درخواست Example: 331
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "",
     *   "data": {
     *     "id": 331,
     *     "requestDate": {
     *       "day": "1404/08/19",
     *       "range": "9 الی 12"
     *     },
     *     "collectedDate": null,
     *     "weight": 0,
     *     "amount": 0,
     *     "status": {
     *       "value": 5,
     *       "label": "لغو توسط کاربر"
     *     },
     *     "customer": {
     *       "name": "dastan rab",
     *       "mob": "09158992231",
     *       "address": "مشهد، بلوار پیروزی، پیروزی 16، حق شناس 2 - رز 20، حق شناس 2/3",
     *       "location": {
     *         "lat": 36.3107,
     *         "lng": 59.5125
     *       }
     *     },
     *     "driver": null,
     *     "wasteItems": null,
     *     "actionButtons": {
     *       "cancel": false,
     *       "review": false
     *     }
     *   }
     * }
     */

    public function single(Submit $submit)
    {
        $user = auth()->user();
        if($submit->user_id != $user->id){
            return sendJson('error', 'چنین درخواستی وجود ندارد');
        }
        $startDeadline = verta()->instance($submit->start_deadline);
        $endDeadline = verta()->instance($submit->end_deadline);
        $driver = $submit->drivers();
        $wasteItems = null;
        $data = [
            'id' => $submit->id,
            'requestDate' => [
                'day' => $startDeadline->format('Y/m/d'),
                'range' => $startDeadline->format('G').' الی '.$endDeadline->format('G'),
            ],
            'collectedDate' => $driver->exists() ? [
                'day' => verta()->instance($driver->first()->collected_at)->format('Y/m/d'),
                'time' => verta()->instance($driver->first()->collected_at)->format('G:i'),
            ] : null,
            'weight' => $driver->exists() ? $driver->first()->weights : 0,
            'amount' => $submit->total_amount,
            'status' => $submit->status(),
            'customer' => [
                'name' => $submit->user->name.' '.$submit->user->lastname,
                'mob'  => $submit->user->mobile,
                'address' => $submit->address->address,
                'location' => [
                    'lat' => $submit->address->lat,
                    'lng' => $submit->address->lon
                ],
            ],
            'driver' => $driver->exists() ? [
                'name' => $driver->first()->car->user->name.' '.$driver->first()->car->user->lastname,
                'plaque' => [
                    'part1' => $driver->first()->car->plaque_1,
                    'part2' => $driver->first()->car->plaque_2,
                    'part3' => $driver->first()->car->plaque_3,
                    'part4' => $driver->first()->car->plaque_4,
                ],
                'mob' => in_array($submit->status,[1,2]) ? $driver->first()->car->user->mobile : '',
                'avatar' => asset('assets/img/avatar/avatar-driver-3.png'),
            ] : null,
            'wasteItems' => $wasteItems,
            'actionButtons' => [
                'cancel' => $submit->status == 1 ? 'لغو': false,
                'review' => $submit->status == 3 && $submit->star == null ? 'ثبت نظر': false,
            ]
        ];
        if($driver->exists() && $driver->first()->receives()->exists()){
            $receives = $driver->first()->receives()->get();
            foreach ($receives as $receive) {
                $wasteItems[] = [
                    'id'     => $receive->id,
                    'title'  => $receive->title,
                    'weight' => weightFormat($receive->weight),
                    'amount' => tomanFormat($receive->price*$receive->weight),
                    'image'  => asset("assets/img/icons/recyclables/{$receive->fava_id}.png")
                ];
            }
            $data['wasteItems'] = $wasteItems;
        }
        return sendJson('success','', $data);
    }

    /**
     * لیست درخواست‌های کاربر
     *
     * @group Request
     * @authenticated
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "",
     *   "data": {
     *     "list": [
     *       {
     *         "id": 331,
     *         "requestDate": {
     *           "day": "1404/08/19",
     *           "range": "9 الی 12"
     *         },
     *         "collectedDate": null,
     *         "weight": 0,
     *         "amount": 0,
     *         "status": {
     *           "value": 5,
     *           "label": "لغو توسط کاربر"
     *         },
     *         "driver": null
     *       }
     *     ],
     *     "limit": 10
     *   }
     * }
     */

    public function list()
    {
        $user = auth()->user();
        $paginate = 10;
        $submits = $user->submits()->with('drivers')->whereNotIn('status',[1,2])->orderBy('id', 'DESC')->paginate($paginate);
        $data = ['list' => []];
        foreach ($submits as $submit) {
            $startDeadline = verta()->instance($submit->start_deadline);
            $endDeadline = verta()->instance($submit->end_deadline);
            $driver = $submit->drivers();
            $data['list'][] = [
                'id' => $submit->id,
                'requestDate' => [
                    'day' => $startDeadline->format('Y/m/d'),
                    'range' => $startDeadline->format('G').' الی '.$endDeadline->format('G'),
                ],
                'collectedDate' => $driver->exists() ? [
                    'day' => verta()->instance($driver->first()->collected_at)->format('Y/m/d'),
                    'time' => verta()->instance($driver->first()->collected_at)->format('G:i'),
                ] : null,
                'weight' => $driver->exists() ? $driver->first()->weights : 0,
                'amount' => $submit->total_amount,
                'status' => $submit->status(),
                'driver' => $driver->exists() ? [
                    'name' => $driver->first()->car->user->name.' '.$driver->first()->car->user->lastname,
                    'plaque' => in_array($submit->status,[1,2]) ? $driver->first()->car->plaque : '',
                    'mob' => in_array($submit->status,[1,2]) ? $driver->first()->car->user->mobile : '',
                ] : null,
            ];
        }
        $data['limit'] = $paginate;
        return sendJson('success','', $data);
    }

    /**
     * لغو درخواست فعال
     *
     * @group Request
     * @authenticated
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "درخواست شما لغو شد",
     *   "data": null
     * }
     */

    public function cancel()
    {
        $user = auth()->user();
        $submit = $user->submits->where('status',1)->first();
        if(!$submit){
            return sendJson('error', 'شما درخواست قابل لغو ندارید.');
        }
        $suggest=new RequestSuggestionV2(0);
        $suggest->cancelDriversSubmit($submit->id);
        $submit->status = 5;
        $submit->canceled_at = now()->format('Y-m-d H:i:s');
        $submit->save();
        $archive = ReceiveArchive::where('date', Carbon::parse($submit->start_deadline)->format('Y-m-d'))->where('type', 1)->where('city_id',1)->first();
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
        return sendJson('success', 'درخواست شما لغو شد');

    }

    /**
     * ثبت نظر روی درخواست
     *
     * این متد به کاربر اجازه می‌دهد نظر و امتیاز خود را برای یک درخواست ثبت کند.
     *
     * @group Request
     *
     * @bodyParam comment string required متن نظر کاربر Example: خدمات عالی بود
     * @bodyParam rate integer required امتیاز کاربر Example: 5
     *
     * @bodyParam tipValues object required نکات مثبت و منفی
     * @bodyParam tipValues.good array required نکات مثبت Example: ["رفتار عالی","سرعت بالا"]
     * @bodyParam tipValues.bad array required نکات منفی Example: ["تاخیر در جمع آوری"]
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "با تشکر از همکاری شما",
     *   "data": null
     * }
     *
     * @response 200 {
     *   "status": "error",
     *   "message": "لطفا موارد مثبت را مشخص کنید."
     * }
     */
    public function review(Request $request, Submit $submit)
    {
        $user = auth()->user();
        if($user->id != $submit->user->id){
            return sendJson('error','امکان ثبت نظر را ندارید');
        }
        $validator = Validator::make(
            $request->all(),
            [
                'comment' => 'required|string',
                'rate'    => 'required|numeric',
                'tipValues' => 'required|array',
                'tipValues.good' => 'required|array',
                'tipValues.bad'  => 'required|array',
            ],
            [
                'comment.required' => 'لطفا نظر خود را وارد کنید.',
                'rate.required'    => 'لطفا امتیاز را انتخاب کنید.',
                'tipValues.good.required' => 'لطفا موارد مثبت را مشخص کنید.',
                'tipValues.bad.required'  => 'لطفا موارد منفی را مشخص کنید.',
            ]
        );

        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }

        $submit->comment = $request->comment;
        $submit->survey = 1;
        $submit->star = $request->rate;
        if($request->tipValues['good']){
            foreach ($request->tipValues['good'] as $good){
                $submit->surveys()->create(['title' => $good]);
            }
        }
        if($request->tipValues['bad']){
            foreach ($request->tipValues['bad'] as $bad){
                $submit->surveys()->create(['title' => $bad]);
            }
        }
        $submit->save();
        return sendJson('success','با تشکر از همکاری شما');
    }
}
